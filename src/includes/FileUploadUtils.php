<?php

namespace BVLWARK\UpdateServer;

use WP_Error;

defined( 'ABSPATH' ) || exit;

class FileUploadUtils {
	/**
	 * @param string $slug    Package slug.
	 * @param string $version Package version.
	 *
	 * @return WP_Error|true
	 */
	public static function handle_package_zip_upload( string $slug, string $version ): WP_Error|true {
		$file = ! empty( $_FILES['package_zip'] )
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			? $_FILES['package_zip']
			: null;

		if ( $file['type'] !== 'application/zip' ) {
			return new WP_Error( 409, 'The uploaded file must be a zip archive.' );
		}

		$file_name = sanitize_file_name( $file['name'] );
		$ext       = pathinfo( $file_name, PATHINFO_EXTENSION );

		if ( $ext !== 'zip' ) {
			return new WP_Error( 409, 'The uploaded file has an invalid extension.' );
		}

		$vault_dir_path = bvlwark_update_server_vault_directory_path();
		$new_file_name  = "{$slug}_$version.zip";

		if ( file_exists( $vault_dir_path . $new_file_name ) ) {
			return new WP_Error( 409, 'A package with the same version already exists.' );
		}

		$upload = move_uploaded_file( $file['tmp_name'], $vault_dir_path . '/' . $new_file_name );

		if ( ! $upload ) {
			return new WP_Error( 409, 'The package file could not be uploaded.' );
		}

		return true;
	}
}
