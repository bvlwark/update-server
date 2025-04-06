<?php

use BVLWARK\UpdateServer\Models\ItemVersionModel;
use BVLWARK\UpdateServer\Repositories\ItemVersionRepository;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bvlwark_add_item_version' ) ) {
	/**
	 * Adds a new item version to the database.
	 *
	 * @param int         $item_id    The corresponding item ID.
	 * @param string      $version    The version of the added item.
	 * @param string|null $requires   The required WordPress version of the added item.
	 * @param string|null $tested     The tested WordPress version of the added item.
	 * @param string|null $changelog  The changelog of the added item version.
	 *
	 * @return WP_Error|ItemVersionModel
	 */
	function bvlwark_add_item_version(
		int $item_id,
		string $version,
		?string $requires = null,
		?string $tested = null,
		?string $changelog = null,
	): WP_Error|ItemVersionModel {
		$item = bvlwark_get_item( $item_id );

		if ( is_wp_error( $item ) ) {
			return $item;
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

		return ItemVersionRepository::instance()->insert_one(
			array(
				'item_id'   => $item_id,
				'version'   => $version,
				'requires'  => $requires,
				'tested'    => $tested,
				'changelog' => $changelog,
			)
		);
	}
}
