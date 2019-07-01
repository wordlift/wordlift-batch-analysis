<?php

namespace Wordlift\Batch_Analysis\Admin;


class Admin_Page {

	function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	function admin_menu() {

		// Add a page in the WordLift menu.
		add_submenu_page(
			'wl_admin_menu',
			__( 'Batch Analysis', 'wordlift-batch-analysis' ),
			__( 'Batch Analysis', 'wordlift-batch-analysis' ),
			'manage_options',
			plugin_dir_path( __FILE__ ) . 'partials/admin-page.php'
		);

	}

}
