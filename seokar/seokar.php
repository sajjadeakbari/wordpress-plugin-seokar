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
 * @package SeoKar
 */

// جلوگیری از اجرای مستقیم فایل
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You are not supposed to be here!' );

// --- ۱. بررسی حداقل نیازمندی‌های PHP و WordPress ---
define( 'SEOKAR_MINIMUM_PHP_VERSION', '7.4' );
define( 'SEOKAR_MINIMUM_WP_VERSION', '5.8' );
define( 'SEOKAR_PLUGIN_BASENAME_FOR_CHECK', plugin_basename( __FILE__ ) );

if ( version_compare( PHP_VERSION, SEOKAR_MINIMUM_PHP_VERSION, '<' ) ) {
    add_action( 'admin_notices', function() {
        $message = sprintf(
            esc_html__( 'SeoKar requires PHP version %1$s or higher. You are running version %2$s. The plugin has been deactivated.', 'seokar' ),
            SEOKAR_MINIMUM_PHP_VERSION,
            PHP_VERSION
        );
        echo '<div class="error"><p>' . wp_kses_post( $message ) . '</p></div>';
    });
    add_action('admin_init', function() {
        if ( is_plugin_active( SEOKAR_PLUGIN_BASENAME_FOR_CHECK ) ) {
            deactivate_plugins( SEOKAR_PLUGIN_BASENAME_FOR_CHECK );
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }
    });
    return;
}

global $wp_version;
if ( version_compare( $wp_version, SEOKAR_MINIMUM_WP_VERSION, '<' ) ) {
    add_action( 'admin_notices', function() use ( $wp_version ) {
        $message = sprintf(
            esc_html__( 'SeoKar requires WordPress version %1$s or higher. You are running version %2$s. The plugin has been deactivated.', 'seokar' ),
            SEOKAR_MINIMUM_WP_VERSION,
            $wp_version
        );
        echo '<div class="error"><p>' . wp_kses_post( $message ) . '</p></div>';
    });
    add_action('admin_init', function() {
         if ( is_plugin_active( SEOKAR_PLUGIN_BASENAME_FOR_CHECK ) ) {
            deactivate_plugins( SEOKAR_PLUGIN_BASENAME_FOR_CHECK );
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }
    });
    return;
}
unset($wp_version);

// --- ۲. بارگذاری Autoloader تولید شده توسط Composer ---
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    if ( defined('WP_DEBUG') && WP_DEBUG ) {
        error_log( 'SeoKar Plugin Error: Composer autoloader not found. Please run "composer install".' );
    }
    if ( is_admin() ) {
        add_action( 'admin_notices', function() {
            $message = '<strong>' . esc_html__( 'SeoKar Plugin Critical Error:', 'seokar' ) . '</strong> ' .
                       esc_html__( 'The Composer autoloader is missing. The plugin cannot function. Please run "composer install" in the plugin directory or contact support.', 'seokar' );
            echo '<div class="error"><p>' . wp_kses_post( $message ) . '</p></div>';
        });
    }
    return;
}

// --- ۳. تعریف تابع اصلی و راه‌اندازی افزونه ---

/**
 * تابع اصلی برای برگرداندن نمونه کلاس SeoKar\Main.
 *
 * @since  0.1.0
 * @return \SeoKar\Main نمونه کلاس اصلی افزونه.
 */
function SeoKar() {
    static $instance = null;
    if ( null === $instance ) {
        $instance = \SeoKar\Main::get_instance( __FILE__ );
    }
    return $instance;
}

SeoKar();

unset(SEOKAR_MINIMUM_PHP_VERSION, SEOKAR_MINIMUM_WP_VERSION, SEOKAR_PLUGIN_BASENAME_FOR_CHECK);
?>
