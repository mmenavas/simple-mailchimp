<?php

/**
 * @file
 * Contains \Drupal\simple_mailchimp\MailchimpService.
 */

namespace Drupal\simple_mailchimp;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;

class MailchimpService implements ContainerInjectionInterface {

  protected $client;
  protected $serviceConfig;

  public function __construct(ClientInterface $http_client) {
    $this->client = $http_client;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }

  public function subscribeEmail($email) {
    // Load mailchimp credentials via configuration system.
    $api_config = \Drupal::config('simple_mailchimp.mailchimp');
    $mailchimp_api_key = $api_config->get('api_key');
    $mailchimp_list_id = $api_config->get('list_id');
    $mailchimp_data_center = $api_config->get('data_center');

    $mailchimp_base_url = 'https://' . $mailchimp_data_center . '.api.mailchimp.com/3.0/';
    $mailchimp_subscribe_url = $mailchimp_base_url . 'lists/' . $mailchimp_list_id . '/members';

    try {
      $response = $this->client->request('POST', $mailchimp_subscribe_url, [
        'auth' => ['apikey', $mailchimp_api_key],
        'json' => [
          'email_address' => $subscriber_email,
          'status' => 'subscribed',
        ]
      ]);
    }
    catch(\Exception $e) {

    }
    return $response;
  }
}
