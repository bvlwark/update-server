<?php

use BVLWARK\UpdateServer\Models\PackageVersionModel;
use BVLWARK\UpdateServer\Repositories\PackageVersionRepository;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bvlwark_add_package_version' ) ) {
	/**
	 * Adds a new package version to the database.
	 *
	 * @param int         $package_id The corresponding package ID.
	 * @param string      $version    The version of the added package.
	 * @param string|null $requires   The required WordPress version of the added package.
	 * @param string|null $tested     The tested WordPress version of the added package.
	 * @param string|null $changelog  The changelog of the added package version.
	 *
	 * @return WP_Error|PackageVersionModel
	 */
	function bvlwark_add_package_version(
		int $package_id,
		string $version,
		?string $requires = null,
		?string $tested = null,
		?string $changelog = null,
	): WP_Error|PackageVersionModel {
		$package = bvlwark_get_package( $package_id );

		if ( is_wp_error( $package ) ) {
			return $package;
		}

		if ( strlen( $version ) > 50 ) {
			return new WP_Error( 409, __( 'Version cannot exceed 50 characters in length.', 'bvlwark-update-server' ) );
		}

		if ( $requires && strlen( $requires ) > 50 ) {
			return new WP_Error( 409, __( 'The required WordPress version cannot exceed 50 characters in length.', 'bvlwark-update-server' ) );
		}

		if ( $tested && strlen( $tested ) > 50 ) {
			return new WP_Error( 409, __( 'The tested WordPress version cannot exceed 50 characters in length.', 'bvlwark-update-server' ) );
		}

		return PackageVersionRepository::instance()->insert_one(
			array(
				'package_id' => $package_id,
				'version'    => $version,
				'requires'   => $requires,
				'tested'     => $tested,
				'changelog'  => $changelog,
			)
		);
	}
}
