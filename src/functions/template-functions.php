<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bvlwark_get_template_html' ) ) {
	/**
	 * Includes a HTML template file.
	 *
	 * @param string $template_name Name of the template file.
	 * @param array  $args          Variables passed to the template file.
	 * @param string $template_path Path to template file.
	 * @param string $default_path  Default path.
	 *
	 * @return void
	 */
	function bvlwark_get_template_html(
		string $template_name,
		array $args = array(),
		string $template_path = '',
		string $default_path = BVLWARK_UPDATE_SERVER_TEMPLATES_DIR
	): void {
		$stylesheet_dir       = get_stylesheet_directory();
		$overwritten_template = $stylesheet_dir . '/bvlwark-update-server/' . $template_name;

		if ( file_exists( $overwritten_template ) ) {
			// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			extract( $args );
			require $overwritten_template;
			return;
		}

		$template = $default_path . $template_name;

		if ( file_exists( $template ) ) {
			// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			extract( $args );
			require $template;
		}
	}
}
