<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package      SeoKar
 * @subpackage   Includes
 * @author       Sajjad Akbari <sajjad.akbari.dev@gmail.com>
 * @since        0.1.0
 */

namespace SeoKar\Includes;

// We need to use the Installer class, so we import its namespace.
use SeoKar\Includes\Database\Installer;

/**
 * The activator class.
 */
class Activator {

	/**
	 * Main activation method.
	 *
	 * This static method is called by the activation hook in the main plugin file.
	 * It orchestrates all tasks that need to be performed on activation.
	 *
	 * @since 0.1.0
	 */
	public static function activate() {
		// 1. Set up the initial default settings for the plugin.
		self::add_default_options();

		// 2. Create or update the necessary database tables.
		// We delegate this responsibility to a dedicated Installer class.
		// Note: The Installer class will be created in a future step.
		$installer = new Installer();
		$installer->run();

		// 3. Flush the rewrite rules.
		// This is important to ensure that any custom rewrite rules (e.g., for sitemaps)
		// are recognized by WordPress immediately.
		flush_rewrite_rules();
	}

	/**
	 * Adds the default plugin options to the database.
	 *
	 * These are the settings the plugin will use out-of-the-box. We use `add_option`
	 * to ensure we don't overwrite existing settings on a re-activation.
	 *
	 * @since 0.1.0
	 */
	private static function add_default_options() {
		$option_name = 'seokar_settings';

		// Only add the option if it doesn't already exist.
		if ( false === get_option( $option_name ) ) {
			$default_settings = [
				'version'          => SEOKAR_VERSION,
				'modules'          => [
					'sitemap'   => true,
					'schema'    => true,
					'metabox'   => true,
					'opengraph' => true,
				],
				// Add other general settings here in the future.
				'general'          => [],
				'sitemap_settings' => [
					'posts_per_page' => 200,
					'include_images' => true,
				],
			];

			add_option( $option_name, $default_settings );
		}
	}
}
