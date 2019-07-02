<?php


namespace Wordlift\Batch_Analysis;

use Wordlift\Task\Task;
use Wordlift_Configuration_Service;

abstract class Abstract_Task implements Task {

	protected $links;
	protected $include_annotated;
	protected $min_occurrences;
	protected $post_type;
	protected $local_only;

	/**
	 * Batch_Analysis_Task constructor.
	 *
	 * @param $links
	 * @param $include_annotated
	 * @param $min_occurrences
	 * @param $post_type
	 * @param $local_only
	 */
	public function __construct( $links, $include_annotated, $min_occurrences, $post_type, $local_only ) {

		$this->links             = $links;
		$this->include_annotated = $include_annotated;
		$this->min_occurrences   = $min_occurrences;
		$this->post_type         = $post_type;
		$this->local_only        = $local_only;

	}

	/**
	 * Define the task ID.
	 *
	 * @return string The task id.
	 * @since 1.0.0
	 */
	function get_id() {
		return static::ID;
	}

	/**
	 * List the items to process.
	 *
	 * @param int $limit The maximum number of items to process, default 0, i.e. no limit.
	 * @param int $offset The starting offset, default 0.
	 *
	 * @return array An array of items.
	 * @since 1.0.0
	 */
	function list_items( $limit = PHP_INT_MAX, $offset = 0 ) {
		global $wpdb;

		return $wpdb->get_col( Batch_Analysis_Sql_Helper::get_sql( 'p.ID', array(
			'post_type'         => $this->post_type,
			'include_annotated' => $this->include_annotated,
			'from'              => null,
			'to'                => null,
			'exclude'           => null,
		), $limit, $offset ) );

//		return get_posts( array(
//			'numberposts'            => $limit,
//			'offset'                 => $offset,
//			'post_type'              => $this->post_type,
//			'orderby'                => 'ID',
//			'order'                  => 'ASC',
//			'cache_results'          => false,
//			'update_post_meta_cache' => false,
//			'update_post_term_cache' => false,
//			'fields'                 => 'ids',
//		) );
	}

	/**
	 * Count the total number of items to process.
	 *
	 * @return int Total number of items to process.
	 * @since 1.0.0
	 */
	function count_items() {
		global $wpdb;

		return $wpdb->get_var( Batch_Analysis_Sql_Helper::get_sql( 'count( 1 )', array(
			'post_type'         => $this->post_type,
			'include_annotated' => $this->include_annotated,
			'from'              => null,
			'to'                => null,
			'exclude'           => null,
		) ) );
	}

	/**
	 * Process the provided item.
	 *
	 * @param mixed $item Process the provided item.
	 *
	 * @since 1.0.0
	 *
	 */
	abstract function process_item( $item );

}
