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
    $this->mailchimpService = new \Drupal\simple_mailchimp\MailchimpService();
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
