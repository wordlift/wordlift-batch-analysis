<?php


namespace Wordlift\Batch_Analysis;


use Wordlift_Configuration_Service;

class Batch_Analysis_Task extends Abstract_Task {

	const ID = '_wlba_do_batch_analysis';

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
