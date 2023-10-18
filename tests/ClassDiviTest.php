<?php

namespace WPSL\Divi;

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;
use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use wpCloud\StatelessMedia\WPStatelessStub;

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
    Functions\when('wp_upload_dir')->justReturn( self::TEST_UPLOAD_DIR );
        
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

    // $divi = new Divi();
    // $divi->module_init([]);
  }

  public function testShouldSkipCacheBusting() {
    // $divi = new Divi();

    // $this->assertEquals('https://test.test/test/test.test', $divi->maybe_skip_cache_busting(null, 'https://test.test/test/test.test'));
  }
}

function wp_doing_ajax() {
  return true;
}
