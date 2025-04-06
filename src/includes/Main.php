<?php

namespace BVLWARK\UpdateServer;

use BVLWARK\UpdateServer\Controllers\ItemController;

defined( 'ABSPATH' ) || exit;

final class Main extends AbstractSingleton {
	/**
	 * Main constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->init_constants();
		$this->init_hooks();

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Init plugin constants.
	 *
	 * @return void
	 */
	private function init_constants(): void {
		if ( ! defined( 'ABSPATH_LENGTH' ) ) {
			define( 'ABSPATH_LENGTH', strlen( ABSPATH ) );
		}

		define( 'BVLWARK_UPDATE_SERVER_ABSPATH', dirname( BVLWARK_UPDATE_SERVER_PLUGIN_FILE ) . '/src/' );
		define( 'BVLWARK_UPDATE_SERVER_PLUGIN_BASENAME', plugin_basename( BVLWARK_UPDATE_SERVER_PLUGIN_FILE ) );

		// Directories.
		define( 'BVLWARK_UPDATE_SERVER_ASSETS_DIR', BVLWARK_UPDATE_SERVER_ABSPATH . 'assets/' );
		define( 'BVLWARK_UPDATE_SERVER_LOG_DIR', BVLWARK_UPDATE_SERVER_ABSPATH . 'logs/' );
		define( 'BVLWARK_UPDATE_SERVER_TEMPLATES_DIR', BVLWARK_UPDATE_SERVER_ABSPATH . 'templates/' );
		define( 'BVLWARK_UPDATE_SERVER_MIGRATIONS_DIR', BVLWARK_UPDATE_SERVER_ABSPATH . 'migrations/' );

		// URLs.
		define( 'BVLWARK_UPDATE_SERVER_ASSETS_URL', BVLWARK_UPDATE_SERVER_PLUGIN_URL . 'assets/' );
		define( 'BVLWARK_UPDATE_SERVER_CSS_URL', BVLWARK_UPDATE_SERVER_ASSETS_URL . 'css/' );
		define( 'BVLWARK_UPDATE_SERVER_JS_URL', BVLWARK_UPDATE_SERVER_ASSETS_URL . 'js/' );
		define( 'BVLWARK_UPDATE_SERVER_IMG_URL', BVLWARK_UPDATE_SERVER_ASSETS_URL . 'img/' );

		// Helpers.
		define( 'BVLWARK_UPDATE_SERVER_DB_DATE_FORMAT', 'Y-m-d H:i:s' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		register_activation_hook( BVLWARK_UPDATE_SERVER_PLUGIN_FILE, array( '\BVLWARK\UpdateServer\Installer', 'install' ) );
		register_deactivation_hook( BVLWARK_UPDATE_SERVER_PLUGIN_FILE, array( '\BVLWARK\UpdateServer\Installer', 'deactivate' ) );
		register_uninstall_hook( BVLWARK_UPDATE_SERVER_PLUGIN_FILE, array( '\BVLWARK\UpdateServer\Installer', 'uninstall' ) );

		add_action( 'admin_enqueue_scripts', array( 'BVLWARK\UpdateServer\AssetManager', 'register' ), 11 );
		add_action( 'admin_enqueue_scripts', array( 'BVLWARK\UpdateServer\AssetManager', 'enqueue' ), 20 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Init BVLWARK\UpdateServer when WordPress Initialises.
	 *
	 * @return void
	 */
	public function init(): void {
		Installer::migrate();

		new Admin\Menu();

		$this->init_controllers();
	}

	/**
	 * Initializes all plugin controllers.
	 *
	 * @return void
	 */
	private function init_controllers(): void {
		$controllers = array(
			ItemController::class,
		);

		/** @var SingletonInterface $controller */
		foreach ( $controllers as $controller ) {
			$controller::instance();
		}
	}

	/**
	 * Add additional links to the plugin row meta.
	 *
	 * @param array  $links Array of already present links.
	 * @param string $file  File name.
	 *
	 * @return array
	 */
	public function plugin_row_meta( array $links, string $file ): array {
		if ( str_contains( $file, 'bvlwark-update-server.php' ) ) {
			$new_links = array(
				'github' => '<a href="https://github.com/bvlwark/update-server" target="_blank">GitHub</a>',
				'docs'   => sprintf(
					'<a href="https://docs.bvlwark.dev/update-server" target="_blank">%s</a>',
					esc_html__( 'Docs', 'bvlwark-update-server' )
				),
			);

			$links = array_merge( $links, $new_links );
		}

		return $links;
	}
}
