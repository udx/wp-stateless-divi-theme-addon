<?php

use PHPUnit\Framework\TestCase;
use wpCloud\StatelessMedia\WPStatelessStub;
use WPSL\Divi\Divi;

/**
 * Class ClassDiviTest
 */
class ClassDiviTest extends TestCase {

  public static $functions;

  public function setUp(): void {
    self::$functions = $this->createPartialMock(
      ClassDiviTest::class,
      ['add_filter', 'add_action', 'apply_filters', 'do_action', 'remove_filter']
    );

    $this::$functions->method('apply_filters')->will($this->returnArgument(1));
  }

  public function testShouldInitModule() {
    self::$functions->expects($this->exactly(1))
      ->method('add_filter')
      ->with('stateless_skip_cache_busting');

    $_POST['action'] = 'et_core_portability_export';

    self::$functions->expects($this->exactly(1))
      ->method('remove_filter')
      ->with('sanitize_file_name');

    $divi = new Divi();
    $divi->module_init([]);
  }

  public function testShouldSkipCacheBusting() {
    $divi = new Divi();

    $this->assertEquals('https://test.test/test/test.test', $divi->maybe_skip_cache_busting(null, 'https://test.test/test/test.test'));
  }

  public function add_filter() {
  }

  public function add_action() {
  }

  public function apply_filters($a, $b) {
  }

  public function do_action($a, ...$b) {
  }

  public function remove_filter($a, ...$b) {
  }

  public function debug_backtrace($a, $b) {
  }

}

function add_filter($a, $b, $c = 10, $d = 1) {
  return ClassDiviTest::$functions->add_filter($a, $b, $c, $d);
}

function add_action($a, $b, $c = 10, $d = 1) {
  return ClassDiviTest::$functions->add_action($a, $b, $c, $d);
}

function apply_filters($a, $b) {
  return ClassDiviTest::$functions->apply_filters($a, $b);
}

function do_action($a, ...$b) {
  return ClassDiviTest::$functions->do_action($a, ...$b);
}

function remove_filter($a, $b) {
  return ClassDiviTest::$functions->remove_filter($a, $b);
}

function wp_doing_ajax() {
  return true;
}

function wp_get_upload_dir() {
  return [
    'baseurl' => 'https://test.test/uploads',
    'basedir' => '/var/www/uploads'
  ];
}

function ud_get_stateless_media() {
  return WPStatelessStub::instance();
}
