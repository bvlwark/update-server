<?php

namespace BVLWARK\UpdateServer\Admin;

defined( 'ABSPATH' ) || exit;

class Menu {
	/**
	 * Packages page slug.
	 */
	const string PACKAGES_PAGE = 'bvlwark_packages';

	/**
	 * Settings page slug.
	 */
	const string SETTINGS_PAGE = 'bvlwark_settings';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Plugin pages.
		new Pages\PackagesPage();
		new Pages\SettingsPage();

		// Screen options.
		add_filter( 'set-screen-option', array( $this, 'set_screen_options' ), 10, 3 );

		// Footer text.
		add_filter( 'admin_footer_text', array( $this, 'add_admin_footer_notice' ), 1 );
	}

	/**
	 * Returns an array of all plugin pages.
	 *
	 * @return array
	 */
	public function get_plugin_page_ids(): array {
		return array(
			'toplevel_page_bvlwark_packages',
			'packages_page_bvlwark_settings',
		);
	}

	/**
	 * Displays the new screen options.
	 *
	 * @param bool   $screen_option  The value to save instead of the option
	 *                               value. Defaults to false (to skip saving
	 *                               the current option).
	 * @param string $option         The option name.
	 * @param int    $value          The option value.
	 *
	 * @return int
	 */
	public function set_screen_options( bool $screen_option, string $option, int $value ): int {
		return $value;
	}

	/**
	 * Sets the custom footer text for the plugin pages.
	 *
	 * @param string $footer_text Original footer text.
	 *
	 * @return string
	 */
	public function add_admin_footer_notice( string $footer_text ): string {
		if ( ! current_user_can( 'manage_bvlwark_update_server' ) ) {
			return $footer_text;
		}

		$current_screen = get_current_screen();

		// Check to make sure we're on a plugin page.
		if ( isset( $current_screen->id ) && in_array( $current_screen->id, $this->get_plugin_page_ids(), true ) ) {
			$footer_text = sprintf(
				// translators: 1$: plugin name, 2$: 5 star.
				__( 'If you like %1$s please leave us a %2$s rating. Huge thanks in advance!', 'bvlwark-update-server' ),
				sprintf( '<strong>%s</strong>', esc_html__( 'BVLWARK Update Server', 'bvlwark-update-server' ) ),
				'<a href="https://wordpress.org/support/plugin/bvlwark-update-server/reviews/?rate=5#new-post" target="_blank" class="rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'bvlwark-update-server' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}
}
