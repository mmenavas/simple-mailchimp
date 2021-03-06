<?php

/**
 * @file
 * Contains \Drupal\simple_mailchimp\MailchimpService.
 */

namespace Drupal\simple_mailchimp;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Config\ImmutableConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

class MailchimpService implements ContainerInjectionInterface {

  protected $client;
  protected $config;

  public function __construct(ClientInterface $http_client, ImmutableConfig $config) {
    $this->client = $http_client;
    $this->config = $config;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('simple_mailchimp.config')
    );
  }

  public function subscribeEmail($subscriber_email) {
    // Load mailchimp credentials via configuration system.
    $mailchimp_api_key = $this->config->get('api_key');
    $mailchimp_list_id = $this->config->get('list_id');
    $mailchimp_data_center = $this->config->get('data_center');

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
      $response = new Response(400);
    }

    return $response;
  }
}



