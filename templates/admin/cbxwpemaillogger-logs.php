<?php
	/**
	 * Provide a dashboard rating log listing
	 *
	 * This file is used to markup the admin-facing rating log listing
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.7
	 *
	 * @package    cbxscratingreview
	 * @subpackage cbxscratingreview/templates
	 */

	if ( ! defined( 'WPINC' ) ) {
		die;
	}
?>

<?php
	$cbxwpemaillogger_logs = new CBXWPEmailLogger_List_Table();

	//Fetch, prepare, sort, and filter log data
	$cbxwpemaillogger_logs->prepare_items();
?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'CBX Email Log Manager', 'cbxwpemaillogger' ); ?>
	</h1>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<!-- main content -->
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<div class="inside">
							<form id="cbxwpemaillogger_logs" method="post">
								<?php $cbxwpemaillogger_logs->views(); ?>

								<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
								<?php $cbxwpemaillogger_logs->search_box( esc_html__( 'Search', 'cbxwpemaillogger' ), 'cbxscratingreviewlogsearch' ); ?>

								<?php $cbxwpemaillogger_logs->display() ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clear clearfix"></div>
	</div>
</div>