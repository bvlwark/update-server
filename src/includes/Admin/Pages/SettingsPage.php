<?php

namespace BVLWARK\UpdateServer\Admin\Pages;

use BVLWARK\UpdateServer\Admin\Menu;
use BVLWARK\UpdateServer\Models\ApiKeyModel;
use BVLWARK\UpdateServer\Tables\ApiKeyTable;

defined( 'ABSPATH' ) || exit;

class SettingsPage {
	/**
	 * @var ApiKeyTable
	 */
	protected ApiKeyTable $list;

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_pages' ), 9 );
		add_action( 'admin_init', array( $this, 'init_settings_api' ) );
	}

	/**
	 * Sets up the settings plugin pages.
	 */
	public function create_pages(): void {
		$hook = add_submenu_page(
			Menu::ITEMS_PAGE,
			__( 'BVLWARK Update Server - Settings', 'bvlwark-update-server' ),
			__( 'Settings', 'bvlwark-update-server' ),
			'manage_bvlwark_update_server',
			Menu::SETTINGS_PAGE,
			array( $this, 'page' )
		);
		add_action( "load-$hook", array( $this, 'screen_options' ) );
	}

	/**
	 * Initialized the plugin Settings API.
	 */
	public function init_settings_api(): void {
		// phpcs:ignore
		// new \BVLWARK\Licenses\Settings();
	}

	/**
	 * Adds the supported screen options for the generators list.
	 *
	 * @return void
	 */
	public function screen_options(): void {
		$args = array(
			'label'   => __( 'Key per page', 'bvlwark-update-server' ),
			'default' => 10,
			'option'  => 'bvlwark_api_keys_per_page',
		);

		add_screen_option( 'per_page', $args );

		$this->list = new ApiKeyTable();
	}

	/**
	 * Sets up the settings page.
	 */
	public function page(): void {
		$tab    = bvlwark_get_current_tab();
		$action = bvlwark_get_current_action( 'list' );

		if ( in_array( $tab, array( 'general', 'order_status', 'tools' ), true ) ) {
			$slug = str_replace( '_', '-', $tab );

			bvlwark_get_template_html(
				"settings/bvlwark-page-settings-$slug.php",
				array(
					'tab' => $tab,
				)
			);
		} elseif ( $tab === 'rest_api' ) {
			switch ( $action ) {
				case 'revoke':
				case 'list':
					bvlwark_get_template_html(
						'settings/rest-api/bvlwark-page-settings-rest-api-list.php',
						array(
							'keys'                 => $this->list,
							'tab'                  => $tab,
							'add_rest_api_key_url' => admin_url( sprintf( 'admin.php?page=%s&tab=rest_api&action=create', Menu::SETTINGS_PAGE ) ),
						)
					);
					break;
				case 'create':
				case 'edit':
					$api_key_id   = isset( $_GET['id'] ) ? (int) $_GET['id'] : null;
					$api_key      = new ApiKeyModel();
					$user         = null;
					$user_options = array();
					$date         = null;

					if ( $api_key_id ) {
						$api_key      = bvlwark_get_api_key( $api_key_id );
						$user         = get_userdata( $api_key->get_user_id() );
						$user_options = array(
							array(
								'value' => $user->ID,
								'label' => sprintf(
									'%s (#%d - %s)',
									$user->user_nicename,
									$user->ID,
									$user->user_email
								),
							),
						);

						if ( $api_key->get_last_access() ) {
							$date = sprintf(
								// translators: 1$: date, 2$: time. Example: 2023-12-31 at 23:59.
								esc_html__( '%1$s at %2$s', 'bvlwark-update-server' ),
								date_i18n( 'F j, Y', strtotime( $api_key->get_last_access() ) ),
								date_i18n( 'g:i a', strtotime( $api_key->get_last_access() ) )
							);
						}
					}

					bvlwark_get_template_html(
						'settings/rest-api/bvlwark-page-settings-rest-api-key.php',
						array(
							'api_key'      => $api_key,
							'api_key_id'   => $api_key_id,
							'action'       => $action,
							'user'         => $user,
							'user_options' => $user_options,
							'permissions'  => bvlwark_get_rest_api_keys_permissions(),
							'date'         => $date,
							'url_revoke'   => wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'revoke',
										'key'    => $api_key_id,
									),
									sprintf(
										admin_url( 'admin.php?page=%s&tab=rest_api' ),
										Menu::SETTINGS_PAGE
									)
								),
								'revoke'
							),
						)
					);
					break;
				case 'show':
					/** @var ApiKeyModel $api_key */
					$api_key = get_transient( 'bvlwark_api_key' );

					bvlwark_get_template_html(
						'settings/rest-api/bvlwark-page-settings-rest-api-show.php',
						array(
							'api_key'      => $api_key,
							'consumer_key' => get_transient( 'bvlwark_consumer_key' ),
							'url_revoke'   => wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'revoke',
										'key'    => $api_key ? $api_key->get_id() : '',
									),
									sprintf(
										admin_url( 'admin.php?page=%s&tab=rest_api' ),
										Menu::SETTINGS_PAGE
									)
								),
								'revoke'
							),
						)
					);

					// Immediately remove the values from the database.
					delete_transient( 'bvlwark_api_key' );
					delete_transient( 'bvlwark_consumer_key' );
					break;
			}
		}
	}
}
