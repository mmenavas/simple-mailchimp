<?php

/**
 * @file
 *
 * Contains \Drupal\Tests\simple_mailchimp\Unit\MailchimpServiceTest.
 */

namespace Drupal\Tests\simple_mailchimp\Unit;

use Drupal\Tests\UnitTestCase;


/**
 * Mailchimp Service tests
 *
 * @group simple_mailchimp
 */
class MailchimpServiceTest extends UnitTestCase {

  public $mailchimpService;

  // Setup mailchimp service
  public function setUp() {

    // create mock config
    $this->config = $this->getMockBuilder('\Drupal\Core\Config\ConfigFactory')
      ->disableOriginalConstructor()
      ->getMock();

    $this->config->expects($this->any())
      ->method('get')
      ->with('key.default_config')
      ->willReturn($this->config); 
    // create mock http client

    $this->http_client = $this->getMockBuilder('\GuzzleHttp\ClientInterface')
      ->disableOriginalConstructor()
      ->getMock();

    $this->mailchimpService = new \Drupal\simple_mailchimp\MailchimpService($this->config, $this->http_client);
  }

  public function testValidEmailSubscribe() {
    $this->assertEquals(100, 100);
  }

  public function testInvalidEmailSubscribe() {
    $this->assertEquals(true, false);
  }

  public function testEmailAlreadyExists() {
    $this->assertEquals(true, false);
  }
}
