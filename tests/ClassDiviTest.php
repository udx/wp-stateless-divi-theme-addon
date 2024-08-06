<?php

namespace SLCA\Divi;

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;
use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use wpCloud\StatelessMedia\WPStatelessStub;
use wpCloud\StatelessMedia\Utility;

/**
 * Class ClassDiviTest
 */
class ClassDiviTest extends TestCase {

  // Adds Mockery expectations to the PHPUnit assertions count.
  use MockeryPHPUnitIntegration;

  public static $functions;

  public function setUp(): void {
		parent::setUp();
		Monkey\setUp();

    Functions\when('ud_get_stateless_media')->justReturn( WPStatelessStub::instance() );
  }

  public function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

  public function testShouldInitModule() {
    $divi = new Divi();

    $divi->module_init([]);

    self::assertNotFalse( has_action('admin_init', [ $divi, 'admin_init' ]) );
    self::assertNotFalse( has_action('wp_ajax_et_core_portability_export', [ $divi, 'portability_ajax_export' ]) );
    self::assertNotFalse( has_filter('stateless_skip_cache_busting', [ $divi, 'maybe_skip_cache_busting' ]) );
  }

  public function testShouldRandomizeFileNameOnGetRequest() {
    $divi = new Divi();

    add_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]);

    $divi->admin_init();

    self::assertNotFalse( has_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]) );
  }

  public function testShouldNotRandomizeFileNameOnGetRequest() {
    $divi = new Divi();

    add_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]);

    $_GET['et_core_portability'] = true;
    $_GET['nonce'] = 'test';

    Functions\when('wp_unslash')->returnArg();
    Functions\when('sanitize_text_field')->returnArg();
    Functions\when('wp_verify_nonce')->justReturn( true );

    $divi->admin_init();

    self::assertFalse( has_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]) );
  }

  public function testShouldNotRandomizeFileNameOnPostRequest() {
    $divi = new Divi();

    add_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]);

    $divi->portability_ajax_export();

    self::assertFalse( has_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]) );
  }

  public function testShouldSkipCacheBusting() {
    $divi = new Divi();

    self::assertEquals(
      'https://test.test/test/test.test', 
      $divi->maybe_skip_cache_busting(null, 'https://test.test/test/test.test')
    );
  }

  public function testShouldNotSkipCacheBusting() {
    $divi = new Divi();

    Utility::setCallStackMatches(false);

    self::assertEquals(
      null, 
      $divi->maybe_skip_cache_busting(null, 'https://test.test/test/test.test')
    );
  }
}
