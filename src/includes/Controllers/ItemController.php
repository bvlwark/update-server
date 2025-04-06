<?php

namespace BVLWARK\UpdateServer\Controllers;

use BVLWARK\UpdateServer\AbstractSingleton;
use BVLWARK\UpdateServer\Admin\Menu;
use BVLWARK\UpdateServer\Admin\Notification;

defined( 'ABSPATH' ) || exit;

class ItemController extends AbstractSingleton {
	/**
	 * ItemController constructor.
	 */
	public function __construct() {
		// Admin POST requests.
		add_action( 'admin_post_bvlwark_update_server_add_item', array( $this, 'admin_post_add_item' ) );
	}

	/**
	 * Add a new item to the database.
	 *
	 * @return void
	 */
	public function admin_post_add_item(): void {
		// Check the nonce.
		check_admin_referer( 'bvlwark_update_server_add_item' );

		$item_id     = bvlwark_clean_int_request_value( 'item_id' );
		$name        = bvlwark_clean_string_request_value( 'name' );
		$slug        = bvlwark_clean_string_request_value( 'slug' );
		$type        = bvlwark_clean_string_request_value( 'type' );
		$description = bvlwark_clean_html_request_value( 'description' );
		$is_public   = bvlwark_clean_int_request_value( 'is_public', false );

		if ( $item_id ) {
			// Item already exists, update it.
			$item = bvlwark_update_item(
				$item_id,
				array(
					'name'        => $name,
					'slug'        => $slug,
					'type'        => $type,
					'description' => $description,
					'is_public'   => $is_public,
				)
			);
		} else {
			// New item, add it.
			$item = bvlwark_add_item(
				$name,
				$slug,
				$type,
				$description,
				$is_public,
			);
		}

		if ( is_wp_error( $item ) ) {
			Notification::error( $item->get_error_message() );
		} else {
			Notification::success(
				$item_id
					? esc_html__( 'Item updated successfully.', 'bvlwark-update-server' )
					: esc_html__( 'Item added successfully.', 'bvlwark-update-server' )
			);
		}

		wp_safe_redirect(
			wp_nonce_url(
				sprintf( 'admin.php?page=%s', Menu::ITEMS_PAGE ),
				'bvlwark_update_server_add_item'
			)
		);
	}
}
