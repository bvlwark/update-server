<?php

namespace BVLWARK\UpdateServer;

defined( 'ABSPATH' ) || exit;

class AssetManager {
	/**
	 * Registers the plugin's JavaScript and CSS files.
	 *
	 * @param string $hook Current screen hook.
	 *
	 * @return void
	 */
	public static function register( string $hook ): void {
		self::register_styles( $hook );
		self::register_scripts( $hook );
	}

	/**
	 * Registers the plugin's CSS files.
	 *
	 * @param string $hook Current screen hook.
	 *
	 * @return void
	 */
	private static function register_styles( string $hook ): void {
	}

	/**
	 * Registers the plugin's JavaScript files.
	 *
	 * @param string $hook Current screen hook.
	 *
	 * @return void
	 */
	private static function register_scripts( string $hook ): void {
		if ( $hook === 'toplevel_page_bvlwark_update_server_items' ) {
			wp_register_script(
				'bvlwark_admin_page_items',
				bvlwark_get_js_url( 'bvlwark-admin-page-items.js' ),
				array( 'jquery' ),
				BVLWARK_UPDATE_SERVER_PLUGIN_VERSION,
				false,
			);
		}
	}

	/**
	 * Enqueues the plugin's JavaScript and CSS files.
	 *
	 * @param string $hook Current screen hook.
	 *
	 * @return void
	 */
	public static function enqueue( string $hook ): void {
		if ( $hook === 'toplevel_page_bvlwark_update_server_items' ) {
			wp_enqueue_editor();
			wp_enqueue_script( 'bvlwark_admin_page_items' );
		}
	}
}
