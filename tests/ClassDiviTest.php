<?php

namespace WPSL\Divi;

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

    // WP mocks
    // Functions\when('wp_upload_dir')->justReturn( self::TEST_UPLOAD_DIR );
    Functions\when('wp_doing_ajax')->justReturn( true );
        
    // WP_Stateless mocks
    // Filters\expectApplied('wp_stateless_file_name')
    //   ->andReturn( self::TEST_FILE );

    // Filters\expectApplied('wp_stateless_handle_root_dir')
    //   ->andReturn( 'uploads' );

    Functions\when('ud_get_stateless_media')->justReturn( WPStatelessStub::instance() );
  }

  public function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

  public function testShouldInitModule() {
    // self::$functions->expects($this->exactly(1))
    //   ->method('add_filter')
    //   ->with('stateless_skip_cache_busting');

    // $_POST['action'] = 'et_core_portability_export';

    // self::$functions->expects($this->exactly(1))
    //   ->method('remove_filter')
    //   ->with('sanitize_file_name');

    $divi = new Divi();

    add_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]);

    $divi->module_init([]);

    self::assertNotFalse( has_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]) );
    self::assertNotFalse( has_filter('stateless_skip_cache_busting', [ $divi, 'maybe_skip_cache_busting' ]) );
  }

  public function testShouldInitModuleAjax() {
    $divi = new Divi();

    $_POST['action'] = 'et_core_portability_export';

    add_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]);

    $divi->module_init([]);
    
    self::assertFalse( has_filter('sanitize_file_name', [ 'wpCloud\StatelessMedia\Utility', 'randomize_filename' ]) );
    self::assertNotFalse( has_filter('stateless_skip_cache_busting', [ $divi, 'maybe_skip_cache_busting' ]) );
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
