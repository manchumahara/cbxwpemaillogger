<?php
	/**
	 * Provide a dashboard rating log listing
	 *
	 * This file is used to markup the admin-facing rating log listing
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    cbxwpemaillogger
	 * @subpackage cbxwpemaillogger/templates
	 */

	if ( ! defined( 'WPINC' ) ) {
		die;
	}
?>
<?php
	$id = intval( $item['id'] );
?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php echo sprintf( esc_html__( 'CBX Email Log Manager: Email ID: %d', 'cbxwpemaillogger' ), $id ); ?>
	</h1>
	<p>
		<a class="button button-primary cbxwpemaillogger_logs_btn" href="<?php echo esc_url(admin_url( 'wp-admin/admin.php?page=cbxwpemaillogger' )); ?>"><?php esc_html_e( 'Back to Log Listing', 'cbxwpemaillogger' ); ?></a>
		<a class="button cbxwpemaillogger_logs_btn" href="<?php echo esc_url(admin_url( 'admin.php?page=cbxwpemailloggersettings#cbxwpemaillogger_log' )); ?>"><?php esc_html_e( 'Log Setting', 'cbxwpemaillogger' ); ?></a>
		<a class="button  cbxwpemaillogger_logs_btn" href="<?php echo esc_url(admin_url( 'admin.php?page=cbxwpemailloggersettings#cbxwpemaillogger_email' )); ?>"><?php esc_html_e( 'SMTP Setting', 'cbxwpemaillogger' ); ?></a>
	</p>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<!-- main content -->
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<div class="inside">
							<?php

								$email_data = maybe_unserialize( $item['email_data'] );
							?>
							<?php do_action( 'cbxwpemaillogger_single_log_display_before', $item ); ?>

							<table class="widefat">
								<thead>
								<tr>
									<th class="row-title"><?php esc_attr_e( 'Date', 'cbxwpemaillogger' ); ?></th>
									<th>
										<?php
											$date_created = '';
											if ( $item['date_created'] != '' ) {
												$date_created = CBXWPEmailLoggerHelper::DateReadableFormat( stripslashes( $item['date_created'] ), 'M j, Y g:i a' );
											}

											echo $date_created;
										?>
									</th>
								</tr>
								</thead>
								<tbody>
								<?php do_action( 'cbxwpemaillogger_single_log_display_start', $item ); ?>
								<tr>
									<td class="row-title"><label for="tablecell"><?php esc_attr_e( 'To', 'cbxwpemaillogger' ); ?></label></td>
									<td>
										<?php
											$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
											$emails      = isset( $headers_arr['email_to'] ) ? $headers_arr['email_to'] : array();

											$email_to = '';

											if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
												$formatted_emails = array();
												foreach ( $emails as $email ) {
													if ( $email['recipient_name'] != '' ) {
														$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email($email['address']) . ')';
													} else {
														$formatted_emails[] = sanitize_email($email['address']);
													}
												}

												$email_to = implode( ',', $formatted_emails );
											}

											echo $email_to;
										?>
									</td>
								</tr>
								<tr class="alternate">
									<td class="row-title"><label for="tablecell"><?php esc_attr_e( 'Subject', 'cbxwpemaillogger' ); ?></label></td>
									<td>
										<?php
											echo esc_attr(wp_unslash( $item['subject'] ));

										?>
									</td>
								</tr>
								<tr>
									<td class="row-title"><label for="tablecell"><?php esc_attr_e( 'From', 'cbxwpemaillogger' ); ?></label></td>
									<td>
										<?php

											$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
											$email_from  = isset( $headers_arr['email_from'] ) ? $headers_arr['email_from'] : array();

											echo $email_from['from_name'] . '(' . sanitize_email($email_from['from_email']) . ')';
										?>
									</td>
								</tr>
								<tr class="alternate">
									<td class="row-title"><?php esc_attr_e( 'ReplyTo', 'cbxwpemaillogger' ); ?></td>
									<td>
										<?php
											$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
											$emails      = isset( $headers_arr['email_reply_to'] ) ? $headers_arr['email_reply_to'] : array();

											$email_reply_to = '';

											if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
												$formatted_emails = array();
												foreach ( $emails as $email ) {
													if ( $email['recipient_name'] != '' ) {
														$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email($email['address'] ). ')';
													} else {
														$formatted_emails[] = sanitize_email($email['address']);
													}
												}

												$email_reply_to = implode( ',', $formatted_emails );
											}

											echo $email_reply_to;
										?>
									</td>
								</tr>
								<tr class="">
									<td class="row-title"><?php esc_attr_e( 'CC', 'cbxwpemaillogger' ); ?></td>
									<td>
										<?php
											$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
											$emails      = isset( $headers_arr['email_cc'] ) ? $headers_arr['email_cc'] : array();

											$email_cc = '';

											if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
												$formatted_emails = array();
												foreach ( $emails as $email ) {
													if ( $email['recipient_name'] != '' ) {
														$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email($email['address']) . ')';
													} else {
														$formatted_emails[] = sanitize_email($email['address']);
													}
												}

												$email_cc = implode( ',', $formatted_emails );
											}

											echo $email_cc;
										?>
									</td>
								</tr>
								<tr class="alternate">
									<td class="row-title"><?php esc_attr_e( 'BCC', 'cbxwpemaillogger' ); ?></td>
									<td>
										<?php
											$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
											$emails      = isset( $headers_arr['email_bcc'] ) ? $headers_arr['email_bcc'] : array();


											$email_bcc = '';

											if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
												$formatted_emails = array();
												foreach ( $emails as $email ) {
													if ( $email['recipient_name'] != '' ) {
														$formatted_emails[] = $email['recipient_name'] . '(' . $email['address'] . ')';
													} else {
														$formatted_emails[] = sanitize_email($email['address']);
													}
												}

												$email_bcc = implode( ',', $formatted_emails );
											}

											echo $email_bcc;
										?>
									</td>
								</tr>
								<tr class="">
									<td class="row-title"><?php esc_attr_e( 'Email Body', 'cbxwpemaillogger' ); ?></td>
									<td>
										<div class="cbxwpemaillogger_body">
											<?php

												$body_url = add_query_arg( array(
													'action'   => 'cbxwpemaillogger_log_body',
													'id'       => $id,
													'_wpnonce' => wp_create_nonce( 'cbxwpemaillogger' )
												), site_url() );


											?>
											<iframe src="<?php echo esc_url( $body_url ); ?>"></iframe>
										</div>
									</td>
								</tr>
								<tr class="alternate">
									<td class="row-title"><?php esc_attr_e( 'Attachments', 'cbxwpemaillogger' ); ?></td>
									<td>
										<?php
											$attachments = isset( $email_data['attachments'] ) ? $email_data['attachments'] : array();

											if ( is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
												echo implode( '<br/>', $attachments );
											} else {
												echo esc_html__( 'N/A', 'cbxwpemaillogger' );
											}

										?>
									</td>
								</tr>
								<tr class="">
									<td class="row-title"><?php esc_attr_e( 'IP Address', 'cbxwpemaillogger' ); ?></td>
									<td>
										<?php
											$ip_address = isset( $item['ip_address'] ) ? $item['ip_address'] : '';

											echo esc_attr( $ip_address );
										?>
									</td>
								</tr>
								<tr class="alternate">
									<td class="row-title"><?php esc_attr_e( 'Status', 'cbxwpemaillogger' ); ?></td>
									<td>
										<?php
											$status = isset( $item['status'] ) ? intval( $item['status'] ) : 0;

											echo ( $status ) ? esc_html__( 'Sent', 'cbxwpemaillogger' ) : esc_html__( 'Failed', 'cbxwpemaillogger' );
										?>
									</td>
								</tr>
								<?php if($status == 0):	?>
									<tr class="" >
										<td class="row-title" style="color: red;"><?php esc_attr_e( 'Error Message', 'cbxwpemaillogger' ); ?></td>
										<td style="color: red;">
											<?php
												$error_message = isset( $item['error_message'] ) ? sanitize_text_field( wp_unslash($item['error_message']) ) : esc_html__('Error not traced', 'cbxwpemaillogger');

												echo $error_message;
											?>
										</td>
									</tr>
								<?php endif; ?>

								<?php do_action( 'cbxwpemaillogger_single_log_display_end', $item ); ?>
								</tbody>
							</table>
							<?php do_action( 'cbxwpemaillogger_single_log_display_after', $item ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clear clearfix"></div>
	</div>
</div>