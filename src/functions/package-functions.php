<?php

use BVLWARK\UpdateServer\Models\PackageModel;
use BVLWARK\UpdateServer\PackageTypeEnum;
use BVLWARK\UpdateServer\Repositories\OperatorEnum;
use BVLWARK\UpdateServer\Repositories\PackageRepository;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bvlwark_add_package' ) ) {
	/**
	 * Adds a new package to the database.
	 *
	 * @param string $name        The package name.
	 * @param string $slug        The package slug.
	 * @param string $type        The package type: either "plugin" or "theme".
	 * @param string $description The package description.
	 * @param bool   $is_public   Identifies whether the package is publicly available.
	 *
	 * @return WP_Error|PackageModel
	 */
	function bvlwark_add_package(
		string $name,
		string $slug,
		string $type,
		string $description,
		bool $is_public,
	): WP_Error|PackageModel {
		if ( ! in_array( strtolower( $type ), PackageTypeEnum::$status, true ) ) {
			return new WP_Error( 409, __( 'Invalid package type.', 'bvlwark-update-server' ) );
		}

		if ( strlen( $name ) > 255 ) {
			return new WP_Error( 409, __( '"Name" cannot exceed 255 characters.', 'bvlwark-update-server' ) );
		}

		if ( strlen( $slug ) > 200 ) {
			return new WP_Error( 409, __( '"Slug" cannot exceed 200 characters.', 'bvlwark-update-server' ) );
		}

		$existing_package = PackageRepository::instance()->find_one_by(
			array(
				'slug' => $slug,
			)
		);

		if ( $existing_package ) {
			return new WP_Error( 409, __( 'A package with this slug already exists.', 'bvlwark-update-server' ) );
		}

		/** @var null|PackageModel $package */
		$package = PackageRepository::instance()->insert_one(
			array(
				'name'        => $name,
				'slug'        => $slug,
				'type'        => $type,
				'description' => $description,
				'is_public'   => $is_public,
			)
		);

		if ( ! $package ) {
			return new WP_Error( 500, __( 'The package could not be created.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_add_package', $package );
	}
}

if ( ! function_exists( 'bvlwark_get_package' ) ) {
	/**
	 * Retrieves a single package from the database.
	 *
	 * @param int $id The package ID.
	 *
	 * @return WP_Error|PackageModel
	 */
	function bvlwark_get_package( int $id ): WP_Error|PackageModel {
		$package = bvlwark_get_package_by( array( 'id' => $id ) );

		return apply_filters( 'bvlwark_get_package', $package );
	}
}

if ( ! function_exists( 'bvlwark_get_package_by' ) ) {
	/**
	 * Retrieves a single package from the database by the search query.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter by.
	 *
	 * @return WP_Error|PackageModel
	 */
	function bvlwark_get_package_by( array $where = array() ): WP_Error|PackageModel {
		/** @var null|PackageModel $package */
		$package = PackageRepository::instance()->find_one_by( $where );

		if ( ! $package ) {
			return new WP_Error( 404, __( 'The package could not be found.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_get_package_by', $package );
	}
}

if ( ! function_exists( 'bvlwark_get_packages' ) ) {
	/**
	 * Retrieves multiple packages.
	 *
	 * @param array $where    Key/value pairs of column names and their value
	 *                        to filter results by.
	 * @param array $order_by Key/value pairs of column names and their value
	 *                        to sort results by.
	 * @param int   $limit    How many rows should be fetched. Fetches all rows
	 *                        if -1 is passed.
	 * @param int   $offset   By how many rows the results should be offset.
	 *
	 * @return PackageModel[]
	 */
	function bvlwark_get_packages(
		array $where = array(),
		array $order_by = array(),
		int $limit = 10,
		int $offset = 0
	): array {
		/** @var PackageModel[] $packages */
		$packages = PackageRepository::instance()->find_many( $where, $order_by, $limit, $offset );

		return apply_filters( 'bvlwark_get_packages', $packages );
	}
}

if ( ! function_exists( 'bvlwark_update_package' ) ) {
	/**
	 * Updates an package.
	 *
	 * @param int   $id   The package ID.
	 * @param array $data Key/value pairs of the table column names as keys and
	 *                    their respective values.
	 *
	 * @return WP_Error|PackageModel
	 */
	function bvlwark_update_package( int $id, array $data ): WP_Error|PackageModel {
		// TODO: Update .zip files when the slug changes.
		$update_data = array();

		/** @var null|PackageModel $old_package */
		$old_package = PackageRepository::instance()->find_one( $id );

		if ( ! $old_package ) {
			return new WP_Error( 400, __( 'The package could not be found.', 'bvlwark-update-server' ) );
		}

		// Check if the name is empty.
		if ( array_key_exists( 'name', $data ) && empty( $data['name'] ) ) {
			return new WP_Error( 409, __( 'The name cannot be empty.', 'bvlwark-update-server' ) );
		}

		if ( array_key_exists( 'slug', $data ) ) {
			// Check if the slug is empty.
			if ( empty( $data['slug'] ) ) {
				return new WP_Error( 409, __( 'The slug cannot be empty.', 'bvlwark-update-server' ) );
			}

			// Check for existing slugs.
			/** @var null|PackageModel $existing_package */
			$existing_package = PackageRepository::instance()->find_one_by(
				array(
					'slug' => $data['slug'],
					'id'   => array(
						OperatorEnum::NOT_LIKE => $id,
					),
				)
			);

			if ( $existing_package ) {
				return new WP_Error( 409, __( 'The slug is already assigned to another package.', 'bvlwark-update-server' ) );
			}

			$update_data['slug'] = $data['slug'];
		}

		if ( ! empty( $data['type'] ) ) {
			$type = strtolower( $data['type'] );

			if ( ! in_array( $type, PackageTypeEnum::$enum_array, true ) ) {
				return new WP_Error( 409, __( 'Invalid package type. Must be either "plugin" or "theme".', 'bvlwark-update-server' ) );
			}

			$update_data['type'] = $type;
		}

		if ( array_key_exists( 'description', $data ) ) {
			$update_data['description'] = $data['description'];
		}

		// Normalize the "is_public" value.
		if ( array_key_exists( 'is_public', $data ) ) {
			$update_data['is_public'] = (bool) $data['is_public'];
		}

		/** @var null|PackageModel $package */
		$package = PackageRepository::instance()->update_one( $id, $update_data );

		if ( ! $package ) {
			return new WP_Error( 500, __( 'The package could not be updated.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_update_package', $package );
	}
}

if ( ! function_exists( 'bvlwark_get_package_count' ) ) {
	/**
	 * Retrieves the package count.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter results by.
	 *
	 * @return int
	 */
	function bvlwark_get_package_count( array $where = array() ): int {
		$count = PackageRepository::instance()->count( $where );

		return apply_filters( 'bvlwark_get_package_count', $count );
	}
}

if ( ! function_exists( 'bvlwark_delete_package' ) ) {
	/**
	 * Deletes a package from the database. Returns the deleted package on
	 * success, or a WP_Error object if the package could not be deleted.
	 *
	 * @param int $id The package ID.
	 *
	 * @return WP_Error|PackageModel
	 */
	function bvlwark_delete_package( int $id ): WP_Error|PackageModel {
		/** @var null|PackageModel $package */
		$package = PackageRepository::instance()->find_one( $id );

		if ( ! $package ) {
			return new WP_Error( 404, __( 'The package could not be found.', 'bvlwark-update-server' ) );
		}

		$deleted = PackageRepository::instance()->delete( (array) $id );

		if ( ! $deleted ) {
			return new WP_Error( 500, __( 'The package could not be deleted.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_delete_package', $package );
	}
}
