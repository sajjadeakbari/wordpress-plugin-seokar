<?php
/**
 * Plugin Name:       سئوکار
 * Plugin URI:        https://yourwebsite.com/seokar
 * Description:       افزونه جامع و هوشمند سئو وردپرس برای بهینه‌سازی تخصصی وب‌سایت شما.
 * Version:           0.1.0
 * Author:            [نام شما یا شرکت شما]
 * Author URI:        https://yourwebsite.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       seokar
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 */

// جلوگیری از دسترسی مستقیم به فایل
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// تعریف ثابت‌های اصلی افزونه
define( 'SEOKAR_VERSION', '0.1.0' );
define( 'SEOKAR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SEOKAR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SEOKAR_PLUGIN_FILE', __FILE__ );
define( 'SEOKAR_TEXT_DOMAIN', 'seokar' ); // << ثابت از قبل تعریف شده بود

/**
 * تابع اصلی برای اجرای افزونه سئوکار.
 */
function seokar_run_plugin() {
    // بارگذاری فایل ترجمه
    load_plugin_textdomain(
        SEOKAR_TEXT_DOMAIN, // <--- استفاده از ثابت به جای رشته متنی
        false,
        dirname( plugin_basename( SEOKAR_PLUGIN_FILE ) ) . '/languages/'
    );

    // در اینجا فایل‌های اصلی کلاس‌ها و توابع را include خواهیم کرد.
    require_once SEOKAR_PLUGIN_DIR . 'includes/class-seokar-core.php';

    // نمونه‌سازی کلاس اصلی افزونه
    if ( class_exists( 'SeoKar_Core' ) ) {
        // برای اطمینان از اینکه فقط یک نمونه از کلاس اصلی داریم (Singleton)
        // می‌توانیم یک متد استاتیک get_instance() در کلاس SeoKar_Core داشته باشیم
        // $GLOBALS['seokar'] = SeoKar_Core::get_instance();
        // یا اگر get_instance پیاده سازی نشده:
        $GLOBALS['seokar_instance'] = new SeoKar_Core();
        $GLOBALS['seokar_instance']->run();
    }

    // سایر تنظیمات اولیه در اینجا قرار می‌گیرند
}
add_action( 'plugins_loaded', 'seokar_run_plugin' );

/**
 * توابع فعال‌سازی و غیرفعال‌سازی افزونه
 */
function seokar_activate() {
    if ( ! get_option( 'seokar_setup_wizard_completed' ) && ! get_option( 'seokar_initial_version' ) ) {
        set_transient( 'seokar_activation_redirect', true, 30 );
    }
    // ذخیره نسخه اولیه افزونه برای مدیریت آپدیت‌ها یا تغییرات ساختاری در آینده
    update_option( 'seokar_initial_version', SEOKAR_VERSION );
    // ذخیره نسخه فعلی افزونه
    update_option( 'seokar_current_version', SEOKAR_VERSION );

    flush_rewrite_rules();
}
register_activation_hook( SEOKAR_PLUGIN_FILE, 'seokar_activate' );

function seokar_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( SEOKAR_PLUGIN_FILE, 'seokar_deactivate' );

/*
seokar/
├── seokar.php
├── uninstall.php
├── languages/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── includes/
│   ├── class-seokar-core.php <--- فایل بعدی ما
│   ├── class-seokar-settings.php
│   ├── class-seokar-setup-wizard.php
│   ├── admin/
│   │   └── class-seokar-admin-menus.php
│   │   └── ...
│   ├── frontend/
│   │   └── class-seokar-frontend-output.php
│   │   └── ...
│   ├── modules/
│   │   └── ...
│   └── helpers/
│       └── functions.php
└── templates/
*/
?>
