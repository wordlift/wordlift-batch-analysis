<?php


namespace Wordlift\Batch_Analysis;


use Wordlift\Task\Task;
use Wordlift_Configuration_Service;

class CleanUp_Task extends Abstract_Task {

	const ID = '_wlba_clean_up';

	/**
	 * Process the provided item.
	 *
	 * @param mixed $item Process the provided item.
	 *
	 * @since 1.0.0
	 *
	 */
	function process_item( $item ) {

		$post = get_post( $item );

		$cleaned_up = preg_replace( \Wordlift_Content_Filter_Service::PATTERN, '$4', $post->post_content );

		wp_update_post( array(
			'ID'           => $post->ID,
			'post_content' => $cleaned_up,
		) );

	}

}
