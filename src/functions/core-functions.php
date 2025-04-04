<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bvlwark_clean_string' ) ) {
	/**
	 * @param mixed $input The input string.
	 *
	 * @return string
	 */
	function bvlwark_clean_string( mixed $input ): string {
		return sanitize_text_field( wp_unslash( $input ) );
	}
}

if ( ! function_exists( 'bvlwark_get_current_action' ) ) {
	/**
	 * Returns the string value of the "action" GET parameter.
	 *
	 * @param string $default_action The default action.
	 *
	 * @return string
	 */
	function bvlwark_get_current_action( string $default_action = '' ): string {
		$action = $default_action;

		if ( ! isset( $_GET['action'] ) || ! is_string( $_GET['action'] ) ) {
			return $action;
		}

		return sanitize_text_field( wp_unslash( $_GET['action'] ) );
	}
}

if ( ! function_exists( 'bvlwark_get_current_tab' ) ) {
	/**
	 * Retrieves the currently active tab.
	 *
	 * @param string|null $default_value The default value.
	 *
	 * @return string|null
	 */
	function bvlwark_get_current_tab( ?string $default_value = null ): ?string {
		if ( ! empty( $_GET['tab'] ) ) {
			return sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		}

		return $default_value;
	}
}

if ( ! function_exists( 'bvlwark_get_rest_api_keys_permissions' ) ) {
	/**
	 * Returns an array of available permissions which can be set for REST API
	 * keys.
	 *
	 * @return array
	 */
	function bvlwark_get_rest_api_keys_permissions(): array {
		$permissions = array(
			array(
				'label' => __( 'Read', 'bvlwark-update-server' ),
				'value' => 'read',
			),
			array(
				'label' => __( 'Write', 'bvlwark-update-server' ),
				'value' => 'write',
			),
			array(
				'label' => __( 'Read/Write', 'bvlwark-update-server' ),
				'value' => 'read_write',
			),
		);

		return apply_filters( 'bvlwark_get_rest_api_keys_permissions', $permissions );
	}
}

if ( ! function_exists( 'bvlwark_rand_hash' ) ) {
	/**
	 * Generates a random hash.
	 *
	 * @param int $length Length of the hash.
	 *
	 * @return string
	 */
	function bvlwark_rand_hash( int $length = 20 ): string {
		$hash = bin2hex( openssl_random_pseudo_bytes( $length ) );

		return apply_filters( 'bvlwark_rand_hash', $hash );
	}
}

if ( ! function_exists( 'bvlwark_api_hash' ) ) {
	/**
	 * @param string $data The value to hash.
	 *
	 * @return string
	 */
	function bvlwark_api_hash( string $data ): string {
		return hash_hmac( 'sha256', $data, 'bvlwark-api' );
	}
}

if ( ! function_exists( 'bvlwark_get_nonce' ) ) {
	/**
	 * Returns a sanitized and un-slashed nonce from the request.
	 *
	 * @param string $name Nonce key in the request.
	 *
	 * @return string
	 */
	function bvlwark_get_nonce( string $name = '_wpnonce' ): string {
		return isset( $_REQUEST[ $name ] )
			? sanitize_text_field( wp_unslash( $_REQUEST[ $name ] ) )
			: '';
	}
}
