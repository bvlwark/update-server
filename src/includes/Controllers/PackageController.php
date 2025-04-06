<?php

namespace BVLWARK\UpdateServer\Controllers;

use BVLWARK\UpdateServer\AbstractSingleton;
use BVLWARK\UpdateServer\Admin\Menu;
use BVLWARK\UpdateServer\Admin\Notification;

defined( 'ABSPATH' ) || exit;

class PackageController extends AbstractSingleton {
	/**
	 * PackageController constructor.
	 */
	public function __construct() {
		// Admin POST requests.
		add_action( 'admin_post_bvlwark_add_package', array( $this, 'admin_post_add_package' ) );
	}

	/**
	 * Add a new package to the database.
	 *
	 * @return void
	 */
	public function admin_post_add_package(): void {
		// Check the nonce.
		check_admin_referer( 'bvlwark_add_package' );

		$package_id  = bvlwark_clean_int_request_value( 'package_id' );
		$name        = bvlwark_clean_string_request_value( 'name' );
		$slug        = bvlwark_clean_string_request_value( 'slug' );
		$type        = bvlwark_clean_string_request_value( 'type' );
		$description = bvlwark_clean_html_request_value( 'description' );
		$is_public   = bvlwark_clean_int_request_value( 'is_public', false );

		if ( $package_id ) {
			// Package already exists, update it.
			$package = bvlwark_update_package(
				$package_id,
				array(
					'name'        => $name,
					'slug'        => $slug,
					'type'        => $type,
					'description' => $description,
					'is_public'   => $is_public,
				)
			);
		} else {
			// New package, add it.
			$package = bvlwark_add_package(
				$name,
				$slug,
				$type,
				$description,
				$is_public,
			);
		}

		if ( is_wp_error( $package ) ) {
			Notification::error( $package->get_error_message() );
		} else {
			Notification::success(
				$package_id
					? esc_html__( 'Package updated successfully.', 'bvlwark-update-server' )
					: esc_html__( 'Package added successfully.', 'bvlwark-update-server' )
			);
		}

		wp_safe_redirect(
			wp_nonce_url(
				sprintf( 'admin.php?page=%s', Menu::PACKAGES_PAGE ),
				'bvlwark_add_package'
			)
		);
	}
}
