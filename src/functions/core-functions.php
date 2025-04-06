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

if ( ! function_exists( 'bvlwark_safe_date_time' ) ) {
	/**
	 * Returns null instead of throwing an exception when instantiating a
	 * DateTime object.
	 *
	 * @param string            $datetime The datetime string. Defaults to 'now'.
	 * @param DateTimeZone|null $timezone Optional DateTimeZone object.
	 *
	 * @return DateTime|null
	 */
	function bvlwark_safe_date_time( string $datetime = 'now', DateTimeZone $timezone = null ): ?DateTime {
		try {
			return new DateTime( $datetime, $timezone );
		} catch ( Exception $e ) {
			return null;
		}
	}
}

if ( ! function_exists( 'bvlwark_vault_directory_path' ) ) {
	/**
	 * Returns the server path to the plugin's vault directory.
	 *
	 * @return string
	 */
	function bvlwark_vault_directory_path(): string {
		$vault_dir_path = trailingslashit( WP_CONTENT_DIR ) . 'bvlwark';

		return apply_filters( 'bvlwark_vault_directory_path', $vault_dir_path );
	}
}

if ( ! function_exists( 'bvlwark_clean_string_request_value' ) ) {
	/**
	 * Safely returns and sanitizes a string value from the global $_REQUEST
	 * object.
	 *
	 * @param string     $key           The key of the value in the $_REQUEST object.
	 * @param mixed|null $default_value The default return value.
	 *
	 * @return mixed
	 */
	function bvlwark_clean_string_request_value( string $key, mixed $default_value = null ): mixed {
		return ! empty( $_REQUEST[ $key ] )
			? sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) )
			: $default_value;
	}
}

if ( ! function_exists( 'bvlwark_clean_int_request_value' ) ) {
	/**
	 * Safely returns and sanitizes an integer value from the global $_REQUEST
	 * object.
	 *
	 * @param string     $key           The key of the value in the $_REQUEST object.
	 * @param mixed|null $default_value The default return value.
	 *
	 * @return mixed
	 */
	function bvlwark_clean_int_request_value( string $key, mixed $default_value = null ): mixed {
		return ! empty( $_REQUEST[ $key ] )
			? (int) $_REQUEST[ $key ]
			: $default_value;
	}
}

if ( ! function_exists( 'bvlwark_clean_html_request_value' ) ) {
	/**
	 * Safely returns and sanitizes a html value from the global $_REQUEST
	 * object.
	 *
	 * @param string     $key           The key of the value in the $_REQUEST object.
	 * @param mixed|null $default_value The default return value.
	 *
	 * @return mixed
	 */
	function bvlwark_clean_html_request_value( string $key, mixed $default_value = null ): mixed {
		return ! empty( $_REQUEST[ $key ] )
			? wp_kses( wp_unslash( $_REQUEST[ $key ] ), bvlwark_allowed_html() )
			: $default_value;
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

if ( ! function_exists( 'bvlwark_allowed_html' ) ) {
	/**
	 * Returns a wp_kses compatible list of allowed HTML tags for output
	 * sanitization.
	 *
	 * @return array
	 */
	function bvlwark_allowed_html(): array {
		$allowed = array(
			'a'        => array(
				'class'  => true,
				'data-*' => true,
				'href'   => true,
				'rel'    => true,
				'target' => true,
			),
			'b'        => true,
			'br'       => true,
			'code'     => array(
				'class'  => true,
				'data-*' => true,
			),
			'div'      => array(
				'id'     => true,
				'class'  => true,
				'style'  => true,
				'data-*' => true,
			),
			'em'       => true,
			'fieldset' => true,
			'i'        => true,
			'img'      => array(
				'alt'   => true,
				'class' => true,
				'src'   => true,
			),
			'input'    => array(
				'checked'     => true,
				'class'       => true,
				'disabled'    => true,
				'id'          => true,
				'max'         => true,
				'min'         => true,
				'name'        => true,
				'placeholder' => true,
				'readonly'    => true,
				'required'    => true,
				'step'        => true,
				'type'        => true,
				'value'       => true,
			),
			'label'    => array(
				'class' => true,
				'for'   => true,
			),
			'li'       => array(
				'for'   => true,
				'class' => true,
			),
			'nav'      => array(
				'class' => true,
			),
			'option'   => array(
				'value'    => true,
				'selected' => true,
			),
			'p'        => array(
				'class' => true,
			),
			'select'   => array(
				'class'    => true,
				'id'       => true,
				'name'     => true,
				'style'    => true,
				'readonly' => true,
				'disabled' => true,
				'required' => true,
			),
			'span'     => array(
				'style' => true,
				'class' => true,
			),
			'strong'   => true,
			'table'    => array(
				'class' => true,
				'style' => true,
			),
			'tbody'    => true,
			'td'       => array(
				'class' => true,
				'id'    => true,
			),
			'textarea' => array(
				'id'       => true,
				'name'     => true,
				'cols'     => true,
				'rows'     => true,
				'readonly' => true,
				'disabled' => true,
				'required' => true,
			),
			'th'       => array(
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'thead'    => true,
			'tr'       => array(
				'class' => true,
				'id'    => true,
			),
			'ul'       => array(
				'class' => true,
			),
		);

		return apply_filters( 'bvlwark_allowed_html', $allowed );
	}
}
