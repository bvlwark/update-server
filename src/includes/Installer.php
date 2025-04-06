<?php
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

namespace BVLWARK\UpdateServer;

use Exception;
use function dbDelta;

defined( 'ABSPATH' ) || exit;

class Installer {
	const int DB_VERSION = 100;

	const string PACKAGE_TABLE_NAME = 'bvlwark_update_server_package';

	const string PACKAGE_VERSION_TABLE_NAME = 'bvlwark_update_server_package_version';

	const string API_KEY_TABLE_NAME = 'bvlwark_update_server_api_key';

	/**
	 * Installation script.
	 *
	 * @throws Exception When the PHP version is too low.
	 */
	public static function install(): void {
		self::check_requirements();
		self::create_tables();
		self::set_default_settings();
		self::create_roles();
		self::create_vault_directory();
	}

	/**
	 * Deactivation script.
	 */
	public static function deactivate() {
		// Nothing for now...
	}

	/**
	 * Uninstall script.
	 */
	public static function uninstall(): void {
		global $wpdb;

		$tables = array(
			$wpdb->prefix . self::PACKAGE_TABLE_NAME,
			$wpdb->prefix . self::PACKAGE_VERSION_TABLE_NAME,
			$wpdb->prefix . self::API_KEY_TABLE_NAME,
		);

		foreach ( $tables as $table ) {
			// phpcs:ignore
			$wpdb->query( "DROP TABLE IF EXISTS $table" );
		}

		self::remove_roles();

		foreach ( self::get_default_settings() as $group => $setting ) {
			delete_option( $group );
		}

		// After cleanup remove version reference.
		delete_option( 'bvlwark_update_server_db_version' );
	}

	/**
	 * Migration script.
	 */
	public static function migrate(): void {
		$current_db_version = get_option( 'bvlwark_update_server_db_version' );

		if ( $current_db_version < self::DB_VERSION ) {
			Migration::do( $current_db_version );
		}
	}

	/**
	 * Checks if all required plugin components are present.
	 *
	 * @throws Exception The PHP version is not correct.
	 */
	private static function check_requirements(): void {
		if ( version_compare( phpversion(), '8.3.0', '<' ) ) {
			throw new Exception( 'PHP 8.2 or lower detected. BVLWARK Update Server requires PHP 8.3 or greater.' );
		}
	}

	/**
	 * Create the necessary database tables.
	 */
	private static function create_tables(): void {
		global $wpdb;

		if ( ! function_exists( '\dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		dbDelta(
			$wpdb->prepare(
				"
				CREATE TABLE IF NOT EXISTS %i (
					`id`           BIGINT(20)  UNSIGNED  NOT NULL AUTO_INCREMENT,
					`name`         VARCHAR(255)          NOT NULL,
					`slug`         VARCHAR(200)          NOT NULL,
					`type`         VARCHAR(8)            NOT NULL,
					`description`  TEXT                  NOT NULL,
					`is_public`    TINYINT(1)            NOT NULL DEFAULT 1,
					`created_at`   DATETIME              DEFAULT CURRENT_TIMESTAMP,
					`created_by`   BIGINT(20)  UNSIGNED  NOT NULL,
					`updated_at`   DATETIME              NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
					`updated_by`   BIGINT(20)  UNSIGNED  NULL DEFAULT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY bvlwark_update_server_package_unique_slug (`slug`)
				) {$wpdb->get_charset_collate()};
				",
				$wpdb->prefix . self::PACKAGE_TABLE_NAME,
			)
		);

		dbDelta(
			$wpdb->prepare(
				"
				CREATE TABLE IF NOT EXISTS %i (
					`id`           BIGINT(20)  UNSIGNED  NOT NULL AUTO_INCREMENT,
					`package_id`   BIGINT(20)  UNSIGNED  NOT NULL,
					`version`      VARCHAR(50)           NOT NULL,
					`requires`     VARCHAR(50)           NULL DEFAULT NULL,
					`tested`       VARCHAR(50)           NULL DEFAULT NULL,
					`changelog`    TEXT                  NULL DEFAULT NULL,
					`created_at`   DATETIME              DEFAULT CURRENT_TIMESTAMP,
					`created_by`   BIGINT(20)  UNSIGNED  NOT NULL,
					`updated_at`   DATETIME              NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
					`updated_by`   BIGINT(20)  UNSIGNED  NULL DEFAULT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY bvlwark_update_server_package_version_unique_version (`package_id`, `version`)
				) {$wpdb->get_charset_collate()};
				",
				$wpdb->prefix . self::PACKAGE_VERSION_TABLE_NAME,
			)
		);

		dbDelta(
			$wpdb->prepare(
				"
				CREATE TABLE IF NOT EXISTS %i (
					`id`               BIGINT(20)    UNSIGNED  NOT NULL AUTO_INCREMENT,
					`user_id`          BIGINT(20)    UNSIGNED  NOT NULL,
					`description`      VARCHAR(200)            NULL DEFAULT NULL,
					`permissions`      VARCHAR(10)             NOT NULL,
					`consumer_key`     CHAR(64)                NOT NULL,
					`consumer_secret`  CHAR(43)                NOT NULL,
					`nonces`           LONGTEXT                NULL,
					`truncated_key`    CHAR(7)                 NOT NULL,
					`last_access`      DATETIME                NULL DEFAULT NULL,
					`created_at`       DATETIME                NOT NULL,
					`created_by`       BIGINT(20)    UNSIGNED  NOT NULL,
					`updated_at`       DATETIME                NULL DEFAULT NULL,
					`updated_by`       BIGINT(20)    UNSIGNED  NULL DEFAULT NULL,
					PRIMARY KEY (`id`),
					INDEX `bvlwark_update_server_api_keys_index_consumer_key` (`consumer_key`),
					INDEX `bvlwark_update_server_api_keys_index_consumer_secret` (`consumer_secret`)
				) {$wpdb->get_charset_collate()};
				",
				$wpdb->prefix . self::API_KEY_TABLE_NAME,
			)
		);
	}

	/**
	 * Set the default plugin options.
	 */
	private static function set_default_settings(): void {
		// Only update user settings if they don't exist already.
		foreach ( self::get_default_settings() as $group => $setting ) {
			if ( ! get_option( $group, false ) ) {
				update_option( $group, $setting );
			}
		}

		// Database version is always updated.
		update_option( 'bvlwark_update_server_db_version', self::DB_VERSION );
	}

	/**
	 * Returns an associative array of the default plugin settings. The key
	 * represents the setting group, and the value the individual settings
	 * fields with their corresponding values.
	 *
	 * @return array
	 */
	private static function get_default_settings(): array {
		return array();
	}

	/**
	 * Add plugin roles.
	 */
	private static function create_roles(): void {
		global $wp_roles;

		// Dummy gettext calls to get strings in the catalog.
		_x( 'BVLWARK Update Server Agent', 'User role', 'bvlwark-update-server' );
		_x( 'BVLWARK Update Server Manager', 'User role', 'bvlwark-update-server' );

		// Agent role.
		add_role( 'bvlwark_update_server_agent', 'BVLWARK Update Server Agent' );

		// Manager role.
		add_role(
			'bvlwark_update_server_manager',
			'BVLWARK Update Server Manager',
			array(
				'level_9'                => true,
				'level_8'                => true,
				'level_7'                => true,
				'level_6'                => true,
				'level_5'                => true,
				'level_4'                => true,
				'level_3'                => true,
				'level_2'                => true,
				'level_1'                => true,
				'level_0'                => true,
				'read'                   => true,
				'read_private_pages'     => true,
				'read_private_posts'     => true,
				'edit_posts'             => true,
				'edit_pages'             => true,
				'edit_published_posts'   => true,
				'edit_published_pages'   => true,
				'edit_private_pages'     => true,
				'edit_private_posts'     => true,
				'edit_others_posts'      => true,
				'edit_others_pages'      => true,
				'publish_posts'          => true,
				'publish_pages'          => true,
				'delete_posts'           => true,
				'delete_pages'           => true,
				'delete_private_pages'   => true,
				'delete_private_posts'   => true,
				'delete_published_pages' => true,
				'delete_published_posts' => true,
				'delete_others_posts'    => true,
				'delete_others_pages'    => true,
				'manage_categories'      => true,
				'manage_links'           => true,
				'moderate_comments'      => true,
				'upload_files'           => true,
				'export'                 => true,
				'import'                 => true,
				'list_users'             => true,
				'edit_theme_options'     => true,
			)
		);

		foreach ( self::get_rest_api_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'bvlwark_update_server_agent', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}

		foreach ( self::get_core_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'bvlwark_update_server_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Remove plugin roles
	 */
	private static function remove_roles(): void {
		global $wp_roles;

		foreach ( self::get_rest_api_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'bvlwark_update_server_agent', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}

		foreach ( self::get_core_capabilities() as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'bvlwark_update_server_manager', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}

		remove_role( 'bvlwark_update_server_agent' );
		remove_role( 'bvlwark_update_server_manager' );
	}

	/**
	 * Returns the plugin's core capabilities.
	 *
	 * @return array
	 */
	private static function get_core_capabilities(): array {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_bvlwark_update_server',
		);

		$capabilities['updates'] = array(
			'plugin_update_do'    => true,
			'plugin_update_check' => true,
			'theme_update_do'     => true,
			'theme_update_check'  => true,
		);

		return $capabilities;
	}

	/**
	 * Return's the plugin's REST API capabilities.
	 *
	 * @return array
	 */
	private static function get_rest_api_capabilities(): array {
		$capabilities = array(
			'updates' => array(
				'plugin_update_do',
				'plugin_update_check',
				'theme_update_do',
				'theme_update_check',
			),
		);

		return apply_filters( 'bvlwark_update_server_rest_api_capabilities', $capabilities );
	}

	/**
	 * Creates the /wp-content/bvlwark directory for storing uploaded ZIP files.
	 * Adds .htaccess and index.html to prevent public access.
	 *
	 * @return void
	 */
	private static function create_vault_directory(): void {
		$vault_dir   = trailingslashit( WP_CONTENT_DIR ) . 'bvlwark';
		$folder_path = apply_filters( 'bvlwark_update_server_vault_directory', $vault_dir );

		if ( ! is_dir( $folder_path ) ) {
			if ( ! wp_mkdir_p( $folder_path ) ) {
				return;
			}
		}

		// Block browser access: Apache.
		$htaccess_path = trailingslashit( $folder_path ) . '.htaccess';

		if ( ! file_exists( $htaccess_path ) ) {
			file_put_contents( $htaccess_path, "deny from all\n" );
		}

		// Block directory listing.
		$index_path = trailingslashit( $folder_path ) . 'index.html';

		if ( ! file_exists( $index_path ) ) {
			file_put_contents( $index_path, '' );
		}
	}
}
