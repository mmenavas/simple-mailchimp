<?php

/**
 * @file
 *
 * Contains \Drupal\Tests\simple_mailchimp\Unit\MailchimpServiceTest.
 */

namespace Drupal\Tests\simple_mailchimp\Unit;

use Drupal\Tests\UnitTestCase;
use GuzzleHttp\Psr7\Response;


/**
 * Mailchimp Service tests
 *
 * @group simple_mailchimp
 */
class MailchimpServiceTest extends UnitTestCase {

  const VALID_EMAIL          = 'valid@email.com';
  const INVALID_EMAIL        = 'nobueno+email';
  const EMAIL_ALREADY_EXISTS = 'already@exists.com';

  public $mailchimpService;
  public $mockConfigMap = [
    ['api_key', 'SIMPLE_MAILCHIMP_TEST_API_KEY'],
    ['list_id', 'SIMPLE_MAILCHIMP_TEST_LIST_ID'],
    ['data_center', 'SIMPLE_MAILCHIMP_TEST_DATA_CENTER']
  ];

  private function getMailchimpRequestMock($email) {

    switch ($email) {
      case ''                         : $status_code = 400; break;
      case self::VALID_EMAIL          : $status_code = 200; break;
      case self::INVALID_EMAIL        : $status_code = 400; break;
      case self::EMAIL_ALREADY_EXISTS : $status_code = 400; break;
    }

    // mock http client response
    $response = new Response($status_code);

    $mailchimp_base_url = 'https://' . $this->config->get('data_center') . '.api.mailchimp.com/3.0/';
    $mailchimp_subscribe_url = $mailchimp_base_url . 'lists/' . $this->config->get('list_id') . '/members';

    return [
      'POST',
      $mailchimp_subscribe_url,
      [
        'auth' => ['apikey', $this->config->get('api_key')],
        'json' => [
          'email_address' => $email,
          'status' => 'subscribed',
        ]
      ],
      $response
    ];
  }

  // Setup mailchimp service and mock objects
  public function setUp() {
    // create mock for config
    $this->config = $this->getMockBuilder('\Drupal\Core\Config\ImmutableConfig')
      ->disableOriginalConstructor()
      ->getMock();
    $this->config->expects($this->any())
      ->method('get')
      ->willReturnMap($this->mockConfigMap);


    // create mock http client
    $this->http_client = $this->getMockBuilder('\GuzzleHttp\ClientInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $this->http_client->expects($this->any())
      ->method('request')
      ->willReturnMap([
        $this->getMailchimpRequestMock(''),
        $this->getMailchimpRequestMock(self::VALID_EMAIL),
        $this->getMailchimpRequestMock(self::INVALID_EMAIL),
        $this->getMailchimpRequestMock(self::EMAIL_ALREADY_EXISTS),
      ]);

    $this->mailchimpService = new \Drupal\simple_mailchimp\MailchimpService($this->http_client, $this->config);
  }

  public function testValidEmailSubscribe() {
    $response = $this->mailchimpService->subscribeEmail(self::VALID_EMAIL);
    $this->assertEquals($response->getStatusCode(), 200);
  }

  public function testInvalidEmailSubscribe() {
    $response = $this->mailchimpService->subscribeEmail(self::INVALID_EMAIL);
    $this->assertEquals($response->getStatusCode(), 400);
  }

  public function testEmptyEmailString() {
    $response = $this->mailchimpService->subscribeEmail('');
    $this->assertEquals($response->getStatusCode(), 400);
  }

  public function testEmailAlreadyExists() {
    $response = $this->mailchimpService->subscribeEmail(self::EMAIL_ALREADY_EXISTS);
    $this->assertEquals($response->getStatusCode(), 400);
  }
}
