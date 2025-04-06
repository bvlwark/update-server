<?php

namespace BVLWARK\UpdateServer\Tables;

use BVLWARK\UpdateServer\Admin\Menu;
use BVLWARK\UpdateServer\Admin\Notification;
use BVLWARK\UpdateServer\Installer;
use BVLWARK\UpdateServer\ItemTypeEnum;

defined( 'ABSPATH' ) || exit;

class ItemsTable extends AbstractTable {
	/**
	 * ItemsTable constructor.
	 */
	public function __construct() {
		global $wpdb;

		parent::__construct(
			array(
				'singular' => __( 'Item', 'bvlwark-update-server' ),
				'plural'   => __( 'Items', 'bvlwark-update-server' ),
				'ajax'     => false,
			),
			$wpdb->prefix . Installer::ITEM_TABLE_NAME
		);
	}

	/**
	 * Creates the different status filter links at the top of the table.
	 *
	 * @return array
	 */
	protected function get_views(): array {
		$status_links = array();
		$current      = ! empty( $_REQUEST['type'] )
			? strtolower( sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) )
			: 'all';

		// All.
		$class               = $current === 'all' ? ' class="current"' : '';
		$all_url             = remove_query_arg( 'type' );
		$status_links['all'] = sprintf(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$all_url,
			$class,
			__( 'All', 'bvlwark-update-server' ),
			bvlwark_get_item_count()
		);

		// Plugins.
		$class                   = $current === ItemTypeEnum::PLUGIN ? ' class="current"' : '';
		$plugins_url             = esc_url( add_query_arg( 'type', ItemTypeEnum::PLUGIN ) );
		$status_links['plugins'] = sprintf(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$plugins_url,
			$class,
			__( 'Plugins', 'bvlwark-update-server' ),
			bvlwark_get_item_count( array( 'type' => ItemTypeEnum::PLUGIN ) )
		);

		// Themes.
		$class                  = $current === ItemTypeEnum::THEME ? ' class="current"' : '';
		$themes_url             = esc_url( add_query_arg( 'type', ItemTypeEnum::THEME ) );
		$status_links['themes'] = sprintf(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$themes_url,
			$class,
			__( 'Themes', 'bvlwark-update-server' ),
			bvlwark_get_item_count( array( 'type' => ItemTypeEnum::THEME ) )
		);

		return $status_links;
	}

	/**
	 * Checkbox column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_cb( $item ): string {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			$item['id']
		);
	}

	/**
	 * Name column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_name( array $item ): string {
		// ID.
		// translators: %d: user id.
		$actions['id'] = sprintf( __( 'ID: %d', 'bvlwark-update-server' ), (int) $item['id'] );

		// Edit.
		$actions['edit'] = sprintf(
			'<a href="%s">%s</a>',
			admin_url(
				wp_nonce_url(
					sprintf(
						'admin.php?page=%s&action=edit&id=%d',
						Menu::ITEMS_PAGE,
						(int) $item['id']
					),
					'bvlwark_upsert_item'
				)
			),
			esc_html__( 'Edit', 'bvlwark-update-server' )
		);

		// Delete.
		$actions['delete'] = sprintf(
			'<a href="%s">%s</a>',
			admin_url(
				sprintf(
					'admin.php?page=%s&action=delete&id=%d&_wpnonce=%s',
					Menu::ITEMS_PAGE,
					(int) $item['id'],
					wp_create_nonce( 'delete' )
				)
			),
			__( 'Delete', 'bvlwark-update-server' )
		);

		return $item['name'] . $this->row_actions( $actions );
	}

	/**
	 * Slug column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_slug( array $item ): string {
		return $item['slug'];
	}

	/**
	 * Type column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_type( array $item ): string {
		return $item['type'] === ItemTypeEnum::PLUGIN
			? esc_html__( 'Plugin', 'bvlwark-update-server' )
			: esc_html__( 'Theme', 'bvlwark-update-server' );
	}

	/**
	 * Description column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_description( array $item ): string {
		return $item['description'];
	}

	/**
	 * Public column.
	 *
	 * @param array $item Associative array of column name and value pairs.
	 *
	 * @return string
	 */
	public function column_is_public( array $item ): string {
		return (int) $item['is_public']
			? esc_html__( 'Public', 'bvlwark-update-server' )
			: esc_html__( 'Private', 'bvlwark-update-server' );
	}

	/**
	 * Default column value.
	 *
	 * @param array  $item        Associative array of column name and value pairs.
	 * @param string $column_name Name of the current column.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ): string {
		$item = apply_filters( 'bvlwark_update_server_table_items_column_value', $item, $column_name );

		return $item[ $column_name ];
	}

	/**
	 * Defines sortable columns and their sort value.
	 *
	 * @return array
	 */
	public function get_sortable_columns(): array {
		$sortable_columns = array(
			'id'          => array( 'id', true ),
			'name'        => array( 'name', true ),
			'slug'        => array( 'slug', true ),
			'type'        => array( 'type', true ),
			'description' => array( 'description', true ),
			'is_public'   => array( 'is_public', true ),
			'created'     => array( 'created_at', true ),
			'updated'     => array( 'updated_at', true ),
		);

		return apply_filters(
			'bvlwark_update_server_table_items_column_sortable',
			$sortable_columns
		);
	}

	/**
	 * Defines items in the bulk action dropdown.
	 *
	 * @return array
	 */
	public function get_bulk_actions(): array {
		$bulk_actions = array(
			'delete' => __( 'Delete', 'bvlwark-update-server' ),
		);

		return apply_filters(
			'bvlwark_update_server_table_items_bulk_actions',
			$bulk_actions
		);
	}

	/**
	 * Processes the currently selected action.
	 */
	private function process_bulk_actions(): void {
		$action = $this->current_action();

		switch ( $action ) {
			case 'delete':
				$this->delete_items();
				break;
			default:
				break;
		}
	}

	/**
	 * Initialization function.
	 */
	public function prepare_items(): void {
		$this->_column_headers = $this->get_column_info();

		$this->process_bulk_actions();

		$per_page     = $this->get_items_per_page( 'bvlwark_update_server_items_per_page', 10 );
		$current_page = $this->get_pagenum();
		$items_count  = $this->get_items_count();

		$this->set_pagination_args(
			array(
				'total_items' => $items_count,
				'per_page'    => $per_page,
				'total_pages' => ceil( $items_count / $per_page ),
			)
		);

		$this->items = $this->get_items( $per_page, $current_page );
	}

	/**
	 * Retrieves the items from the database.
	 *
	 * @param int $per_page    Default amount of items per page.
	 * @param int $page_number Default page number.
	 *
	 * @return array
	 */
	private function get_items( int $per_page = 20, int $page_number = 1 ): array {
		global $wpdb;

		// phpcs:ignore
		return $wpdb->get_results( $this->build_sql_query( $per_page, $page_number, false ), ARRAY_A );
	}

	/**
	 * Retrieves the items table row count.
	 *
	 * @return int
	 */
	private function get_items_count(): int {
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
			: "SELECT * FROM $this->table WHERE 1 = 1";

		// Applies the type filter.
		if ( $this->is_type_filter_active() ) {
			// Already validated by the view filter check.
			// phpcs:ignore
			$sql .= $wpdb->prepare( ' AND type = %s', $_GET['type'] );
		}

		// Applies the search box filter.
		if ( $s ) {
			$sql .= $wpdb->prepare( ' AND name LIKE %s', "%$s%" );
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
	 * Output in case no items exist.
	 */
	public function no_items(): void {
		esc_html_e( 'No items found.', 'bvlwark-update-server' );
	}

	/**
	 * Set the table columns.
	 */
	public function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'name'        => esc_html__( 'Name', 'bvlwark-update-server' ),
			'slug'        => esc_html__( 'Slug', 'bvlwark-update-server' ),
			'type'        => esc_html__( 'Type', 'bvlwark-update-server' ),
			'description' => esc_html__( 'Description', 'bvlwark-update-server' ),
			'is_public'   => esc_html__( 'Public', 'bvlwark-update-server' ),
			'created'     => esc_html__( 'Created', 'bvlwark-update-server' ),
			'updated'     => esc_html__( 'Updated', 'bvlwark-update-server' ),
		);

		return apply_filters( 'bvlwark_update_server_table_items_column_name', $columns );
	}

	/**
	 * Removes the item(s) permanently from the database.
	 *
	 * @return void
	 */
	private function delete_items(): void {
		$this->verify_nonce( 'delete', Menu::ITEMS_PAGE );
		$this->verify_selection(
			'id',
			esc_html__( 'No items were selected.', 'bvlwark-update-server' ),
			Menu::ITEMS_PAGE
		);

		$item_ids = isset( $_REQUEST['id'] )
			? array_map( 'intval', (array) wp_unslash( $_REQUEST['id'] ) )
			: array();
		$count    = 0;

		foreach ( $item_ids as $item_id ) {
			$result = bvlwark_delete_item( $item_id );

			if ( ! is_wp_error( $result ) ) {
				++$count;
			}
		}

		$message = esc_html(
			// translators: %d: number of items deleted.
			_n(
				'%s item deleted.',
				'%s items deleted.',
				$count,
				'bvlwark-update-server',
			)
		);

		// Set the admin notice.
		Notification::success( $message );

		// Redirect and exit.
		wp_safe_redirect(
			admin_url(
				sprintf( 'admin.php?page=%s', Menu::ITEMS_PAGE )
			)
		);
	}

	/**
	 * Checks if there are currently any item type filters active.
	 *
	 * @return bool
	 */
	private function is_type_filter_active(): bool {
		return ( isset( $_GET['type'] ) && in_array( strtolower( $_GET['type'] ), ItemTypeEnum::$status, true ) );
	}
}
