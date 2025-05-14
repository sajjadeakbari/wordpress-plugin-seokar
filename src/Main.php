<?php
/**
 * کلاس اصلی افزونه سئوکار.
 *
 * این فایل حاوی کلاس Main است که نقطه مرکزی کنترل و مدیریت افزونه سئوکار می‌باشد.
 *
 * @package SeoKar
 * @since 0.1.0
 */

namespace SeoKar; // تعریف Namespace برای تمام کلاس‌های افزونه

// جلوگیری از دسترسی مستقیم به فایل اگر این فایل به تنهایی include شود (احتیاط بیشتر)
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// اطمینان از اینکه کلاس اصلی قبلاً تعریف نشده است (برای جلوگیری از تداخل در موارد نادر)
if ( ! class_exists( 'SeoKar\\Main' ) ) {

    /**
     * کلاس اصلی افزونه سئوکار (SeoKar Main Class).
     *
     * مسئولیت راه‌اندازی اولیه، تعریف ثابت‌ها، ثبت هوک‌های اصلی،
     * بارگذاری کامپوننت‌های هسته و مدیریت کلی افزونه را بر عهده دارد.
     * این کلاس با استفاده از الگوی طراحی Singleton پیاده‌سازی شده است.
     *
     * @package SeoKar
     * @since   0.1.0
     */
    final class Main {

        /**
         * تنها نمونه (instance) از کلاس Main.
         * برای پیاده‌سازی الگوی Singleton استفاده می‌شود.
         *
         * @since  0.1.0
         * @var    Main|null
         * @access private
         * @static
         */
        private static $instance = null;

        /**
         * نسخه فعلی افزونه سئوکار.
         * این مقدار باید با هدر افزونه در seokar.php هماهنگ باشد.
         *
         * @since  0.1.0
         * @var    string
         * @access public
         */
        public $version = '0.1.0';

        /**
         * مسیر کامل فایل اصلی افزونه (seokar.php).
         * این مقدار از طریق کانستراکتور دریافت و ذخیره می‌شود.
         *
         * @since  0.1.0
         * @var    string
         * @access private
         */
        private $plugin_file_path;

        /**
         * Constructor خصوصی برای پیاده‌سازی الگوی Singleton.
         * مسیر فایل اصلی افزونه را به عنوان پارامتر دریافت می‌کند.
         *
         * @since  0.1.0
         * @access private
         * @param string $plugin_file_path مسیر کامل فایل اصلی افزونه (seokar.php).
         */
        private function __construct( $plugin_file_path ) {
            $this->plugin_file_path = $plugin_file_path;

            /**
             * Fires before SeoKar constants are defined.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_before_constants_defined', $this );
            $this->define_constants();
            /**
             * Fires after SeoKar constants are defined.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_after_constants_defined', $this );

            /**
             * Fires before SeoKar initial hooks are registered.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_before_init_hooks', $this );
            $this->init_hooks();
            /**
             * Fires after SeoKar initial hooks are registered.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_after_init_hooks', $this );

            /**
             * Fires before SeoKar core components are loaded.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_before_core_components_loaded', $this );
            $this->load_core_components();
            /**
             * Fires after SeoKar core components are loaded.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_after_core_components_loaded', $this );
        }

        /**
         * جلوگیری از کلون کردن نمونه (بخشی از الگوی Singleton).
         *
         * @since  0.1.0
         * @access private
         */
        private function __clone() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is not allowed for the SeoKar Main class.', 'seokar' ), $this->version );
        }

        /**
         * جلوگیری از unserialize کردن نمونه (بخشی از الگوی Singleton).
         *
         * @since  0.1.0
         * @access public
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of the SeoKar Main class is not allowed.', 'seokar' ), $this->version );
        }

        /**
         * متد اصلی برای دریافت تنها نمونه از کلاس (الگوی Singleton).
         * مسیر فایل اصلی افزونه را برای اولین نمونه‌سازی دریافت می‌کند.
         *
         * @since  0.1.0
         * @access public
         * @static
         * @param  string|null $plugin_file_path مسیر کامل فایل اصلی افزونه (seokar.php).
         *                                        این پارامتر فقط در اولین فراخوانی برای ایجاد نمونه نیاز است.
         * @return Main تنها نمونه از کلاس Main.
         * @throws \Exception اگر $plugin_file_path در اولین فراخوانی برای ایجاد نمونه ارائه نشود.
         */
        public static function get_instance( $plugin_file_path = null ) {
            if ( null === self::$instance ) {
                if ( null === $plugin_file_path ) {
                    throw new \Exception( 'Plugin file path must be provided on first instantiation of SeoKar\\Main class.' );
                }
                self::$instance = new self( $plugin_file_path );
            }
            return self::$instance;
        }

        /**
         * تعریف ثابت‌های اصلی و کاربردی افزونه.
         *
         * @since  0.1.0
         * @access private
         */
        private function define_constants() {
            // --- نسخه و شناسه‌های اصلی ---
            if ( ! defined( 'SEOKAR_VERSION' ) ) {
                define( 'SEOKAR_VERSION', $this->version );
            }
            if ( ! defined( 'SEOKAR_PLUGIN_FILE' ) ) {
                define( 'SEOKAR_PLUGIN_FILE', $this->plugin_file_path );
            }
            if ( ! defined( 'SEOKAR_PLUGIN_BASENAME' ) ) {
                define( 'SEOKAR_PLUGIN_BASENAME', plugin_basename( SEOKAR_PLUGIN_FILE ) );
            }
            if ( ! defined( 'SEOKAR_TEXT_DOMAIN' ) ) {
                define( 'SEOKAR_TEXT_DOMAIN', 'seokar' );
            }

            // --- مسیرهای دایرکتوری (Paths) ---
            if ( ! defined( 'SEOKAR_PLUGIN_DIR' ) ) {
                define( 'SEOKAR_PLUGIN_DIR', plugin_dir_path( SEOKAR_PLUGIN_FILE ) );
            }
            if ( ! defined( 'SEOKAR_SRC_DIR' ) ) {
                define( 'SEOKAR_SRC_DIR', SEOKAR_PLUGIN_DIR . 'src/' );
            }
            if ( ! defined( 'SEOKAR_ADMIN_FILES_DIR' ) ) {
                define( 'SEOKAR_ADMIN_FILES_DIR', SEOKAR_PLUGIN_DIR . 'admin/' );
            }
            if ( ! defined( 'SEOKAR_PUBLIC_FILES_DIR' ) ) {
                define( 'SEOKAR_PUBLIC_FILES_DIR', SEOKAR_PLUGIN_DIR . 'public/' );
            }
            if ( ! defined( 'SEOKAR_TEMPLATES_DIR' ) ) {
                define( 'SEOKAR_TEMPLATES_DIR', SEOKAR_PLUGIN_DIR . 'templates/' );
            }
             if ( ! defined( 'SEOKAR_ASSETS_DIR' ) ) {
                define( 'SEOKAR_ASSETS_DIR', SEOKAR_PLUGIN_DIR . 'assets/' );
            }
            if ( ! defined( 'SEOKAR_LANG_DIR_NAME' ) ) {
                define( 'SEOKAR_LANG_DIR_NAME', 'languages' );
            }
            if ( ! defined( 'SEOKAR_LANG_DIR' ) ) {
                define( 'SEOKAR_LANG_DIR', SEOKAR_PLUGIN_DIR . SEOKAR_LANG_DIR_NAME . '/' );
            }
            if ( ! defined( 'SEOKAR_VENDOR_DIR' ) ) {
                define( 'SEOKAR_VENDOR_DIR', SEOKAR_PLUGIN_DIR . 'vendor/' );
            }

            // --- آدرس‌های URL ---
            if ( ! defined( 'SEOKAR_PLUGIN_URL' ) ) {
                define( 'SEOKAR_PLUGIN_URL', plugin_dir_url( SEOKAR_PLUGIN_FILE ) );
            }
            if ( ! defined( 'SEOKAR_ASSETS_URL' ) ) {
                define( 'SEOKAR_ASSETS_URL', SEOKAR_PLUGIN_URL . 'assets/' );
            }

            /**
             * Fires after the main constants are defined, allowing other components to define their own.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_define_additional_constants', $this );
        }

        /**
         * مقداردهی اولیه و ثبت هوک‌های اصلی وردپرس.
         *
         * @since  0.1.0
         * @access private
         */
        private function init_hooks() {
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

            register_activation_hook( SEOKAR_PLUGIN_FILE, array( __CLASS__, 'activate' ) );
            register_deactivation_hook( SEOKAR_PLUGIN_FILE, array( __CLASS__, 'deactivate' ) );

            if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
                add_action( 'plugins_loaded', array( $this, 'load_admin_components' ), 15 );
            }

            add_action( 'plugins_loaded', array( $this, 'run' ), 20 );

            // هوک برای ثبت اسکریپت‌ها و استایل‌های ادمین
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
            // هوک برای ثبت اسکریپت‌ها و استایل‌های بخش کاربری (در صورت نیاز)
            // add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
        }

        /**
         * بارگذاری و نمونه‌سازی کامپوننت‌های هسته‌ای افزونه.
         *
         * @since 0.1.0
         * @access private
         */
        private function load_core_components() {
            // مثال: بارگذاری کلاس مدیریت آپگریدها
            // if ( class_exists( 'SeoKar\\Core\\Upgrader' ) ) {
            //     Core\Upgrader::get_instance()->init_hooks();
            // }

            // مثال: بارگذاری کلاس برای مدیریت AJAX یا REST API endpoints
            // if ( class_exists( 'SeoKar\\Core\\Ajax_Handler' ) ) {
            //     Core\Ajax_Handler::get_instance()->init_hooks();
            // }
            // if ( class_exists( 'SeoKar\\Core\\Rest_Api_Controller' ) ) {
            //     Core\Rest_Api_Controller::get_instance()->register_routes();
            // }

            /**
             * Fires after core components are loaded.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_core_components_loaded_hook', $this );
        }

        /**
         * بارگذاری کامپوننت‌های بخش مدیریت (Admin).
         *
         * @since 0.1.0
         * @access public
         */
        public function load_admin_components() {
            // بارگذاری و نمونه‌سازی کلاس مدیریت منوها
            if ( class_exists( 'SeoKar\\Admin\\Menu_Manager' ) ) {
                \SeoKar\Admin\Menu_Manager::get_instance();
            }

            // بارگذاری و نمونه‌سازی کلاس صفحه تنظیمات عمومی
            if ( class_exists( 'SeoKar\\Admin\\Settings\\General_Settings_Page' ) ) {
                \SeoKar\Admin\Settings\General_Settings_Page::get_instance();
            }

            // مثال: بارگذاری کنترلر اصلی بخش ادمین (برای متا باکس‌ها، نوتیس‌ها و ...)
            // if ( class_exists( 'SeoKar\\Admin\\Admin_Controller' ) ) {
            //     Admin\Admin_Controller::get_instance()->init();
            // }

            // مثال: بارگذاری جادوگر راه‌اندازی اولیه
            // $general_settings = get_option( 'seokar_settings_general', array() );
            // $setup_wizard_completed = isset( $general_settings['setup_wizard_completed'] ) ? $general_settings['setup_wizard_completed'] : false;
            // if ( class_exists( 'SeoKar\\Admin\\Setup_Wizard' ) && ! $setup_wizard_completed ) {
            //     // Setup_Wizard باید خودش هوک‌های مربوط به ریدایرکت و نمایش صفحه را مدیریت کند
            //     Admin\Setup_Wizard::get_instance();
            // }

            /**
             * Fires after admin components are loaded.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_admin_components_loaded_hook', $this );
        }


        /**
         * بارگذاری فایل ترجمه افزونه.
         *
         * @since  0.1.0
         * @access public
         */
        public function load_textdomain() {
            /**
             * Fires before the plugin text domain is loaded.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_before_load_textdomain', $this );

            load_plugin_textdomain(
                SEOKAR_TEXT_DOMAIN,
                false,
                SEOKAR_LANG_DIR_NAME
            );

            /**
             * Fires after the plugin text domain is loaded.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_after_load_textdomain', $this );
        }

        /**
         * ثبت و بارگذاری اسکریپت‌ها و استایل‌های بخش مدیریت.
         *
         * @since 0.1.0
         * @access public
         * @param string $hook_suffix نامک صفحه ادمین فعلی.
         */
        public function enqueue_admin_assets( $hook_suffix ) {
            // مثال: بارگذاری یک فایل CSS عمومی برای تمام صفحات سئوکار
            // if ( strpos( $hook_suffix, 'seokar-' ) !== false ) { // اگر نامک صفحه شامل 'seokar-' باشد
            //     wp_enqueue_style(
            //         'seokar-admin-common',
            //         SEOKAR_ASSETS_URL . 'css/admin-common.css',
            //         array(),
            //         SEOKAR_VERSION
            //     );
            // }

            // بارگذاری اسکریپت‌ها و استایل‌های مخصوص صفحه تنظیمات عمومی
            // نامک این صفحه 'toplevel_page_seokar-dashboard' برای صفحه اصلی داشبورد
            // و 'seokar_page_seokar-general-settings' (سئوکار_page_نامک-زیرمنو) برای صفحه تنظیمات عمومی است.
            // یا می‌توانیم از get_current_screen()->id استفاده کنیم.
            $current_screen = get_current_screen();
            if ( $current_screen && $current_screen->id === 'seokar_page_seokar-general-settings' ) {
                // فایل JS برای مدیریت نمایش شرطی فیلدها و Media Uploader
                wp_enqueue_script(
                    'seokar-admin-general-settings',
                    SEOKAR_ASSETS_URL . 'js/admin-general-settings.js', // این فایل باید ایجاد شود
                    array( 'jquery', 'wp-media' ), // وابستگی به jQuery و کتابخانه مدیا وردپرس
                    SEOKAR_VERSION,
                    true // بارگذاری در فوتر
                );

                // می‌توانید داده‌هایی را از PHP به JS ارسال کنید (مثلاً ترجمه‌ها یا تنظیمات خاص)
                // wp_localize_script(
                //     'seokar-admin-general-settings',
                //     'seokarGeneralSettingsParams',
                //     array(
                //         'some_string' => __( 'Some translatable string for JS', 'seokar' ),
                //         'ajax_nonce' => wp_create_nonce( 'seokar_general_settings_nonce' ),
                //     )
                // );

                // فایل CSS برای صفحه تنظیمات عمومی (اگر استایل‌های خاصی دارد)
                // wp_enqueue_style(
                //     'seokar-admin-general-settings-styles',
                //     SEOKAR_ASSETS_URL . 'css/admin-general-settings.css', // این فایل باید ایجاد شود
                //     array(),
                //     SEOKAR_VERSION
                // );
            }

            /**
             * Fires when admin assets are being enqueued for SeoKar pages.
             * @since 0.1.0
             * @param string $hook_suffix The current admin page hook.
             */
            do_action( 'seokar_enqueue_admin_assets', $hook_suffix );
        }

        /**
         * تابع اجرا شونده هنگام فعال‌سازی افزونه.
         *
         * @since  0.1.0
         * @access public
         * @static
         */
        public static function activate() {
            $current_version = defined('SEOKAR_VERSION') ? SEOKAR_VERSION : '0.1.0';

            $general_settings_option_name = 'seokar_settings_general';
            $general_settings = get_option( $general_settings_option_name, array() );
            if ( ! is_array( $general_settings ) ) {
                $general_settings = array();
            }

            $general_settings['version'] = $current_version;
            if ( ! isset( $general_settings['setup_wizard_completed'] ) ) {
                $general_settings['setup_wizard_completed'] = false;
            }
            update_option( $general_settings_option_name, $general_settings );

            if ( false === get_option( 'seokar_initial_version' ) ) {
                update_option( 'seokar_initial_version', $current_version );
            }
            update_option( 'seokar_current_version', $current_version );

            if ( ! $general_settings['setup_wizard_completed'] ) {
                 set_transient( 'seokar_activation_redirect', true, 30 );
            }

            /**
             * Fires when the SeoKar plugin is activated.
             * @since 0.1.0
             * @param string $current_version The current version of the plugin.
             */
            do_action( 'seokar_activated_hook', $current_version );

            flush_rewrite_rules();
        }

        /**
         * تابع اجرا شونده هنگام غیرفعال‌سازی افزونه.
         *
         * @since  0.1.0
         * @access public
         * @static
         */
        public static function deactivate() {
            // wp_clear_scheduled_hook( 'seokar_example_cron_hook' );
            delete_transient( 'seokar_activation_redirect' );

            /**
             * Fires when the SeoKar plugin is deactivated.
             * @since 0.1.0
             */
            do_action( 'seokar_deactivated_hook' );

            flush_rewrite_rules();
        }

        /**
         * متد اصلی برای اجرای منطق اصلی افزونه و بارگذاری ماژول‌ها.
         *
         * @since  0.1.0
         * @access public
         */
        public function run() {
            /**
             * Fires before the main plugin logic in run() method is executed.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_before_run_hook', $this );

            // مثال: بارگذاری و نمونه‌سازی مدیر ماژول‌ها
            // if ( class_exists( 'SeoKar\\Core\\Module_Manager' ) ) {
            //     Core\Module_Manager::get_instance()->load_active_modules();
            // }

            // مثال: بارگذاری کلاس‌های مربوط به بخش عمومی (frontend)
            // if ( ! is_admin() && class_exists( 'SeoKar\\Public_Facing\\Frontend_Controller' ) ) {
            //     Public_Facing\Frontend_Controller::get_instance()->init();
            // }

            /**
             * Fires after the main plugin logic in run() method has been executed.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_run_complete_hook', $this );
        }

    } // پایان کلاس SeoKar\Main

} // پایان if ( ! class_exists( 'SeoKar\\Main' ) )
