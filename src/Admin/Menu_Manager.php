<?php
/**
 * SeoKar Admin Menu Manager
 *
 * این کلاس مسئولیت ایجاد و مدیریت منوهای افزونه سئوکار در پیشخوان وردپرس را بر عهده دارد.
 *
 * @package SeoKar\Admin
 * @since 0.1.0
 */

namespace SeoKar\Admin;

// جلوگیری از دسترسی مستقیم به فایل
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'SeoKar\\Admin\\Menu_Manager' ) ) {

    /**
     * کلاس Menu_Manager
     *
     * برای ثبت منوی اصلی و زیرمنوهای افزونه سئوکار.
     */
    class Menu_Manager {

        /**
         * تنها نمونه از کلاس Menu_Manager (Singleton Pattern).
         *
         * @var Menu_Manager|null
         * @since 0.1.0
         * @access private
         * @static
         */
        private static $instance = null;

        /**
         * نامک (slug) منوی اصلی افزونه.
         *
         * @var string
         * @since 0.1.0
         * @access public
         */
        public $main_menu_slug = 'seokar-dashboard';

        /**
         * حداقل سطح دسترسی مورد نیاز برای مشاهده منوهای سئوکار.
         *
         * @var string
         * @since 0.1.0
         * @access public
         */
        public $capability = 'manage_options'; // این می‌تواند به یک قابلیت سفارشی سئوکار تغییر یابد

        /**
         * Constructor خصوصی برای پیاده‌سازی الگوی Singleton.
         *
         * @since 0.1.0
         * @access private
         */
        private function __construct() {
            // ثبت هوک برای ایجاد منوها در پیشخوان وردپرس
            add_action( 'admin_menu', array( $this, 'register_menus' ) );
        }

        /**
         * برگرداندن تنها نمونه از کلاس (Singleton Pattern).
         *
         * @static
         * @return Menu_Manager - نمونه Menu_Manager.
         * @since 0.1.0
         * @access public
         */
        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * ثبت منوی اصلی و زیرمنوهای افزونه سئوکار.
         * این متد توسط هوک 'admin_menu' فراخوانی می‌شود.
         *
         * @since 0.1.0
         * @access public
         */
        public function register_menus() {
            // ۱. ثبت منوی اصلی "سئوکار"
            add_menu_page(
                esc_html__( 'SeoKar Dashboard', 'seokar' ),      // عنوان صفحه (Page Title)
                esc_html__( 'سئوکار', 'seokar' ),                // عنوان منو (Menu Title)
                $this->capability,                               // سطح دسترسی (Capability)
                $this->main_menu_slug,                           // نامک منو (Menu Slug)
                array( $this, 'display_dashboard_page' ),        // تابع نمایش دهنده محتوای صفحه
                'dashicons-chart-area',                          // آیکون منو (از Dashicons، مرتبط با تحلیل و سئو)
                26                                               // موقعیت منو (معمولاً بعد از "دیدگاه‌ها")
            );

            // ۲. ثبت زیرمنوی "تنظیمات عمومی"
            add_submenu_page(
                $this->main_menu_slug,                           // نامک والد (منوی اصلی سئوکار)
                esc_html__( 'General Settings - SeoKar', 'seokar' ), // عنوان صفحه
                esc_html__( 'تنظیمات عمومی', 'seokar' ),         // عنوان زیرمنو
                $this->capability,                               // سطح دسترسی
                'seokar-general-settings',                       // نامک این زیرمنو
                array( $this, 'display_general_settings_page' )  // تابع نمایش دهنده محتوای صفحه
            );

            // ۳. ایجاد یک "زیرمنوی" تکراری برای داشبورد اصلی با نام "داشبورد"
            // این کار باعث می‌شود اولین آیتم زیرمنو "داشبورد" باشد.
            add_submenu_page(
                $this->main_menu_slug,                           // نامک والد
                esc_html__( 'SeoKar Dashboard', 'seokar' ),  // عنوان صفحه
                esc_html__( 'داشبورد', 'seokar' ),               // عنوان زیرمنو
                $this->capability,                               // سطح دسترسی
                $this->main_menu_slug,                           // نامک (همان نامک منوی اصلی)
                array( $this, 'display_dashboard_page' )     // تابع نمایش دهنده (همان تابع داشبورد)
            );

            // --- در آینده زیرمنوهای دیگر مانند موارد زیر در اینجا اضافه خواهند شد ---
            // add_submenu_page(
            //     $this->main_menu_slug,
            //     esc_html__( 'Titles & Metas - SeoKar', 'seokar' ),
            //     esc_html__( 'عناوین و متاها', 'seokar' ),
            //     $this->capability,
            //     'seokar-titles-metas',
            //     array( $this, 'display_titles_metas_page' ) // نیاز به تابع callback جدید
            // );
            //
            // add_submenu_page(
            //     $this->main_menu_slug,
            //     esc_html__( 'XML Sitemaps - SeoKar', 'seokar' ),
            //     esc_html__( 'نقشه سایت XML', 'seokar' ),
            //     $this->capability,
            //     'seokar-sitemaps',
            //     array( $this, 'display_sitemaps_page' ) // نیاز به تابع callback جدید
            // );
            // ... و سایر ماژول‌ها ...

            // هوک برای اضافه کردن زیرمنوهای اضافی توسط سایر ماژول‌ها یا افزونه‌های جانبی
            do_action( 'seokar_register_submenu_pages', $this->main_menu_slug, $this->capability );
        }

        /**
         * نمایش محتوای صفحه "داشبورد اصلی سئوکار" با بارگذاری فایل تمپلیت.
         *
         * @since 0.1.0
         * @access public
         */
        public function display_dashboard_page() {
            // اطمینان از اینکه ثابت SEOKAR_TEMPLATES_DIR تعریف شده و در دسترس است.
            // این ثابت در کلاس SeoKar\Main::define_constants() تعریف شده است.
            if ( defined( 'SEOKAR_TEMPLATES_DIR' ) ) {
                $template_path = SEOKAR_TEMPLATES_DIR . 'admin/dashboard-page.php';
                if ( file_exists( $template_path ) ) {
                    require_once $template_path;
                } else {
                    // Fallback یا نمایش خطا اگر فایل تمپلیت پیدا نشد
                    echo '<div class="wrap"><h1>' . esc_html__( 'Template Error', 'seokar' ) . '</h1><p>' . sprintf( esc_html__( 'Dashboard template file not found at: %s', 'seokar' ), esc_html( $template_path ) ) . '</p></div>';
                }
            } else {
                // Fallback یا نمایش خطا اگر ثابت مسیر تمپلیت‌ها تعریف نشده باشد
                echo '<div class="wrap"><h1>' . esc_html__( 'Configuration Error', 'seokar' ) . '</h1><p>' . esc_html__( 'The SEOKAR_TEMPLATES_DIR constant is not defined. Please check plugin configuration.', 'seokar' ) . '</p></div>';
            }
        }

        /**
         * نمایش محتوای صفحه "تنظیمات عمومی سئوکار" با بارگذاری فایل تمپلیت.
         *
         * @since 0.1.0
         * @access public
         */
        public function display_general_settings_page() {
            if ( defined( 'SEOKAR_TEMPLATES_DIR' ) ) {
                $template_path = SEOKAR_TEMPLATES_DIR . 'admin/general-settings-page.php';
                if ( file_exists( $template_path ) ) {
                    require_once $template_path;
                } else {
                    echo '<div class="wrap"><h1>' . esc_html__( 'Template Error', 'seokar' ) . '</h1><p>' . sprintf( esc_html__( 'General settings template file not found at: %s', 'seokar' ), esc_html( $template_path ) ) . '</p></div>';
                }
            } else {
                echo '<div class="wrap"><h1>' . esc_html__( 'Configuration Error', 'seokar' ) . '</h1><p>' . esc_html__( 'The SEOKAR_TEMPLATES_DIR constant is not defined. Please check plugin configuration.', 'seokar' ) . '</p></div>';
            }
        }

        /**
         * یک تابع callback موقت برای سایر زیرمنوهایی که در آینده اضافه می‌شوند (اگر نیاز باشد).
         * این تابع می‌تواند برای صفحاتی که هنوز تمپلیت یا منطق خاص خود را ندارند استفاده شود.
         *
         * @since 0.1.0
         * @access public
         * @param string $page_title عنوان صفحه (معمولاً از عنوان منو گرفته می‌شود).
         */
        public function display_page_placeholder( $page_title = '' ) {
            // اگر عنوان صفحه به صورت خودکار توسط وردپرس از عنوان منو پاس داده نشده، یک پیش‌فرض می‌گذاریم
            if ( empty( $page_title ) ) {
                 // سعی در گرفتن عنوان از صفحه فعلی (اگر ممکن باشد)
                $screen = get_current_screen();
                if ( $screen && ! empty( $screen->base ) && strpos( $screen->base, $this->main_menu_slug ) !== false ) {
                    // این بخش نیاز به منطق بیشتری برای دریافت دقیق عنوان منو دارد
                    // فعلا یک عنوان عمومی استفاده می‌کنیم
                    $page_title = esc_html__( 'SeoKar Page', 'seokar' );
                } else {
                    $page_title = esc_html__( 'SeoKar Page', 'seokar' );
                }
            }

            echo '<div class="wrap seokar-admin-page seokar-placeholder-page">';
            echo '<h1>' . esc_html( $page_title ) . '</h1>';
            echo '<hr class="wp-header-end">'; // خط جداکننده استاندارد وردپرس
            echo '<p>' . esc_html__( 'This page is part of SeoKar plugin and is currently under development. It will be available in a future update.', 'seokar' ) . '</p>';
            echo '<!-- More content or specific module information can be added here by developers -->';
            echo '</div>';
        }

    } // پایان کلاس Menu_Manager

} // پایان if ( ! class_exists( 'SeoKar\\Admin\\Menu_Manager' ) )
