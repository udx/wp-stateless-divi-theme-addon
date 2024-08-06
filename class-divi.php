<?php

namespace SLCA\Divi;

use wpCloud\StatelessMedia\Compatibility;
use wpCloud\StatelessMedia\Utility;

/**
 * Class Divi
 */
class Divi extends Compatibility {
  protected $id = 'divi';
  protected $title = 'Divi';
  protected $constant = 'WP_STATELESS_COMPATIBILITY_DIVI';
  protected $description = 'Ensures compatibility with Divi theme.';
  protected $theme_name = 'Divi';

  /**
   * Cache Busting call stack conditions to disable.
   * Fixing the issue with multiple cache files being created on each page load.
   * @see https://github.com/wpCloud/wp-stateless/issues/430
   * @var array
   */
  private $cache_busting_disable_conditions = array(
    array('stack_level' => 7, 'function' => '__construct', 'class' => 'ET_Core_PageResource'),
    array('stack_level' => 7, 'function' => 'get_cache_filename', 'class' => 'ET_Builder_Element')
  );

  /**
   * Initialize compatibility module
   * @param $sm
   */
  public function module_init($sm) {
    // exclude randomize_filename from export
    add_action('admin_init', array($this, 'admin_init'), 5);
    add_action('wp_ajax_et_core_portability_export', array($this, 'portability_ajax_export'), 5);

    // maybe skip cache busting
    add_filter('stateless_skip_cache_busting', array($this, 'maybe_skip_cache_busting'), 10, 2);
  }

  /**
   * Disable Cache Busting when exporting Divi Options
   */
  public function admin_init() {
    if ( empty($_GET['et_core_portability']) ) {
      return;
    }

    if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['nonce'] ) ) , 'et_core_portability_export' ) ) {
      return;
    }

    remove_filter('sanitize_file_name', array("wpCloud\StatelessMedia\Utility", 'randomize_filename'), 10);
  }

  /**
   * Disable Cache Busting when exporting Divi Options
   */
  public function portability_ajax_export() {
    remove_filter('sanitize_file_name', array("wpCloud\StatelessMedia\Utility", 'randomize_filename'), 10);
  }

  /**
   * Maybe skip cache busting
   * @param $null
   * @param $filename
   * @return bool | string
   */
  public function maybe_skip_cache_busting($null, $filename) {
    $callstack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8);
    if (Utility::isCallStackMatches($callstack, $this->cache_busting_disable_conditions)) return $filename;
    return $null;
  }
}
