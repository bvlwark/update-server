<?php

use BVLWARK\UpdateServer\Models\ApiKeyModel;
use BVLWARK\UpdateServer\Repositories\ApiKeyRepository;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bvlwark_add_api_key' ) ) {
	/**
	 * Adds a new pair of API keys to the database.
	 *
	 * @param int         $user_id         WordPress User ID. Owner of the API keys.
	 * @param string      $description     Description of the API keys.
	 * @param string      $permissions     Either "read", "write" or "read_write".
	 * @param null|string $consumer_key    The API consumer key. Will be generated if not provided.
	 * @param null|string $consumer_secret The API consumer secret. Will be generated if not provided.
	 *
	 * @return WP_Error|ApiKeyModel
	 */
	function bvlwark_add_api_key(
		int $user_id,
		string $description,
		string $permissions,
		?string $consumer_key = null,
		?string $consumer_secret = null
	): WP_Error|ApiKeyModel {
		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return new WP_Error( 404, __( 'The user could not be found.', 'bvlwark-update-server' ) );
		}

		if ( ! in_array( $permissions, array( 'read', 'write', 'read_write' ), true ) ) {
			return new WP_Error( 409, __( 'Permission must be read, write, or read_write.', 'bvlwark-update-server' ) );
		}

		if ( ! $consumer_key ) {
			$consumer_key = 'ck_' . bvlwark_rand_hash();
		}

		if ( ! $consumer_secret ) {
			$consumer_secret = 'cs_' . bvlwark_rand_hash();
		}

		/** @var ?ApiKeyModel $api_key */
		$api_key = ApiKeyRepository::instance()->insert_one(
			array(
				'user_id'         => $user_id,
				'description'     => $description,
				'permissions'     => $permissions,
				'consumer_key'    => bvlwark_api_hash( $consumer_key ),
				'consumer_secret' => $consumer_secret,
				'truncated_key'   => substr( $consumer_key, -7 ),
			)
		);

		if ( ! $api_key ) {
			return new WP_Error( 500, __( 'The API key could not be added.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_add_api_key', $api_key );
	}
}

if ( ! function_exists( 'bvlwark_get_api_key' ) ) {
	/**
	 * Returns a pair of API keys.
	 *
	 * @param int $id BVLWARK API Key ID.
	 *
	 * @return WP_Error|ApiKeyModel
	 */
	function bvlwark_get_api_key( int $id ): WP_Error|ApiKeyModel {
		/** @var ?ApiKeyModel $api_key */
		$api_key = ApiKeyRepository::instance()->find_one( $id );

		if ( ! $api_key ) {
			return new WP_Error( 404, __( 'The API key could not be found', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_get_api_key', $api_key );
	}
}

if ( ! function_exists( 'bvlwark_get_api_keys' ) ) {
	/**
	 * Retrieves multiple API key pairs.
	 *
	 * @param array $where    Key/value pairs of column names and their value
	 *                        to filter results by.
	 * @param array $order_by Key/value pairs of column names and their value
	 *                        to sort results by.
	 * @param int   $limit    How many rows should be fetched. Fetches all rows
	 *                        if -1 is passed.
	 * @param int   $offset   By how many rows the results should be offset.
	 *
	 * @return ApiKeyModel[]
	 */
	function bvlwark_get_api_keys(
		array $where = array(),
		array $order_by = array(),
		int $limit = 10,
		int $offset = 0
	): array {
		/** @var ApiKeyModel[] $api_keys */
		$api_keys = ApiKeyRepository::instance()->find_many( $where, $order_by, $limit, $offset );

		return apply_filters( 'bvlwark_get_api_keys', $api_keys );
	}
}

if ( ! function_exists( 'bvlwark_update_api_key' ) ) {
	/**
	 * Updates the specified API key.
	 *
	 * @param int   $id         API Key pair ID.
	 * @param array $data       Key/value pairs of the generator table column
	 *                          names as keys and their respective values.
	 *
	 * @return WP_Error|ApiKeyModel
	 */
	function bvlwark_update_api_key( int $id, array $data ): WP_Error|ApiKeyModel {
		/** @var ?ApiKeyModel $old_api_key */
		$old_api_key = ApiKeyRepository::instance()->find_one( $id );

		if ( ! $old_api_key ) {
			return new WP_Error( 404, __( 'The API key could not be found.', 'bvlwark-update-server' ) );
		}

		$update_data = array();

		if ( isset( $data['user_id'] ) ) {
			$user = get_userdata( (int) $data['user_id'] );

			if ( ! $user ) {
				return new WP_Error( 404, __( 'The user could not be found.', 'bvlwark-update-server' ) );
			} else {
				$update_data['user_id'] = $user->ID;
			}
		}

		if ( isset( $data['permissions'] ) ) {
			if ( ! in_array( $data['permissions'], array( 'read', 'write', 'read_write' ), true ) ) {
				return new WP_Error( 409, __( 'Permission must be read, write, or read_write.', 'bvlwark-update-server' ) );
			} else {
				$update_data['permissions'] = $data['permissions'];
			}
		}

		if ( isset( $data['description'] ) ) {
			$update_data['description'] = $data['description'];
		}

		/** @var ?ApiKeyModel $api_key */
		$api_key = ApiKeyRepository::instance()->update_one( $id, $update_data );

		if ( ! $api_key ) {
			return new WP_Error( 500, __( 'The API key could not be updated.', 'bvlwark-update-server' ) );
		}

		return apply_filters( 'bvlwark_update_api_key', $api_key );
	}
}

if ( ! function_exists( 'bvlwark_get_api_key_count' ) ) {
	/**
	 * Retrieves the license count.
	 *
	 * @param array $where    Key/value pairs of column names and their value
	 *                        to filter results by.
	 *
	 * @return int
	 */
	function bvlwark_get_api_key_count( array $where = array() ): int {
		$count = ApiKeyRepository::instance()->count( $where );

		return apply_filters( 'bvlwark_get_api_key_count', $count );
	}
}

if ( ! function_exists( 'bvlwark_delete_api_key' ) ) {
	/**
	 * Deletes an API key pair from the database. Returns the deleted API key
	 * pair on success, or an WP_Error object if the API key pair could not be
	 * deleted.
	 *
	 * @param int $id API key pair ID.
	 *
	 * @return WP_Error|ApiKeyModel
	 */
	function bvlwark_delete_api_key( int $id ): WP_Error|ApiKeyModel {
		/** @var ?ApiKeyModel $api_key */
		$api_key = ApiKeyRepository::instance()->find_one( $id );

		if ( ! $api_key ) {
			return new WP_Error( 404, __( 'The API key could not be found.', 'bvlwark-update-server' ) );
		}

		$deleted = ApiKeyRepository::instance()->delete( (array) $id );

		if ( ! $deleted ) {
			return new WP_Error( 500, 'The API key pair could not be deleted.' );
		}

		return apply_filters( 'bvlwark_delete_api_key', $deleted );
	}
}
