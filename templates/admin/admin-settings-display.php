<?php
	/**
	 * Provide a settings view for the plugin
	 *
	 * This file is used to markup the public-facing aspects of the plugin.
	 *
	 * @link       http://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    Cbxform
	 * @subpackage Cbxform/admin/templates
	 */
	if ( ! defined( 'WPINC' ) ) {
		die;
	}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2><?php esc_html_e( 'CBX Email Log Manager : Global Setting', 'cbxwpemaillogger' ); ?></h2>
	<p>
		<a class="button button-primary cbxwpemaillogger_logs_btn" href="<?php echo esc_url(admin_url( 'admin.php?page=cbxwpemaillogger' )); ?>"><?php esc_html_e( 'Back to Log Listing', 'cbxwpemaillogger' ); ?></a>

	</p>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<!-- main content -->
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<div class="inside">
							<?php
								$this->settings_api->show_navigation();
								$this->settings_api->show_forms();
							?>
						</div>
					</div>
				</div>
			</div>
			<?php
				include( cbxwpemaillogger_locate_template( 'admin/sidebar.php' ) );
			?>
		</div>
		<div class="clear"></div>
	</div>
</div>