<?php

/**
 * @file
 * Contains \Drupal\simple_mailchimp\Form\MailchimpSubscribeForm.
 */

namespace Drupal\simple_mailchimp\Form;

use Drupal\simple_mailchimp\MailchimpService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MailchimpSubscribeForm.
 *
 * @package Drupal\simple_mailchimp\Form
 */
class MailchimpSubscribeForm extends FormBase {
  
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

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mailchimp_subscribe_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['email'] = array(
      '#type' => 'email',
      '#size' => '22',
      '#required' => TRUE,
      '#attributes' => array(
        'class' => array('simple-mailchimp--email-field')
      )
    );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#attributes' => array(
        'class' => array('simple-mailchimp--submit-button')
      )
    );

    return $form;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('simple_mailchimp.mailchimp_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $subscriber_email = strtolower(trim($form['email']['#value']));
    $response = $this->mailchimpService->subscribeEmail($subscriber_email);

    $api_config = \Drupal::config('simple_mailchimp.mailchimp');

    // Load mailchimp form-submission messages
    $on_success = $api_config->get('success_msg');
    $on_already_subscribed = $api_config->get('already_subscribed_msg');
    $on_system_failure = $api_config->get('system_failure_msg');

    if ($response->getStatusCode() == 200) {
      drupal_set_message($this->t('@message', array('@message' => $on_success)), 'status');
    }
    else if ($response->getStatusCode() == 400) {
      drupal_set_message($this->t('@message', array('@message' => $on_already_subscribed)), 'warning');
    }
    else {
      $form_state->setRedirect('contact.site_page');
      drupal_set_message($this->t('@message', array('@message' => $on_system_failure)), 'error');
    }

  }

}
