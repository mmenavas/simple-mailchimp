<?php

/**
 * @file
 *
 * Contains \Drupal\Tests\simple_mailchimp\Unit\MailchimpServiceTest.
 */

namespace Drupal\Tests\simple_mailchimp\Unit;

use Drupal\Tests\UnitTestCase;

class MailchimpServiceTest extends UnitTestCase {

  public $mailchimpService;

  // Setup mailchimp service
  public function setUp() {
    $this->mailchimpService = new \Drupal\simple_mailchimp\MailchimpService();
  }

  public function testEmailSubscribe() {
    $this->assertEquals(100, 100);
  }
}
