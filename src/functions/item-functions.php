<?php

use BVLWARK\UpdateServer\ItemTypeEnum;
use BVLWARK\UpdateServer\Models\ItemModel;
use BVLWARK\UpdateServer\Repositories\ItemRepository;
use BVLWARK\UpdateServer\Repositories\OperatorEnum;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bvlwark_add_item' ) ) {
	/**
	 * Adds a new item to the database.
	 *
	 * @param string $name        The item name.
	 * @param string $slug        The item slug.
	 * @param string $type        Either "plugin" or "theme".
	 * @param string $description The item description.
	 * @param bool   $is_public   Identifies whether the item is publicly available.
	 *
	 * @return WP_Error|ItemModel
	 */
	function bvlwark_add_item(
		string $name,
		string $slug,
		string $type,
		string $description,
		bool $is_public,
	): WP_Error|ItemModel {
		if ( ! in_array( strtolower( $type ), ItemTypeEnum::$status, true ) ) {
			return new WP_Error( 409, __( 'Invalid item type.', 'bvlwark-update-server' ) );
		}

		if ( strlen( $name ) > 255 ) {
			return new WP_Error( 409, __( '"Name" cannot be longer than 255 characters.', 'bvlwark-update-server' ) );
		}

		if ( strlen( $slug ) > 200 ) {
			return new WP_Error( 409, __( '"Slug" cannot be longer than 200 characters.', 'bvlwark-update-server' ) );
		}

		$existing_item = ItemRepository::instance()->find_one_by(
			array(
				'slug' => $slug,
			)
		);

		if ( $existing_item ) {
			return new WP_Error( 409, __( 'An item with this slug already exists.', 'bvlwark-update-server' ) );
		}

		/** @var null|ItemModel $item */
		$item = ItemRepository::instance()->insert_one(
			array(
				'name'        => $name,
				'slug'        => $slug,
				'type'        => $type,
				'description' => $description,
				'is_public'   => $is_public,
			)
		);

		if ( ! $item ) {
			return new WP_Error( 500, __( 'The item could not be created.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_add_item', $item );
	}
}

if ( ! function_exists( 'bvlwark_get_item' ) ) {
	/**
	 * Retrieves a single item from the database.
	 *
	 * @param int $id The item ID.
	 *
	 * @return WP_Error|ItemModel
	 */
	function bvlwark_get_item( int $id ): WP_Error|ItemModel {
		$item = bvlwark_get_item_by( array( 'id' => $id ) );

		return apply_filters( 'bvlwark_get_item', $item );
	}
}

if ( ! function_exists( 'bvlwark_get_item_by' ) ) {
	/**
	 * Retrieves a single item from the database by the search query.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter by.
	 *
	 * @return WP_Error|ItemModel
	 */
	function bvlwark_get_item_by( array $where = array() ): WP_Error|ItemModel {
		/** @var null|ItemModel $item */
		$item = ItemRepository::instance()->find_one_by( $where );

		if ( ! $item ) {
			return new WP_Error( 404, __( 'The item could not be found.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_get_item_by', $item );
	}
}

if ( ! function_exists( 'bvlwark_get_items' ) ) {
	/**
	 * Retrieves multiple items.
	 *
	 * @param array $where    Key/value pairs of column names and their value
	 *                        to filter results by.
	 * @param array $order_by Key/value pairs of column names and their value
	 *                        to sort results by.
	 * @param int   $limit    How many rows should be fetched. Fetches all rows
	 *                        if -1 is passed.
	 * @param int   $offset   By how many rows the results should be offset.
	 *
	 * @return ItemModel[]
	 */
	function bvlwark_get_items(
		array $where = array(),
		array $order_by = array(),
		int $limit = 10,
		int $offset = 0
	): array {
		/** @var ItemModel[] $items */
		$items = ItemRepository::instance()->find_many( $where, $order_by, $limit, $offset );

		return apply_filters( 'bvlwark_get_items', $items );
	}
}

if ( ! function_exists( 'bvlwark_update_item' ) ) {
	/**
	 * Updates an item.
	 *
	 * @param int   $id   Item ID.
	 * @param array $data Key/value pairs of the table column names as keys and
	 *                    their respective values.
	 *
	 * @return WP_Error|ItemModel
	 */
	function bvlwark_update_item( int $id, array $data ): WP_Error|ItemModel {
		// TODO: Update .zip files when the slug changes.
		$update_data = array();

		/** @var null|ItemModel $old_item */
		$old_item = ItemRepository::instance()->find_one( $id );

		if ( ! $old_item ) {
			return new WP_Error( 400, __( 'The item could not be found.', 'bvlwark-update-server' ) );
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
			/** @var null|ItemModel $existing_item */
			$existing_item = ItemRepository::instance()->find_one_by(
				array(
					'slug' => $data['slug'],
					'id'   => array(
						OperatorEnum::NOT_LIKE => $id,
					),
				)
			);

			if ( $existing_item ) {
				return new WP_Error( 409, __( 'The slug is already assigned to another item.', 'bvlwark-update-server' ) );
			}

			$update_data['slug'] = $data['slug'];
		}

		if ( ! empty( $data['type'] ) ) {
			$type = strtolower( $data['type'] );

			if ( ! in_array( $type, ItemTypeEnum::$enum_array, true ) ) {
				return new WP_Error( 409, __( 'Invalid item type. Must be either "plugin" or "theme".', 'bvlwark-update-server' ) );
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

		/** @var null|ItemModel $item */
		$item = ItemRepository::instance()->update_one( $id, $update_data );

		if ( ! $item ) {
			return new WP_Error( 500, __( 'The item could not be updated.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_update_item', $item );
	}
}

if ( ! function_exists( 'bvlwark_get_item_count' ) ) {
	/**
	 * Retrieves the item count.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter results by.
	 *
	 * @return int
	 */
	function bvlwark_get_item_count( array $where = array() ): int {
		$count = ItemRepository::instance()->count( $where );

		return apply_filters( 'bvlwark_get_item_count', $count );
	}
}

if ( ! function_exists( 'bvlwark_delete_item' ) ) {
	/**
	 * Deletes an item from the database. Returns the deleted item on
	 * success, or an WP_Error object if the item could not be deleted.
	 *
	 * @param int $id Item ID.
	 *
	 * @return WP_Error|ItemModel
	 */
	function bvlwark_delete_item( int $id ): WP_Error|ItemModel {
		/** @var null|ItemModel $item */
		$item = ItemRepository::instance()->find_one( $id );

		if ( ! $item ) {
			return new WP_Error( 404, __( 'The item could not be found.', 'bvlwark-update-server' ) );
		}

		$deleted = ItemRepository::instance()->delete( (array) $id );

		if ( ! $deleted ) {
			return new WP_Error( 500, __( 'The item could not be deleted.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_delete_item', $item );
	}
}
