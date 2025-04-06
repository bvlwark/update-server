<?php

use BVLWARK\UpdateServer\HtmlRenderer;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bvlwark_render_admin_input' ) ) {
	/**
	 * Renders an input field.
	 *
	 * @param array $args Input field data.
	 *
	 * @return void
	 */
	function bvlwark_render_admin_input( array $args ): void {
		$html = apply_filters( 'bvlwark_render_admin_input', HtmlRenderer::render_admin_input( $args ), $args );

		echo wp_kses( $html, bvlwark_allowed_html() );
	}
}

if ( ! function_exists( 'bvlwark_render_admin_select' ) ) {
	/**
	 * Renders a select field.
	 *
	 * @param array $args Input field data.
	 *
	 * @return void
	 */
	function bvlwark_render_admin_select( array $args ): void {
		$html = apply_filters( 'bvlwark_render_admin_select', HtmlRenderer::render_admin_select( $args ), $args );

		echo wp_kses( $html, bvlwark_allowed_html() );
	}
}


if ( ! function_exists( 'bvlwark_render_admin_textarea' ) ) {
	/**
	 * Renders a textarea field.
	 *
	 * @param array $args Textarea field data.
	 *
	 * @return void
	 */
	function bvlwark_render_admin_textarea( array $args ): void {
		$html = apply_filters( 'bvlwark_render_admin_textarea', HtmlRenderer::render_admin_textarea( $args ), $args );

		echo wp_kses( $html, bvlwark_allowed_html() );
	}
}

if ( ! function_exists( 'bvlwark_render_admin_rich_text' ) ) {
	/**
	 * Renders a textarea as a rich text field.
	 *
	 * @param array $args Rich text field data.
	 *
	 * @return void
	 */
	function bvlwark_render_admin_rich_text( array $args ): void {
		HtmlRenderer::render_admin_rich_text( $args );
	}
}

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
		string $default_path = BVLWARK_TEMPLATES_DIR
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


if ( ! function_exists( 'bvlwark_get_asset_url' ) ) {
	/**
	 * Returns the URL of an asset file.
	 *
	 * @param string $file_name File name of the general asset.
	 *
	 * @return string
	 */
	function bvlwark_get_asset_url( string $file_name ): string {
		return apply_filters( 'bvlwark_asset_url', BVLWARK_ASSETS_URL . $file_name, $file_name );
	}
}

if ( ! function_exists( 'bvlwark_get_js_url' ) ) {
	/**
	 * @param string $file_name File name of the JavaScript asset.
	 *
	 * @return string
	 */
	function bvlwark_get_js_url( string $file_name ): string {
		return apply_filters( 'bvlwark_js_url', BVLWARK_JS_URL . $file_name, $file_name );
	}
}

if ( ! function_exists( 'bvlwark_get_css_url' ) ) {
	/**
	 * @param string $file_name File name of the CSS asset.
	 *
	 * @return string
	 */
	function bvlwark_get_css_url( string $file_name ): string {
		return apply_filters( 'bvlwark_css_url', BVLWARK_CSS_URL . $file_name, $file_name );
	}
}

if ( ! function_exists( 'bvlwark_get_img_url' ) ) {
	/**
	 * @param string $file_name File name of the image asset.
	 *
	 * @return string
	 */
	function bvlwark_get_img_url( string $file_name ): string {
		return apply_filters( 'bvlwark_get_img_url', BVLWARK_IMG_URL . $file_name, $file_name );
	}
}
