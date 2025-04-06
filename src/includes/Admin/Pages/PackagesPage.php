<?php
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

namespace BVLWARK\UpdateServer\Admin\Pages;

use BVLWARK\UpdateServer\Admin\Menu;
use BVLWARK\UpdateServer\Tables\PackagesTable;

defined( 'ABSPATH' ) || exit;

class PackagesPage {
	/**
	 * @var PackagesTable
	 */
	protected PackagesTable $list;

	/**
	 * PackagesPage constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_pages' ), 9 );
		add_action( 'bvlwark_render_page_packages_list', array( $this, 'list' ) );
		add_action( 'bvlwark_render_page_packages_delete', array( $this, 'list' ) );
		add_action( 'bvlwark_render_page_packages_add', array( $this, 'upsert' ) );
		add_action( 'bvlwark_render_page_packages_edit', array( $this, 'upsert' ) );
	}

	/**
	 * Sets up the packages plugin pages.
	 */
	public function create_pages(): void {
		add_menu_page(
			__( 'BVLWARK Update Server', 'bvlwark-update-server' ),
			__( 'BVLWARK Update Server', 'bvlwark-update-server' ),
			'manage_bvlwark_update_server',
			Menu::PACKAGES_PAGE,
			array( $this, 'page' ),
			'dashicons-superhero',
			10
		);

		$hook = add_submenu_page(
			Menu::PACKAGES_PAGE,
			__( 'BVLWARK Update Server', 'bvlwark-update-server' ),
			__( 'Packages', 'bvlwark-update-server' ),
			'manage_bvlwark_update_server',
			Menu::PACKAGES_PAGE,
			array( $this, 'page' )
		);

		add_action( "load-$hook", array( $this, 'screen_options' ) );
	}

	/**
	 * Adds the supported screen options for the packages list.
	 */
	public function screen_options(): void {
		$option = 'per_page';
		$args   = array(
			'label'   => __( 'Packages per page', 'bvlwark-update-server' ),
			'default' => 10,
			'option'  => 'bvlwark_packages_per_page',
		);

		add_screen_option( $option, $args );

		$this->list = new PackagesTable();
	}

	/**
	 * Sets up the packagess page.
	 */
	public function page(): void {
		$action = bvlwark_get_current_action( 'list' );

		do_action( "bvlwark_render_page_packages_$action" );
	}

	/**
	 * Renders the "Packages" page.
	 *
	 * @return void
	 */
	public function list(): void {
		bvlwark_get_template_html(
			'packages/bvlwark-page-list-packages.php',
			array(
				'packages'        => $this->list,
				'add_package_url' => admin_url(
					sprintf(
						'admin.php?page=%s&action=add&_wpnonce=%s',
						Menu::PACKAGES_PAGE,
						wp_create_nonce( 'bvlwark_upsert_package' )
					)
				),
			)
		);
	}

	/**
	 * Renders the "Packages -> Add" or "Packages -> Edit" page.
	 *
	 * @return void
	 */
	public function upsert(): void {
		check_admin_referer( 'bvlwark_upsert_package' );

		$package_id = bvlwark_clean_int_request_value( 'id' );
		$package    = $package_id ? bvlwark_get_package( $package_id ) : null;

		if ( is_wp_error( $package ) ) {
			wp_die( esc_html( $package->get_error_message() ) );
		}

		bvlwark_get_template_html(
			'packages/bvlwark-page-upsert-package.php',
			array(
				'package_id' => $package_id,
				'package'    => $package,
			)
		);
	}
}
