<?php

namespace BVLWARK\UpdateServer\Tables;

use BVLWARK\UpdateServer\Admin\Menu;
use BVLWARK\UpdateServer\Admin\Notification;
use BVLWARK\UpdateServer\Installer;
use BVLWARK\UpdateServer\Repositories\ApiKeyRepository;

defined( 'ABSPATH' ) || exit;

class ApiKeyTable extends AbstractTable {
	/**
	 * ApiKeyTable constructor.
	 */
	public function __construct() {
		global $wpdb;

		parent::__construct(
			array(
				'singular' => esc_html__( 'Key', 'bvlwark-update-server' ),
				'plural'   => esc_html__( 'Keys', 'bvlwark-update-server' ),
				'ajax'     => false,
			),
			$wpdb->prefix . Installer::API_KEY_TABLE_NAME,
		);
	}

	/**
	 * No items found text.
	 */
	public function no_items(): void {
		esc_html_e( 'No keys found.', 'bvlwark-update-server' );
	}

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns(): array {
		return array(
			'cb'            => '<input type="checkbox" />',
			'title'         => esc_html__( 'Description', 'bvlwark-update-server' ),
			'truncated_key' => esc_html__( 'Consumer key ending in', 'bvlwark-update-server' ),
			'user'          => esc_html__( 'User', 'bvlwark-update-server' ),
			'permissions'   => esc_html__( 'Permissions', 'bvlwark-update-server' ),
			'last_access'   => esc_html__( 'Last access', 'bvlwark-update-server' ),
		);
	}

	/**
	 * Checkbox column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_cb( $item ): string {
		return sprintf( '<input type="checkbox" name="key[]" value="%1$s" />', $item['id'] );
	}

	/**
	 * Title column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_title( array $item ): string {
		$key_id  = (int) $item['id'];
		$url     = admin_url( sprintf( 'admin.php?page=%s&tab=rest_api&action=edit&id=%d', Menu::SETTINGS_PAGE, $key_id ) );
		$user_id = (int) $item['user_id'];

		// Check if current user can edit other users or if it's the same user.
		$user_can_edit = current_user_can( 'edit_user', $user_id ) || get_current_user_id() === $user_id;

		$output = '<strong>';

		if ( $user_can_edit ) {
			$output .= sprintf( '<a href="%s" class="row-title">', esc_url( $url ) );
		}

		if ( empty( $item['description'] ) ) {
			$output .= esc_html__( 'API key', 'bvlwark-update-server' );
		} else {
			$output .= esc_html( $item['description'] );
		}

		if ( $user_can_edit ) {
			$output .= '</a>';
		}

		$output .= '</strong>';

		// Get actions.
		$actions = array(
			// translators: %d: api key id.
			'id' => sprintf( esc_html__( 'ID: %d', 'bvlwark-update-server' ), $key_id ),
		);

		if ( $user_can_edit ) {
			$actions['edit']  = sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'View/Edit', 'bvlwark-update-server' ) );
			$actions['trash'] = sprintf(
				'<a class="submitdelete" aria-label="%s" href="%s">%s</a>',
				esc_attr__( 'Revoke API key', 'bvlwark-update-server' ),
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'revoke',
								'key'    => $key_id,
							),
							admin_url( sprintf( 'admin.php?page=%s&tab=rest_api', Menu::SETTINGS_PAGE ) )
						),
						'revoke'
					)
				),
				esc_html__( 'Revoke', 'bvlwark-update-server' )
			);
		}

		$row_actions = array();

		foreach ( $actions as $class => $label ) {
			$row_actions[] = sprintf( '<span class="%s">%s</span>', esc_attr( $class ), wp_kses( $label, wp_kses_allowed_html() ) );
		}

		$output .= sprintf( '<div class="row-actions">%s</div>', implode( ' | ', $row_actions ) );

		return $output;
	}

	/**
	 * Truncated consumer key column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_truncated_key( array $item ): string {
		return sprintf( '<code>&hellip;%s</code>', esc_html( $item['truncated_key'] ) );
	}

	/**
	 * User column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_user( array $item ): string {
		$user = get_user_by( 'id', $item['user_id'] );

		if ( ! $user ) {
			return '';
		}

		if ( current_user_can( 'edit_user', $user->ID ) ) {
			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( array( 'user_id' => $user->ID ), admin_url( 'user-edit.php' ) ) ),
				esc_html( $user->display_name )
			);
		}

		return esc_html( $user->display_name );
	}

	/**
	 * Permissions column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_permissions( array $item ): string {
		$permission_key = $item['permissions'];
		$permissions    = array(
			'read'       => __( 'Read', 'bvlwark-update-server' ),
			'write'      => __( 'Write', 'bvlwark-update-server' ),
			'read_write' => __( 'Read/Write', 'bvlwark-update-server' ),
		);

		if ( isset( $permissions[ $permission_key ] ) ) {
			return esc_html( $permissions[ $permission_key ] );
		}

		return '';
	}

	/**
	 * Last access column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_last_access( array $item ): string {
		if ( ! empty( $item['last_access'] ) ) {
			$date = sprintf(
				// translators: 1$: date 2$: time - example: 2023-12-31 at 23:59.
				esc_html__( '%1$s at %2$s', 'bvlwark-update-server' ),
				date_i18n( 'F j, Y', strtotime( $item['last_access'] ) ),
				date_i18n( 'g:i a', strtotime( $item['last_access'] ) )
			);

			return apply_filters( 'bvlwark_update_server_api_key_last_access_datetime', $date, $item['last_access'] );
		}

		return esc_html__( 'Unknown', 'bvlwark-update-server' );
	}

	/**
	 * Defines sortable columns and their sort value.
	 *
	 * @return array
	 */
	public function get_sortable_columns(): array {
		$sortable_columns = array(
			'created' => array( 'created_at', true ),
			'updated' => array( 'updated_at', true ),
		);

		return apply_filters( 'bvlwark_table_api_keys_column_sortable', $sortable_columns );
	}

	/**
	 * Defines items in the bulk action dropdown.
	 *
	 * @return array
	 */
	protected function get_bulk_actions(): array {
		if ( ! current_user_can( 'remove_users' ) ) {
			return array();
		}

		return array(
			'revoke' => __( 'Revoke', 'bvlwark-update-server' ),
		);
	}

	/**
	 * Handle bulk action requests.
	 */
	private function process_bulk_actions(): void {
		$action = $this->current_action();

		if ( ! $action ) {
			return;
		}

		if ( ! current_user_can( 'remove_users' ) ) {
			return;
		}

		switch ( $action ) {
			case 'revoke':
				$this->verify_nonce( 'revoke', Menu::SETTINGS_PAGE );
				$this->verify_selection(
					'key',
					esc_html__( 'No API keys were selected.', 'bvlwark-update-server' ),
					sprintf( '%s&tab=rest_api', Menu::SETTINGS_PAGE ),
				);
				$this->revoke_keys();
				break;
			default:
				break;
		}
	}

	/**
	 * Prepare table list items.
	 */
	public function prepare_items(): void {
		$this->_column_headers = $this->get_column_info();

		$this->process_bulk_actions();

		$per_page     = $this->get_items_per_page( 'bvlwark_update_server_api_keys_per_page', 10 );
		$current_page = $this->get_pagenum();
		$count        = $this->get_api_key_count();

		$this->set_pagination_args(
			array(
				'total_items' => $count,
				'per_page'    => $per_page,
				'total_pages' => ceil( $count / $per_page ),
			)
		);

		$this->items = $this->get_api_keys( $per_page, $current_page );
	}

	/**
	 * Retrieves the api keys from the database.
	 *
	 * @param int $per_page    Default amount of api keys per page.
	 * @param int $page_number Default page number.
	 *
	 * @return array
	 */
	private function get_api_keys( int $per_page = 20, int $page_number = 1 ): array {
		global $wpdb;

		// phpcs:ignore
		return $wpdb->get_results( $this->build_sql_query( $per_page, $page_number, false ), ARRAY_A );
	}

	/**
	 * Retrieves the license table row count.
	 *
	 * @return int
	 */
	private function get_api_key_count(): int {
		global $wpdb;

		// phpcs:ignore
		return (int) $wpdb->get_var( $this->build_sql_query() );
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
	protected function build_sql_query( ?int $per_page = null, ?int $page_number = null, bool $count = true ): string {
		global $wpdb;

		$s = isset( $_REQUEST['s'] )
			? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) )
			: '';

		$order_by_column    = 'id';
		$order_by_direction = 'DESC';

		if ( isset( $_REQUEST['orderby'] ) ) {
			$order_by_column = sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) );
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$order_by_direction = sanitize_text_field( wp_unslash( $_REQUEST['order'] ) );
		}

		$sql = $count
			? "SELECT COUNT(*) FROM $this->table WHERE 1 = 1"
			: "SELECT id, user_id, description, permissions, truncated_key, last_access FROM $this->table WHERE 1 = 1";

		// Applies the search box filter.
		if ( $s ) {
			$sql .= $wpdb->prepare( ' AND description LIKE %s', "%$s%", );
		}

		// Applies sorting.
		$sql .= " ORDER BY $order_by_column $order_by_direction";

		// Applies the limit.
		if ( $per_page ) {
			$sql .= " LIMIT $per_page";
		}

		// Applies the offset.
		if ( $page_number ) {
			$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		}

		return $sql;
	}

	/**
	 * Permanently deletes API keys from the database.
	 *
	 * @return void
	 */
	private function revoke_keys(): void {
		$key = isset( $_REQUEST['key'] )
			? array_map( 'intval', (array) wp_unslash( $_REQUEST['key'] ) )
			: array();

		$count = ApiKeyRepository::instance()->delete( $key );

		if ( $count ) {
			// translators: %d: number of api keys.
			Notification::success( sprintf( esc_html__( '%d API key(s) permanently revoked.', 'bvlwark-update-server' ), $count ) );
		} else {
			Notification::error( esc_html__( 'There was a problem revoking the API key(s).', 'bvlwark-update-server' ) );
		}

		wp_safe_redirect( sprintf( 'admin.php?page=%s&tab=rest_api', Menu::SETTINGS_PAGE ) );
	}
}
