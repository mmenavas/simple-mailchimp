<?php

/**
 * @file
 * Contains \Drupal\simple_mailchimp\Form\MailchimpBulkSubscribeForm.
 */

namespace Drupal\simple_mailchimp\Form;

use Drupal\simple_mailchimp\MailchimpService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MailchimpBulkSubscribeForm.
 *
 * @package Drupal\simple_mailchimp\Form
 */
class MailchimpBulkSubscribeForm extends FormBase {

  /**
   * @var \Drupal\simple_mailchimp\MailchimpService
   */
  protected $mailchimpService;

  /**
   * {@inheritdoc}
   */
  public function __construct(MailchimpService $mailchimpService) {
    $this->mailchimpService = $mailchimpService;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('simple_mailchimp.mailchimp_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mailchimp_bulk_subscribe_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['emails'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Emails'),
      '#default_value' => '',
      '#description' => $this->t('Enter one email per line.')
    );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Subscribe all emails')
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $subscriber_emails = preg_split('/[ \n]/', strtolower(trim($form['emails']['#value'])));

    foreach ($subscriber_emails as $email) {
      $email = trim($email);

      $response = $this->mailchimpService->subscribeEmail($email);
      $api_config = \Drupal::config('simple_mailchimp.mailchimp');

      // Load mailchimp form-submission messages
      $on_success = $api_config->get('success_msg');
      $on_already_subscribed = $api_config->get('already_subscribed_msg');
      $on_system_failure = $api_config->get('system_failure_msg');

      if ($response->getStatusCode() == 200) {
        drupal_set_message($this->t('@email: @message', array('@email' => $email, '@message' => $on_success)), 'status');
      }
      else if ($response->getStatusCode() == 400) {
        drupal_set_message($this->t('@email: @message', array('@email' => $email, '@message' => $on_already_subscribed)), 'warning');
      }
      else {
        $form_state->setRedirect('contact.site_page');
        drupal_set_message($this->t('@email: @message', array('@email' => $email, '@message' => $on_system_failure)), 'error');
      }
    }
  }

}
