<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bvlwark_update_server_clean_string' ) ) {
	/**
	 * @param mixed $input The input string.
	 *
	 * @return string
	 */
	function bvlwark_update_server_clean_string( mixed $input ): string {
		return sanitize_text_field( wp_unslash( $input ) );
	}
}
