<?php
/**
 * Plugin Name:     WordLift Batch Analysis
 * Plugin URI:      https://wordlift.io
 * Description:     WordLift Batch Analysis.
 * Author:          David Riccitelli <david@wordlift.io>
 * Author URI:      https://wodlift.io
 * Text Domain:     wordlift-batch-analysis
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wordlift\Batch_Analysis
 */

spl_autoload_register( function ( $class_name ) {

	// Bail out if these are not our classes.
	if ( 0 !== strpos( $class_name, 'Wordlift\\Batch_Analysis' )
	     && 0 !== strpos( $class_name, 'Wordlift\\Task' ) ) {
		return false;
	}

	$class_name_lc = strtolower( str_replace( '_', '-', $class_name ) );

	preg_match( '|^(?:(.*)\\\\)?(.+?)$|', $class_name_lc, $matches );

	$path = 'src/' . str_replace( '\\', DIRECTORY_SEPARATOR, $matches[1] );
	$file = 'class-' . $matches[2] . '.php';

	$full_path = plugin_dir_path( __FILE__ ) . $path . DIRECTORY_SEPARATOR . $file;

	if ( ! file_exists( $full_path ) ) {
		echo( "Class $class_name not found at $full_path.");
		return false;
	}

	require_once $full_path;

	return true;
} );

// Imports.
use Wordlift\Batch_Analysis\Admin\Admin_Page;
use Wordlift\Batch_Analysis\Batch_Analysis_Task_Factory;
use Wordlift\Task\Ajax_Adapter;

add_action( 'init', function () {

	// Bail out if WordLift isn't installed or if we're not in the admin area.
	if ( ! class_exists( 'Wordlift' ) || ! is_admin() ) {
		return;
	}

	new Admin_Page();
	new Ajax_Adapter( Batch_Analysis_Task_Factory::create() );

} );
