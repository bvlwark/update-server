<?php
/**
 * Plugin Name: BVLWARK Theme & Plugin Update Server
 * Plugin URI: https://drazen.bebic.dev/projects/bvlwark-update-server
 * Description: A secure, self-hosted update server for WordPress plugins and themes. Gate updates behind a paywall, manage versions, and serve updates on your terms.
 * Author: Drazen Bebic
 * Author URI: https://drazen.bebic.dev
 * Version: 0.1.0
 */

namespace BVLWARK\UpdateServer;

defined( 'ABSPATH' ) || exit;

// Require the composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Require function files.
require_once __DIR__ . '/src/functions/api-key-functions.php';
require_once __DIR__ . '/src/functions/core-functions.php';
require_once __DIR__ . '/src/functions/package-functions.php';
require_once __DIR__ . '/src/functions/package-version-functions.php';
require_once __DIR__ . '/src/functions/template-functions.php';

// Define the plugin version.
if ( ! defined( 'BVLWARK_PLUGIN_VERSION' ) ) {
	define( 'BVLWARK_PLUGIN_VERSION', '0.1.0' );
}

// Define BVLWARK_PLUGIN_FILE.
if ( ! defined( 'BVLWARK_PLUGIN_FILE' ) ) {
	define( 'BVLWARK_PLUGIN_FILE', __FILE__ );
}

// Define BVLWARK_PLUGIN_DIR.
if ( ! defined( 'BVLWARK_PLUGIN_DIR' ) ) {
	define( 'BVLWARK_PLUGIN_DIR', __DIR__ );
}

// Define BVLWARK_PLUGIN_URL.
if ( ! defined( 'BVLWARK_PLUGIN_URL' ) ) {
	define( 'BVLWARK_PLUGIN_URL', plugins_url( '', __FILE__ ) . '/src/' );
}

/**
 * Main instance of the plugin.
 *
 * @return Main
 */
Main::instance();
