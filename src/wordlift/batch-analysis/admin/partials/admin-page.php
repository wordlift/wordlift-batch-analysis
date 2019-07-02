<?php

use Wordlift\Batch_Analysis\Batch_Analysis_Task;
use Wordlift\Batch_Analysis\CleanUp_Task;

wp_enqueue_script( 'batch-analysis-admin-page', plugin_dir_url( dirname( __FILE__ ) ) . '/js/admin-page.js', array( 'wp-util' ), '1.0.0', true );
wp_localize_script( 'batch-analysis-admin-page', 'wlbaBatchAnalysisSettings', array(
	'batchAnalysisAction'            => Batch_Analysis_Task::ID,
	'cleanUpAction'                  => CleanUp_Task::ID,
	'batchAnalysisAction_ajax_nonce' => wp_create_nonce( Batch_Analysis_Task::ID ),
	'cleanUpAction_ajax_nonce'       => wp_create_nonce( CleanUp_Task::ID ),
	'limit'                          => 1,
) );
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Batch Analysis', 'wordlift-batch-analysis' ); ?></h1>

	<div class="wlba-task__progress" style="display:none; border: 1px solid #23282D; height: 20px; margin: 8px 0;">
		<div id="wlba-progress-bar" class="wlba-task__progress__bar"
		     style="width:0; background: #0073AA; text-align: center; height: 100%; color: #fff;"></div>
	</div>

	<form id="wlba-form" method="post" action="" novalidate="novalidate">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Links', 'wordlift-batch-analysis' ); ?></th>
				<td>
					<label>
						<input name="links" type="radio" value="default" checked="checked">
						<?php esc_html_e( 'default', 'wordlift-batch-analysis' ); ?>
					</label>
					<label>
						<input name="links" type="radio" value="yes">
						<?php esc_html_e( 'yes', 'wordlift-batch-analysis' ); ?>
					</label>
					<label>
						<input name="links" type="radio" value="no">
						<?php esc_html_e( 'no', 'wordlift-batch-analysis' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Include annotated posts', 'wordlift-batch-analysis' ); ?></th>
				<td>
					<label>
						<input name="include_annotated" type="radio" value="yes" checked="checked">
						<?php esc_html_e( 'yes', 'wordlift-batch-analysis' ); ?>
					</label>
					<label>
						<input name="include_annotated" type="radio" value="no">
						<?php esc_html_e( 'no', 'wordlift-batch-analysis' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Minimum number of occurrences', 'wordlift-batch-analysis' ); ?></th>
				<td>
					<input name="min_occurrences" type="number" value="1"">
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Post type', 'wordlift-batch-analysis' ); ?></th>
				<td>
					<input name="post_type" type="text" value="post">
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Local only', 'wordlift-batch-analysis' ); ?></th>
				<td>
					<label>
						<input name="local_only" type="radio" value="yes" checked="checked">
						<?php esc_html_e( 'yes', 'wordlift-batch-analysis' ); ?>
					</label>
					<label>
						<input name="local_only" type="radio" value="no">
						<?php esc_html_e( 'no', 'wordlift-batch-analysis' ); ?>
					</label>
				</td>
			</tr>
			</tbody>
		</table>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
		                         value="<?php esc_attr_e( 'Start', 'wordlift-batch-analysis' ); ?>">
			<button id="wlba-cleanup-btn"
			        class="button action"><?php esc_html_e( 'Clean up', 'wordlift-batch-analysis' ); ?></button>
		</p>

	</form>

</div>
