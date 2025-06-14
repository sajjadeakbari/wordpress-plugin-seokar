<?php
/**
 * Plugin Name:       SeoKar
 * Plugin URI:        https://seokar.click
 * Description:       افزونه سئو پیشرفته وردپرس "سئوکار" - راهکار جامع و هوشمند برای بهینه‌سازی تخصصی وب‌سایت شما و پیشی گرفتن از رقبا با تکیه بر جدیدترین متدهای سئو و هوش مصنوعی.
 * Version:           0.1.0
 * Author:            Sajjad Akbari
 * Author URI:        https://sajjadakbari.ir
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       seokar
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 *
 * @package           SeoKar
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The main plugin bootstrap file.
 *
 * This file is responsible for starting the plugin. It defines constants,
 * includes the autoloader, registers activation/deactivation hooks, and
 * tells WordPress to begin executing the plugin.
 *
 * @package SeoKar
 */

// -----------------------------------------------------------------------------
// 1. Define Core Constants
// -----------------------------------------------------------------------------
// We include the constants file first, as many subsequent files will rely on them.
require_once plugin_dir_path( __FILE__ ) . 'includes/constants.php';


// -----------------------------------------------------------------------------
// 2. Include the Autoloader
// -----------------------------------------------------------------------------
// This file is responsible for automatically loading our plugin's classes.
require_once SEOKAR_INCLUDES_PATH . 'autoloader.php';


// -----------------------------------------------------------------------------
// 3. Register Activation & Deactivation Hooks
// -----------------------------------------------------------------------------
// These hooks are registered in the global scope and call static methods
// to keep the global namespace clean. The logic is contained within dedicated classes.

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
register_activation_hook( __FILE__, [ 'SeoKar\Includes\Activator', 'activate' ] );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
register_deactivation_hook( __FILE__, [ 'SeoKar\Includes\Deactivator', 'deactivate' ] );


// -----------------------------------------------------------------------------
// 4. Instantiate and Run the Plugin
// -----------------------------------------------------------------------------
// We create a single instance of our main plugin class and store it for later use.
// We hook this to 'plugins_loaded' to ensure all plugins and the WP core are available.

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point is as simple as
 * calling the get_instance() method on the main class.
 *
 * @since 0.1.0
 */
function seokar_run() {
	return \SeoKar\Includes\SeoKar::get_instance();
}

// Let's get this party started!
add_action( 'plugins_loaded', 'seokar_run' );
