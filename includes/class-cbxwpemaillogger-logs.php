<?php
	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}


	class CBXWPEmailLogger_List_Table extends WP_List_Table {

		/**
		 * The current list of all branches.
		 *
		 * @since  3.1.0
		 * @access public
		 * @var array
		 */
		function __construct() {

			//Set parent defaults
			parent::__construct( array(
				'singular' => 'cbxwpemaillogger',     //singular name of the listed records
				'plural'   => 'cbxwpemailloggers',    //plural name of the listed records
				'ajax'     => false      //does this table support ajax?
			) );
		}

		function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				/*$1%s*/
				$this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
				/*$2%s*/
				$item['id']                //The value of the checkbox should be the record's id
			);
		}//end method column_cb

		/**
		 * Callback for column 'id'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_id( $item ) {
			/*return '<a href="' . admin_url( 'admin.php?page=cbxscratingreviewreviewlist&view=view&id=' . $item['id'] ) . '" title="' . esc_html__( 'View Review', 'cbxwpemaillogger' ) . '">' . $item['id'] . '</a>' . ' (<a target="_blank" href="' . admin_url( 'admin.php?page=cbxscratingreviewreviewlist&view=addedit&id=' . $item['id'] ) . '" title="' . esc_html__( 'Edit Review', 'cbxwpemaillogger' ) . '">' . esc_html__( 'Edit', 'cbxwpemaillogger' ) . '</a>)';*/
		}


		/**
		 * Callback for column 'Date Created'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_date_created( $item ) {

			$actions = array();

			$view_url = add_query_arg( array(
				'view'   => 'email',
				'log_id' => intval( $item['id'] )
			) );

			$body_url = add_query_arg( array(
				'action'   => 'cbxwpemaillogger_log_body',
				'id'       => intval( $item['id'] ),
				'_wpnonce' => wp_create_nonce( 'cbxwpemaillogger' )
			), site_url() );

			$actions['view'] = '<a class="cbxwpemaillogger_actions cbxwpemaillogger_actions_view" href="' . esc_url( $view_url ) . '">' . esc_html__( 'View', 'cbxwpemaillogger' ) . '</a>';

			$actions['delete'] = '<a data-busy="0" data-id="' . intval( $item['id'] ) . '" class="cbxwpemaillogger_actions cbxwpemaillogger_actions_delete" href="#">' . esc_html__( 'Delete', 'cbxwpemaillogger' ) . '</a>';

			$actions['template'] = '<a title="'.esc_html__('Email Template/Body Preview', 'cbxwpemaillogger').'" class="thickbox cbxwpemaillogger_actions cbxwpemaillogger_actions_template" href="' . esc_url( $body_url ) . '">' . esc_html__( 'Template', 'cbxwpemaillogger' ) . '</a>';

			$actions['resend'] = '<a  data-busy="0" data-id="' . intval( $item['id'] ) . '" class="cbxwpemaillogger_actions cbxwpemaillogger_actions_resend" href="#">' . esc_html__( 'ReSend', 'cbxwpemaillogger' ) . '</a>';

			$date_created = '';
			if ( $item['date_created'] != '' ) {
				$date_created = CBXWPEmailLoggerHelper::DateReadableFormat( stripslashes( $item['date_created'] ), 'M j, Y g:i a' );
			}

			return $date_created . $this->row_actions( $actions );
		}//end method column_date_created


		/**
		 * Callback for column 'email_from'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_email_from( $item ) {
			$email_data  = maybe_unserialize( $item['email_data'] );
			$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
			$email_from  = isset( $headers_arr['email_from'] ) ? $headers_arr['email_from'] : array();

			return $email_from['from_name'] . '(' . sanitize_email($email_from['from_email']) . ')';
		}//end column_to

		/**
		 * Callback for column 'email_to'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_email_to( $item ) {
			$email_data  = maybe_unserialize( $item['email_data'] );
			$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
			$emails      = isset( $headers_arr['email_to'] ) ? $headers_arr['email_to'] : array();

			if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
				$formatted_emails = array();
				foreach ( $emails as $email ) {
					if ( $email['recipient_name'] != '' ) {
						$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email($email['address']) . ')';
					} else {
						$formatted_emails[] = sanitize_email($email['address']);
					}
				}

				return implode( ',', $formatted_emails );
			}

			return '';
		}//end column_to

		/**
		 * Callback for column 'email_reply_to'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_email_reply_to( $item ) {
			$email_data  = maybe_unserialize( $item['email_data'] );
			$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
			$emails      = isset( $headers_arr['email_reply_to'] ) ? $headers_arr['email_reply_to'] : array();

			if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
				$formatted_emails = array();
				foreach ( $emails as $email ) {
					if ( $email['recipient_name'] != '' ) {
						$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email($email['address']) . ')';
					} else {
						$formatted_emails[] = sanitize_email($email['address']);
					}
				}

				return implode( ',', $formatted_emails );
			}

			return '';
		}//end column_to


		/**
		 * Callback for column 'email_cc'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_email_cc( $item ) {
			$email_data  = maybe_unserialize( $item['email_data'] );
			$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
			$emails      = isset( $headers_arr['email_cc'] ) ? $headers_arr['email_cc'] : array();

			if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
				$formatted_emails = array();
				foreach ( $emails as $email ) {
					if ( $email['recipient_name'] != '' ) {
						$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email($email['address']) . ')';
					} else {
						$formatted_emails[] = sanitize_email($email['address']);
					}
				}

				return implode( ',', $formatted_emails );
			}

			return '';
		}//end method column_cc

		/**
		 * Callback for column 'email_bcc'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_email_bcc( $item ) {
			$email_data  = maybe_unserialize( $item['email_data'] );
			$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : array();
			$emails      = isset( $headers_arr['email_bcc'] ) ? $headers_arr['email_bcc'] : array();

			if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
				$formatted_emails = array();
				foreach ( $emails as $email ) {
					if ( $email['recipient_name'] != '' ) {
						$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email($email['address']) . ')';
					} else {
						$formatted_emails[] = sanitize_email($email['address']);
					}
				}

				return implode( ',', $formatted_emails );
			}

			return '';

		}//end method column_bcc


		/**
		 * Callback for column 'subject'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_subject( $item ) {
			$subject = esc_attr(wp_unslash($item['subject'] ));

			return $subject;
		}//end method column_subject


		/**
		 * Callback for column 'attachment'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_attachment( $item ) {

		    $id = intval( $item['id']);
			$email_data = maybe_unserialize( $item['email_data'] );

			$attachments = isset( $email_data['attachments'] ) ? $email_data['attachments'] : array();



			if ( is_array( $attachments ) && sizeof( $attachments ) > 0 ) {

				$setting          = new CBXWPEmailLoggerSettings();;
				$enable_store_attachment = intval( $setting->get_option( 'enable_store_attachment', 'cbxwpemaillogger_log', 0 ) );
				if($enable_store_attachment){
					global $wp_filesystem;
					require_once( ABSPATH . '/wp-admin/includes/file.php' );
					WP_Filesystem();

					$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();

					$log_folder_dir       = $dir_info['cbxwpemaillogger_base_dir'] . $id;

					if ($wp_filesystem->exists( $log_folder_dir ) ) {

					    $files =  array();

						foreach ($attachments as $attachment){


							if ($wp_filesystem->exists( $log_folder_dir.'/'.$attachment ) ) {

							    $attachment_download_url = admin_url('admin-ajax.php?file='.$attachment.'&action=cbxwpemaillogger_download_attachment&log_id='.$id);
								$attachment_download_url = wp_nonce_url( $attachment_download_url, 'cbxwpemaillogger', 'cbxwpemaillogger_nonce' );

							    $attachment_url  = '<a href="'.esc_url($attachment_download_url).'">'.$attachment.'</a>';

							    $files[] = $attachment_url;
                            }
                            else{
                                $files[] = $attachment;
                            }

						}

						return implode( '<br/>', $files );
                    }
                    else{
	                    return implode( '<br/>', $attachments );
                    }

                }
                else{
	                return implode( '<br/>', $attachments );
                }
			}

			return esc_html__( 'N/A', 'cbxwpemaillogger' );
		}//end method column_subject


		/**
		 * Callback for column 'status'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_status( $item ) {

			$status = isset( $item['status'] ) ? intval( $item['status'] ) : 0;

			return ( $status ) ? esc_html__( 'Sent', 'cbxwpemaillogger' ) : '<a title="'.esc_html__( 'See error message in details mode', 'cbxwpemaillogger' ).'" href="'.admin_url('admin.php?page=cbxwpemaillogger&view=email&log_id=').intval( $item['id'] ).'">'.esc_html__( 'Failed', 'cbxwpemaillogger' ).'</a>';
		}//end method column_subject

		/**
		 * Display col 'ip_address' value
		 *
		 * @param $item
		 *
		 * @return string|void
		 */
		function column_ip_address( $item ) {
			$ip_address = isset( $item['ip_address'] ) ? $item['ip_address'] : '';

			return esc_attr( $ip_address );
		}//end method column_ip_address


		/**
		 * Display col 'src_tracked' value
		 *
		 * @param $item
		 *
		 * @return string|void
		 */
		function column_src_tracked( $item ) {
			$src_tracked = isset( $item['src_tracked'] ) ? esc_attr(wp_unslash($item['src_tracked'])) : '';

			$sources = CBXWPEmailLoggerHelper::email_known_src();



			return isset($sources[$src_tracked])? esc_attr($sources[$src_tracked]): esc_html__('Untraced', 'cbxwpemaillogger');
		}//end method column_ip_address

		/**
		 * Callback for column 'actions'
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_actions( $item ) {

			$actions = array();

			$actions[] = '<a class="button button-primary button-small cbxwpemaillogger_actions cbxwpemaillogger_actions_view" href="#">' . esc_html__( 'View', 'cbxwpemaillogger' ) . '</a>';

			$actions[] = '<a data-busy="0" data-id="' . intval( $item['id'] ) . '" class="button button-small cbxwpemaillogger_actions cbxwpemaillogger_actions_delete" href="#">' . esc_html__( 'Delete', 'cbxwpemaillogger' ) . '</a>';

			return implode( ' ', $actions );
		}//end method column_subject


		function column_default( $item, $column_name ) {

			switch ( $column_name ) {
				case 'date_created':
					return $item[ $column_name ];
				case 'email_from':
					return $item[ $column_name ];
				case 'email_to':
					return $item[ $column_name ];
				case 'email_reply_to':
					return $item[ $column_name ];
				case 'cc':
					return $item[ $column_name ];
				case 'bcc':
					return $item[ $column_name ];
				case 'subject':
					return $item[ $column_name ];
				case 'email_type':
					return $item[ $column_name ];
				case 'status':
					return $item[ $column_name ];
				case 'attachment':
					return $item[ $column_name ];
				case 'ip_address':
					return $item[ $column_name ];
				default:
					//return print_r( $item, true ); //Show the whole array for troubleshooting purposes
					echo apply_filters( 'cbxwpemaillogger_log_listing_column_default', $item, $column_name );
			}
		}//end method column_default

		function get_columns() {
			$columns = array(
				'cb'             => '<input type="checkbox" />', //Render a checkbox instead of text
				'date_created'   => esc_html__( 'Date', 'cbxwpemaillogger' ),
				'email_to'       => esc_html__( 'To', 'cbxwpemaillogger' ),
				'subject'        => esc_html__( 'Subject', 'cbxwpemaillogger' ),
				'email_type'     => esc_html__( 'Type', 'cbxwpemaillogger' ),
				'email_from'     => esc_html__( 'From', 'cbxwpemaillogger' ),
				'email_reply_to' => esc_html__( 'ReplyTo', 'cbxwpemaillogger' ),
				'email_cc'       => esc_html__( 'CC', 'cbxwpemaillogger' ),
				'email_bcc'      => esc_html__( 'BCC', 'cbxwpemaillogger' ),
				'status'         => esc_html__( 'Status', 'cbxwpemaillogger' ),
				'attachment'     => esc_html__( 'Attachments', 'cbxwpemaillogger' ),
				'ip_address'     => esc_html__( 'IP Address', 'cbxwpemaillogger' ),
				'src_tracked'     => esc_html__( 'Source', 'cbxwpemaillogger' ),
				//'actions'       => esc_html__( 'Actions', 'cbxwpemaillogger' )
			);

			return apply_filters( 'cbxwpemaillogger_log_listing_columns', $columns );
		}//end method get_columns


		function get_sortable_columns() {
			$sortable_columns = array(
				//'id'           => array( 'logs.id', false ), //true means it's already sorted
				'date_created' => array( 'logs.date_created', false ),
				//'email_from'    => array( 'logs.email_from', false ),
				//'email_to'      => array( 'logs.email_to', false ),
				'subject'      => array( 'logs.subject', false ),
				'email_type'      => array( 'logs.email_type', false ),
				'status'       => array( 'logs.status', false ),
				'ip_address'   => array( 'logs.ip_address', false ),
			);

			return apply_filters( 'cbxwpemaillogger_log_listing_sortable_columns', $sortable_columns );
		}//end method get_sortable_columns


		function get_bulk_actions() {
			$status_arr = CBXWPEmailLoggerHelper::StatusOptions();

			$bulk_actions = apply_filters( 'cbxwpemaillogger_action', $status_arr );

			return $bulk_actions;
		}//end method get_bulk_actions

		function process_bulk_action() {

			$new_status = $this->current_action();

			if ( $new_status == - 1 ) {
				return;
			}

			global $wpdb;

			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

			if ( isset( $_REQUEST['delete_all'] ) && ! empty( $_REQUEST['delete_all'] ) ) {
				do_action( 'cbxwpemaillogger_log_all_delete_before' );

				$delete_all_status = $wpdb->query( "DELETE FROM $table_cbxwpemaillogger WHERE 1" );

				if ( $delete_all_status !== false ) {
					do_action( 'cbxwpemaillogger_log_all_delete_after' );
				}

			}//end delete all

			if ( isset( $_REQUEST['delete_all_old'] ) && ! empty( $_REQUEST['delete_all_old'] ) ) {

				$settings = new CBXWPEmailLoggerSettings();

				$delete_old_log = $settings->get_option( 'delete_old_log', 'cbxwpemaillogger_log', 'no' );

				if ( $delete_old_log == 'yes' ) {

					$log_old_days = intval( $settings->get_option( 'log_old_days', 'cbxwpemaillogger_log', '30' ) );

					if ( $log_old_days > 0 ) {

						CBXWPEmailLoggerHelper::delete_old_log( $log_old_days );

					}
				}

			}//end delete all


			//Detect when a bulk action is being triggered...
			if ( ! empty( $_REQUEST['cbxwpemaillogger'] ) ) {


				$results = $_REQUEST['cbxwpemaillogger'];
				foreach ( $results as $id ) {

					$id = intval( $id );

					if ( 'delete' === $new_status ) {
						do_action( 'cbxwpemaillogger_log_delete_before', $id );

						$delete_status = $wpdb->query( $wpdb->prepare( "DELETE FROM $table_cbxwpemaillogger WHERE id=%d", $id ) );

						if ( $delete_status !== false ) {
							do_action( 'cbxwpemaillogger_log_delete_after', $id );
						}
					}
				}
			}

			return;
		}//end method process_bulk_action


		/**
		 * Prepare the review log items
		 */
		function prepare_items() {
			global $wpdb; //This is used only if making any database queries

			$user   = get_current_user_id();
			$screen = get_current_screen();

			$current_page = $this->get_pagenum();

			$option_name = $screen->get_option( 'per_page', 'option' ); //the core class name is WP_Screen

			$per_page = intval( get_user_meta( $user, $option_name, true ) );

			if ( $per_page == 0 ) {
				$per_page = intval( $screen->get_option( 'per_page', 'default' ) );
			}


			$columns  = $this->get_columns();
			$hidden   = array();
			$sortable = $this->get_sortable_columns();


			$this->_column_headers = array( $columns, $hidden, $sortable );


			$this->process_bulk_action();


			$search  = isset( $_REQUEST['s'] ) ? esc_attr( wp_unslash( $_REQUEST['s'] ) ) : '';
			$logdate = ( isset( $_REQUEST['logdate'] ) && $_REQUEST['logdate'] != '' ) ? sanitize_text_field( $_REQUEST['logdate'] ) : '';

			$order   = ( isset( $_REQUEST['order'] ) && $_REQUEST['order'] != '' ) ? sanitize_text_field( $_REQUEST['order'] ) : 'DESC';
			$orderby = ( isset( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'logs.id';


			$data        = $this->getLogData( $search, $logdate, $orderby, $order, $per_page, $current_page );
			$total_items = intval( $this->getLogDataCount( $search, $logdate ) );

			$this->items = $data;

			/**
			 * REQUIRED. We also have to register our pagination options & calculations.
			 */
			$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
			) );

		}

		/**
		 * Get logs data
		 *
		 * @param string $search
		 * @param string $orderby
		 * @param string $order
		 * @param int    $perpage
		 * @param int    $page
		 *
		 * @return array|null|object
		 */
		public function getLogData( $search = '', $logdate = '', $orderby = 'logs.id', $order = 'DESC', $perpage = 20, $page = 1 ) {

			global $wpdb;

			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';


			$sql_select = "logs.*";
			$where_sql  = '';

			if ( $search != '' ) {
				if ( $where_sql != '' ) {
					$where_sql .= ' AND ';
				}

				$where_sql .= $wpdb->prepare( " logs.subject LIKE '%%%s%%' OR logs.email_data LIKE '%%%s%%' ", $search, $search );
			}

			if ( $logdate != '' ) {
				if ( $where_sql != '' ) {
					$where_sql .= ' AND ';
				}

				$where_sql .= $wpdb->prepare( " DATE(logs.date_created) = %s ", $logdate );
			}


			$where_sql = apply_filters( 'cbxwpemaillogger_log_listing_where', $where_sql, $search, $orderby, $order, $perpage, $page );

			if ( $where_sql == '' ) {
				$where_sql = '1';
			}

			$start_point = ( $page * $perpage ) - $perpage;
			$limit_sql   = "LIMIT";
			$limit_sql   .= ' ' . $start_point . ',';
			$limit_sql   .= ' ' . $perpage;

			$sortingOrder = " ORDER BY $orderby $order ";

			$data = $wpdb->get_results( "SELECT $sql_select FROM $table_cbxwpemaillogger as logs WHERE  $where_sql $sortingOrder  $limit_sql", 'ARRAY_A' );

			return $data;
		}//end method getLogData

		/**
		 * Logs data count
		 *
		 * @param string $search
		 * @param string $logdate
		 *
		 * @return null|string
		 */
		public function getLogDataCount( $search = '', $logdate = '' ) {

			global $wpdb;
			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

			$sql_select = "SELECT COUNT(*) FROM $table_cbxwpemaillogger as logs";

			$where_sql = '';


			if ( $search != '' ) {
				if ( $where_sql != '' ) {
					$where_sql .= ' AND ';
				}

				$where_sql .= $wpdb->prepare( " logs.subject LIKE '%%%s%%' OR logs.email_data LIKE '%%%s%%' ", $search, $search );
			}

			if ( $logdate != '' ) {
				if ( $where_sql != '' ) {
					$where_sql .= ' AND ';
				}

				$where_sql .= $wpdb->prepare( " DATE(logs.date_created) = %s ", $logdate );
			}

			$where_sql = apply_filters( 'cbxwpemaillogger_log_listing_where_total', $where_sql, $search );

			if ( $where_sql == '' ) {
				$where_sql = '1';
			}


			$count = $wpdb->get_var( "$sql_select WHERE  $where_sql" );

			return $count;
		}//end method getLogDataCount


		/**
		 * Generates content for a single row of the table
		 *
		 * @access public
		 *
		 * @param object $item The current item
		 */
		public function single_row( $item ) {
			$row_class = 'cbxwpemaillogger_row';
			$row_class = apply_filters( 'cbxwpemaillogger_row_class', $row_class, $item );
			echo '<tr id="cbxwpemaillogger_row_' . $item['id'] . '" class="' . $row_class . '">';
			$this->single_row_columns( $item );
			echo '</tr>';
		}//end method single_row

		/**
		 * Message to be displayed when there are no items
		 *
		 * @since  3.1.0
		 * @access public
		 */
		public function no_items() {
			echo '<div class="notice notice-warning inline "><p>' . esc_html__( 'No log found. Please change your search criteria for better result.', 'cbxwpemaillogger' ) . '</p></div>';
		}//end method no_items

		/**
		 * @param string $which
		 */
		protected function extra_tablenav( $which ) {

			if ( 'top' !== $which ) {
				return;
			}
			?>
			<div class="actions">
				<?php
					/*if ( ! is_singular() ) {
						if ( ! $this->is_trash ) {
							$this->months_dropdown( 'attachment' );
						}

						//This action is documented in wp-admin/includes/class-wp-posts-list-table.php
						do_action( 'restrict_manage_posts', '', $which );

						submit_button( __( 'Filter' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
					}*/

					if ( $this->has_items() ) {
						submit_button( esc_html__( 'Delete All', 'cbxwpemaillogger' ), 'apply', 'delete_all', false );
						submit_button( esc_html__( 'Delete Old Logs', 'cbxwpemaillogger' ), 'apply', 'delete_all_old', false );
					} ?>
			</div>
			<?php
		}

	}//end class CBXWPEmailLogger_List_Table