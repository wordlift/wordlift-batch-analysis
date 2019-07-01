<?php


namespace Wordlift\Batch_Analysis;


use Wordlift\Task\Task;
use Wordlift_Configuration_Service;

class Batch_Analysis_Task implements Task {

	const ID = '_wlba_do_batch_analysis';

	private $links;
	private $include_annotated;
	private $min_occurrences;
	private $post_type;
	private $local_only;

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
		return self::ID;
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

//		return count( get_posts( array(
//			'numberposts'            => - 1,
//			'post_type'              => $this->post_type,
//			'orderby'                => 'ID',
//			'order'                  => 'ASC',
//			'cache_results'          => false,
//			'update_post_meta_cache' => false,
//			'update_post_term_cache' => false,
//			'fields'                 => 'ids',
//		) ) );
	}

	/**
	 * Process the provided item.
	 *
	 * @param mixed $item Process the provided item.
	 *
	 * @since 1.0.0
	 *
	 */
	function process_item( $item ) {

		$config        = Wordlift_Configuration_Service::get_instance();
		$dataset_uri   = $config->get_dataset_uri();
		$language_code = $config->get_language_code();

		preg_match( "|^http://.*/(.+?)$|", $dataset_uri, $matches );
		$analyzer = $matches[1];

		$post = get_post( $item );

		$params = array(
			"content"            => $post->post_content,
			"redlinkAnalyzer"    => $analyzer,
			"contentType"        => "html",
			"minimumConfidence"  => 0.85,
			"minimumOccurrences" => $this->min_occurrences,
			"summary"            => false,
			"thumbnail"          => false,
			"excludes"           => array(),
			"datasetUri"         => $dataset_uri,
			"localOnly"          => $this->local_only,
			"languageCode"       => $language_code,
			"links"              => $this->links,
		);

		$response = wp_remote_post( "https://api.wordlift.io/analysis/redlink/analysis", array(
			'timeout' => 60,
			'headers' => array(
				'Accept'        => 'text/html',
				'Content-type'  => 'application/json;charset=UTF-8',
				'Authorization' => 'Key ' . $config->get_key(),
				'Expect'        => '',
			),
			'body'    => wp_json_encode( $params ),
		) );

		// Bail out in case of error.
		if ( is_wp_error( $response ) || 2 !== (int) wp_remote_retrieve_response_code( $response ) / 100 ) {
			return;
		}

		// Update the post content.
		$body = wp_remote_retrieve_body( $response );

		// Bail out if the body is empty.
		if ( empty( $body ) ) {
			return;
		}

		wp_update_post( array(
			'ID'           => $post->ID,
			'post_content' => wp_remote_retrieve_body( $response ),
		) );

	}

}
//
//function preempt_expect_header( $r ) {
//	$r['headers']['Expect'] = '';
//
//	return $r;
//}
//
//add_filter( 'http_request_args', 'preempt_expect_header' );
