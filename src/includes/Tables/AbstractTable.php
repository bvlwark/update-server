<?php

namespace BVLWARK\UpdateServer\Tables;

use BVLWARK\UpdateServer\Admin\Notification;
use WP_List_Table;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

abstract class AbstractTable extends WP_List_Table {
	/**
	 * @var string
	 */
	protected string $table;

	/**
	 * @var string
	 */
	protected string $date_format;

	/**
	 * @var string
	 */
	protected string $time_format;

	/**
	 * @var string
	 */
	protected string $gmt_offset;

	/**
	 * AbstractTable constructor.
	 *
	 * @param array  $args       Array string of arguments.
	 * @param string $table_name Table name.
	 */
	public function __construct( array $args, string $table_name ) {
		parent::__construct( $args );

		$this->table       = $table_name;
		$this->date_format = get_option( 'date_format' );
		$this->time_format = get_option( 'time_format' );
		$this->gmt_offset  = get_option( 'gmt_offset' );
	}

	/**
	 * Builds the SQL query used for data selection in the table.
	 *
	 * @param int|null $per_page    How many items per page.
	 * @param int|null $page_number Current page number.
	 * @param bool     $count       Count or select values.
	 *
	 * @return string
	 */
	abstract protected function build_sql_query( ?int $per_page = null, ?int $page_number = null, bool $count = true ): string;

	/**
	 * Created column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_created( array $item ): string {
		$html = '';

		if ( $item['created_at'] ) {
			$offset_seconds = floatval( $this->gmt_offset ) * 60 * 60;
			$timestamp      = strtotime( $item['created_at'] ) + $offset_seconds;
			$result         = gmdate( BVLWARK_UPDATE_SERVER_DB_DATE_FORMAT, $timestamp );
			$date           = bvlwark_safe_date_time( $result );

			$html .= sprintf(
				'<span>%s <b>%s, %s</b></span>',
				esc_html__( 'at', 'bvlwark-update-server' ),
				$date?->format( $this->date_format ),
				$date?->format( $this->time_format )
			);
		}

		if ( $item['created_by'] ) {
			$user = get_user_by( 'id', $item['created_by'] );

			if ( $user ) {
				if ( current_user_can( 'edit_users' ) ) {
					$html .= sprintf(
						'<br>%s <a href="%s">%s</a>',
						esc_html__( 'by', 'bvlwark-update-server' ),
						get_edit_user_link( $user->ID ),
						$user->display_name
					);
				} else {
					$html .= sprintf(
						'<br><span>%s %s</span>',
						esc_html__( 'by', 'bvlwark-update-server' ),
						$user->display_name
					);
				}
			}
		}

		return $html;
	}

	/**
	 * Updated column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_updated( array $item ): string {
		$html = '';

		if ( $item['updated_at'] ) {
			$offset_seconds = floatval( $this->gmt_offset ) * 60 * 60;
			$timestamp      = strtotime( $item['updated_at'] ) + $offset_seconds;
			$result         = gmdate( BVLWARK_UPDATE_SERVER_DB_DATE_FORMAT, $timestamp );
			$date           = bvlwark_safe_date_time( $result );

			$html .= sprintf(
				'<span>%s <b>%s, %s</b></span>',
				esc_html__( 'at', 'bvlwark-update-server' ),
				$date?->format( $this->date_format ),
				$date?->format( $this->time_format )
			);
		}

		if ( $item['updated_by'] ) {
			$user = get_user_by( 'id', $item['updated_by'] );

			if ( $user ) {
				if ( current_user_can( 'edit_users' ) ) {
					$html .= sprintf(
						'<br>%s <a href="%s">%s</a>',
						__( 'by', 'bvlwark-update-server' ),
						get_edit_user_link( $user->ID ),
						$user->display_name
					);
				} else {
					$html .= sprintf(
						'<br><span>%s %s</span>',
						__( 'by', 'bvlwark-update-server' ),
						$user->display_name
					);
				}
			}
		}

		return $html;
	}

	/**
	 * Checks if the given nonce is valid.
	 *
	 * @param string $action      The nonce to check.
	 * @param string $redirect_to The page to redirect to if the nonce is
	 *                            invalid.
	 *
	 * @return void
	 */
	protected function verify_nonce( string $action, string $redirect_to ): void {
		$nonce           = bvlwark_get_nonce();
		$is_single_valid = wp_verify_nonce( $nonce, $action );
		$is_bulk_valid   = wp_verify_nonce( $nonce, "bulk-{$this->_args['plural']}" );

		if ( ! $is_single_valid && ! $is_bulk_valid ) {
			Notification::error( __( 'The nonce is invalid or has expired.', 'bvlwark-update-server' ) );

			wp_safe_redirect(
				admin_url( sprintf( 'admin.php?page=%s', $redirect_to ) )
			);

			exit();
		}
	}

	/**
	 * Make sure that a selection was made for the bulk action.
	 *
	 * @param string $name        Name of the key in the request.
	 * @param string $message     Warning message.
	 * @param string $redirect_to The page to redirect to if the selection is
	 *                            invalid.
	 *
	 * @return void
	 */
	protected function verify_selection( string $name, string $message, string $redirect_to ): void {
		// Nothing was selected, show a warning and redirect.
		if ( ! array_key_exists( $name, $_REQUEST ) ) {
			Notification::warning( $message );

			wp_safe_redirect(
				admin_url(
					sprintf( 'admin.php?page=%s', $redirect_to )
				)
			);

			exit();
		}
	}
}
