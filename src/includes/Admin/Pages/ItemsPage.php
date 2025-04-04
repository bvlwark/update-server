<?php
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

namespace BVLWARK\UpdateServer\Admin\Pages;

use BVLWARK\UpdateServer\Admin\Menu;
use BVLWARK\UpdateServer\Tables\ItemsTable;

defined( 'ABSPATH' ) || exit;

class ItemsPage {
	/**
	 * @var ItemsTable
	 */
	protected ItemsTable $list;

	/**
	 * Licenses constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_pages' ), 9 );
		add_action( 'bvlwark_render_page_items_list', array( $this, 'list' ) );
		add_action( 'bvlwark_render_page_items_delete', array( $this, 'list' ) );
		add_action( 'bvlwark_render_page_items_add', array( $this, 'add' ) );
		add_action( 'bvlwark_render_page_items_edit', array( $this, 'edit' ) );
	}

	/**
	 * Sets up the licenses plugin pages.
	 */
	public function create_pages(): void {
		add_menu_page(
			__( 'BVLWARK Update Server', 'bvlwark-update-server' ),
			__( 'Items', 'bvlwark-update-server' ),
			'manage_bvlwark_update_server',
			Menu::ITEMS_PAGE,
			array( $this, 'page' ),
			'dashicons-superhero',
			10
		);

		$hook = add_submenu_page(
			Menu::ITEMS_PAGE,
			__( 'BVLWARK Update Server', 'bvlwark-update-server' ),
			__( 'Items', 'bvlwark-update-server' ),
			'manage_bvlwark_update_server',
			Menu::ITEMS_PAGE,
			array( $this, 'page' )
		);

		add_action( "load-$hook", array( $this, 'screen_options' ) );
	}

	/**
	 * Adds the supported screen options for the items list.
	 */
	public function screen_options(): void {
		$option = 'per_page';
		$args   = array(
			'label'   => __( 'Items per page', 'bvlwark-update-server' ),
			'default' => 10,
			'option'  => 'bvlwark_update_server_items_per_page',
		);

		add_screen_option( $option, $args );

		$this->list = new ItemsTable();
	}

	/**
	 * Sets up the items page.
	 */
	public function page(): void {
		$action = bvlwark_get_current_action( 'list' );

		do_action( "bvlwark_render_page_items_$action" );
	}

	/**
	 * Renders the "Items" page.
	 *
	 * @return void
	 */
	public function list(): void {
		bvlwark_get_template_html(
			'items/bvlwark-page-list-items.php',
			array(
				'items'        => $this->list,
				'add_item_url' => admin_url(
					sprintf(
						'admin.php?page=%s&action=add&_wpnonce=%s',
						Menu::ITEMS_PAGE,
						wp_create_nonce( 'bvlwark_add_item' )
					)
				),
			)
		);
	}

	/**
	 * Renders the "Items -> Add" page.
	 *
	 * @return void
	 */
	public function add(): void {
		check_admin_referer( 'bvlwark_add_item' );

		bvlwark_get_template_html( 'items/bvlwark-page-add-item.php' );
	}

	/**
	 * Renders the "Items -> Edit" page.
	 *
	 * @return void
	 */
	public function edit(): void {
		check_admin_referer( 'bvlwark_edit_items' );

		if ( isset( $_GET['id'] ) ) {
			$item_id = (int) $_GET['id'];
		} else {
			wp_die( esc_html__( 'Item ID missing from request', 'bvlwark-update-server' ) );
		}

		$item = bvlwark_get_item( $item_id );

		if ( is_wp_error( $item ) ) {
			wp_die( esc_html( $item->get_error_message() ) );
		}

		bvlwark_get_template_html(
			'items/bvlwark-page-edit-item.php',
			array(
				'item' => $item,
			)
		);
	}
}
