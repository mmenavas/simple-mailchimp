<?php

/**
 * @file
 * Contains \Drupal\simple_mailchimp\Plugin\Block\MailchimpSubscribeBlock.
 */

namespace Drupal\simple_mailchimp\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'MailchimpSubscribeBlock' block.
 *
 * @Block(
 *  id = "mailchimp_subscribe_block",
 *  admin_label = @Translation("Mailchimp Subscribe Block"),
 * )
 */
class MailchimpSubscribeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['heading'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Heading'),
      '#default_value' => isset($this->configuration['heading']) ? $this->configuration['heading'] : '',
      '#maxlength' => 255,
    );
    $form['subheading'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Subheading'),
      '#default_value' => isset($this->configuration['subheading']) ? $this->configuration['subheading'] : '',
      '#maxlength' => 255,
    );
    $form['email_placeholder'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Email Placeholder'),
      '#default_value' => isset($this->configuration['email_placeholder']) ? $this->configuration['email_placeholder'] : 'example@example.com',
      '#maxlength' => 255,
    );
    $form['button_label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Button Label'),
      '#default_value' => isset($this->configuration['button_label']) ? $this->configuration['button_label'] : $this->t('Subscribe'),
      '#maxlength' => 255,
    );

    return $form;
  }



  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['heading'] = $form_state->getValue('heading');
    $this->configuration['subheading'] = $form_state->getValue('subheading');
    $this->configuration['email_placeholder'] = $form_state->getValue('email_placeholder');
    $this->configuration['button_label'] = $form_state->getValue('button_label');
  }
  
  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('Drupal\simple_mailchimp\Form\MailchimpSubscribeForm');

    $form['email']['#placeholder'] = $this->configuration['email_placeholder'];
    $form['actions']['submit']['#value'] =  $this->configuration['button_label'];

    return $form;
  }
}
