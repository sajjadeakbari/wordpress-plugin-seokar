<?php
/**
 * PSR-4 Autoloader for the SeoKar plugin.
 *
 * This file is responsible for automatically loading class files as they are needed.
 * It maps the plugin's namespaces to their corresponding directories.
 *
 * @package      SeoKar
 * @subpackage   Includes
 * @since        0.1.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

spl_autoload_register(
	function( $class ) {
		// Define the base namespace for our plugin.
		$prefix = 'SeoKar\\';

		// Does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			// No, move to the next registered autoloader.
			return;
		}

		// Get the relative class name.
		$relative_class = substr( $class, $len );

		// The base directory for the namespace prefix.
		// Our root namespace `SeoKar` maps to the plugin's root directory.
		$base_dir = SEOKAR_PATH;

		// Replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .php.
		// Example: 'SeoKar\Includes\Activator' becomes
		// '/path/to/plugins/seokar/includes/Activator.php' (before formatting).
		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		// We need to convert the class name to the WordPress file naming standard.
		// Example: `My_Awesome_Class` becomes `class-my-awesome-class.php`.
		// Example: `MyAwesomeClass` becomes `class-myawesomeclass.php`. (WP Standard prefers underscores)
		// We will support both PSR-4 (CamelCase) and WordPress (Pascal_Case_With_Underscores).
		$parts = explode( '/', $file );
		$file_name = array_pop( $parts ); // Get the file name part (e.g., 'Activator.php').
		$path = implode( '/', $parts );   // Get the directory path.

		// Check for traits.
		if ( strpos( strtolower( $file_name ), 'trait-' ) === 0 ) {
			$file_name_formatted = str_replace( '_', '-', strtolower( $file_name ) );
		} else {
			// It's a class.
			// 'ClassName.php' -> 'class-classname.php'
			// 'Class_Name.php' -> 'class-class-name.php'
			$file_name_formatted = 'class-' . str_replace( '_', '-', strtolower( substr( $file_name, 0, -4 ) ) ) . '.php';
		}
		
		$file = $path . '/' . $file_name_formatted;

		// If the file exists, require it.
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
