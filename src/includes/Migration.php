<?php

namespace BVLWARK\UpdateServer;

defined( 'ABSPATH' ) || exit;

class Migration {
	/**
	 * Performs a database upgrade.
	 *
	 * @param int $old_db_version Old database version.
	 */
	public static function do( int $old_db_version ): void {
		$regex_filename = '/(\d{14})_(.*?)_(.*?)\.php/';

		foreach ( glob( BVLWARK_UPDATE_SERVER_MIGRATIONS_DIR . '*.php' ) as $file_name ) {
			if ( preg_match( $regex_filename, basename( $file_name ), $match ) ) {
				$file_basename    = $match[0];
				$file_date_time   = $match[1];
				$file_version     = (int) $match[2];
				$file_description = $match[3];

				if ( $file_version <= Installer::DB_VERSION && $file_version > $old_db_version ) {
					require_once $file_name;
				}
			}
		}

		update_option( 'bvlwark_update_server_db_version', Installer::DB_VERSION, true );
	}
}
