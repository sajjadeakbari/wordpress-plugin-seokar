<?php
/**
 * Defines all the core constants for the SeoKar plugin.
 *
 * This file is loaded very early, and its constants are used throughout the plugin
 * for defining paths, URLs, version numbers, and other key identifiers.
 *
 * @package      SeoKar
 * @subpackage   Includes
 * @since        0.1.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// -----------------------------------------------------------------------------
// 1. Plugin Information Constants
// -----------------------------------------------------------------------------

/**
 * The current version of the plugin.
 * Used for cache-busting of scripts and styles, and for database migration checks.
 *
 * @since 0.1.0
 */
define( 'SEOKAR_VERSION', '0.1.0' );

/**
 * The main plugin file path.
 * This is used as a base for other path and URL constants.
 * We define it here to avoid repetitive calls to `dirname()`.
 *
 * @since 0.1.0
 */
define( 'SEOKAR_PLUGIN_FILE', dirname( __DIR__ ) . '/seokar.php' );

/**
 * The plugin's text domain.
 * Used for internationalization (i18n).
 *
 * @since 0.1.0
 */
define( 'SEOKAR_TEXT_DOMAIN', 'seokar' );


// -----------------------------------------------------------------------------
// 2. Directory Path Constants
// -----------------------------------------------------------------------------

/**
 * The absolute filesystem path to the plugin's root directory.
 * Includes a trailing slash.
 * Example: /var/www/html/wp-content/plugins/seokar/
 *
 * @since 0.1.0
 */
define( 'SEOKAR_PATH', plugin_dir_path( SEOKAR_PLUGIN_FILE ) );

/**
 * The absolute filesystem path to the 'includes' directory.
 * Includes a trailing slash.
 *
 * @since 0.1.0
 */
define( 'SEOKAR_INCLUDES_PATH', SEOKAR_PATH . 'includes/' );

/**
 * The absolute filesystem path to the 'admin' directory.
 * Includes a trailing slash.
 *
 * @since 0.1.0
 */
define( 'SEOKAR_ADMIN_PATH', SEOKAR_PATH . 'admin/' );

/**
 * The absolute filesystem path to the 'public' directory.
 * Includes a trailing slash.
 *
 * @since 0.1.0
 */
define( 'SEOKAR_PUBLIC_PATH', SEOKAR_PATH . 'public/' );

/**
 * The absolute filesystem path to the 'modules' directory.
 * Includes a trailing slash.
 *
 * @since 0.1.0
 */
define( 'SEOKAR_MODULES_PATH', SEOKAR_PATH . 'modules/' );

/**
 * The absolute filesystem path to the 'languages' directory.
 * Includes a trailing slash.
 *
 * @since 0.1.0
 */
define( 'SEOKAR_LANGUAGES_PATH', SEOKAR_PATH . 'languages/' );


// -----------------------------------------------------------------------------
// 3. URL Constants
// -----------------------------------------------------------------------------

/**
 * The base URL to the plugin's root directory.
 * Includes a trailing slash.
 * Example: http://example.com/wp-content/plugins/seokar/
 *
 * @since 0.1.0
 */
define( 'SEOKAR_URL', plugin_dir_url( SEOKAR_PLUGIN_FILE ) );

/**
 * The URL to the 'admin/assets' directory.
 * Used for enqueueing admin-specific scripts and styles.
 * Includes a trailing slash.
 *
 * @since 0.1.0
 */
define( 'SEOKAR_ADMIN_ASSETS_URL', SEOKAR_URL . 'admin/assets/' );

/**
 * The URL to the 'public/assets' directory.
 * Used for enqueueing front-end scripts and styles.
 * Includes a trailing slash.
 *
 * @since 0.1.0
 */
define( 'SEOKAR_PUBLIC_ASSETS_URL', SEOKAR_URL . 'public/assets/' );
