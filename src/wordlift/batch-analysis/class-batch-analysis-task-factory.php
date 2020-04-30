<?php


namespace Wordlift\Batch_Analysis;


class Batch_Analysis_Task_Factory {

	/**
	 * @return Batch_Analysis_Task
	 */
	public static function create() {

		$links             = filter_input( INPUT_POST, 'links' );
		$include_annotated = filter_input( INPUT_POST, 'include_annotated' );
		$min_occurrences   = filter_input( INPUT_POST, 'min_occurrences', FILTER_VALIDATE_INT );
		$post_type         = filter_input( INPUT_POST, 'post_type' );
		$local_only        = filter_input( INPUT_POST, 'local_only' );

		return new Batch_Analysis_Task( $links, 'yes' === $include_annotated, $min_occurrences, $post_type, 'yes' === $local_only );
	}

	/**
	 * @return CleanUp_Task
	 */
	public static function create_cleanup_task() {

		$links             = filter_input( INPUT_POST, 'links' );
		$include_annotated = filter_input( INPUT_POST, 'include_annotated' );
		$min_occurrences   = filter_input( INPUT_POST, 'min_occurrences', FILTER_VALIDATE_INT );
		$post_type         = filter_input( INPUT_POST, 'post_type' );
		$local_only        = filter_input( INPUT_POST, 'local_only' );

		return new CleanUp_Task( $links, 'yes' === $include_annotated, $min_occurrences, $post_type, 'yes' === $local_only );
	}

}
