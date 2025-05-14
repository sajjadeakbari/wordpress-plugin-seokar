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

            // هوک‌های اکشن و فیلتر داخلی برای توسعه‌پذیری
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

            // Autoloader توسط Composer مدیریت می‌شود و در seokar.php بارگذاری شده است.

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
                    // این حالت نباید رخ دهد اگر از تابع کمکی SeoKar() در seokar.php استفاده شود.
                    throw new \Exception( 'Plugin file path must be provided on first instantiation of SeoKar\\Main class.' );
                }
                self::$instance = new self( $plugin_file_path );
            }
            return self::$instance;
        }

        /**
         * تعریف ثابت‌های اصلی و کاربردی افزونه.
         * این ثابت‌ها برای دسترسی آسان به مسیرها، URLها و سایر اطلاعات مهم استفاده می‌شوند.
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
                define( 'SEOKAR_SRC_DIR', SEOKAR_PLUGIN_DIR . 'src/' ); // دایرکتوری سورس کد اصلی با namespace SeoKar
            }
            // مسیرهای پوشه‌های admin, public, templates برای فایل‌های غیر کلاسی (مانند تمپلیت‌ها، فایل‌های JS/CSS جانبی)
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
                define( 'SEOKAR_LANG_DIR_NAME', 'languages' ); // فقط نام پوشه
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
            // بارگذاری Text Domain برای ترجمه افزونه
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

            // ثبت هوک‌های فعال‌سازی و غیرفعال‌سازی افزونه
            register_activation_hook( SEOKAR_PLUGIN_FILE, array( __CLASS__, 'activate' ) );
            register_deactivation_hook( SEOKAR_PLUGIN_FILE, array( __CLASS__, 'deactivate' ) );

            // بارگذاری کامپوننت‌های مربوط به بخش مدیریت (مانند منوها)
            if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
                // اولویت 15 برای اجرا پس از بارگذاری Text Domain (که معمولاً اولویت 10 دارد)
                add_action( 'plugins_loaded', array( $this, 'load_admin_components' ), 15 );
            }

            // اجرای متد run اصلی افزونه پس از بارگذاری کامل افزونه‌ها
            // اولویت 20 برای اجرا پس از سایر بارگذاری‌های اولیه (textdomain, admin_components)
            add_action( 'plugins_loaded', array( $this, 'run' ), 20 );

            // مثال برای یک هوک که به متد run در زمان دیگری نیاز دارد
            // add_action( 'wp_loaded', array( $this, 'late_run_tasks' ) );
        }

        /**
         * بارگذاری و نمونه‌سازی کامپوننت‌های هسته‌ای افزونه.
         * این متد در کانستراکتور فراخوانی می‌شود.
         *
         * @since 0.1.0
         * @access private
         */
        private function load_core_components() {
            // مثال: بارگذاری کلاس مدیریت تنظیمات (Settings API)
            // if ( class_exists( 'SeoKar\\Core\\Settings_Manager' ) ) { // نام کلاس می‌تواند متفاوت باشد
            //     Core\Settings_Manager::get_instance();
            // }

            // مثال: بارگذاری کلاس مدیریت آپگریدها و مایگریشن‌های دیتابیس
            // if ( class_exists( 'SeoKar\\Core\\Upgrader' ) ) {
            //     Core\Upgrader::get_instance()->init_hooks(); // یا check_for_updates()
            // }

            /**
             * Fires after core components are loaded, allowing other core parts to initialize.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_core_components_loaded_hook', $this ); // تغییر نام هوک برای وضوح بیشتر
        }

        /**
         * بارگذاری کامپوننت‌های بخش مدیریت (Admin).
         * این متد از طریق هوک 'plugins_loaded' فراخوانی می‌شود.
         *
         * @since 0.1.0
         * @access public
         */
        public function load_admin_components() {
            // بارگذاری و نمونه‌سازی کلاس مدیریت منوها
            if ( class_exists( 'SeoKar\\Admin\\Menu_Manager' ) ) {
                \SeoKar\Admin\Menu_Manager::get_instance();
            }

            // مثال: بارگذاری کنترلر اصلی بخش ادمین (برای صفحات تنظیمات، متا باکس‌ها و ...)
            // if ( class_exists( 'SeoKar\\Admin\\Admin_Controller' ) ) {
            //     Admin\Admin_Controller::get_instance()->init();
            // }

            // مثال: بارگذاری جادوگر راه‌اندازی اولیه (اگر هنوز تکمیل نشده باشد)
            // $setup_wizard_completed = get_option( 'seokar_settings_general', array() )['setup_wizard_completed'] ?? false;
            // if ( class_exists( 'SeoKar\\Admin\\Setup_Wizard' ) && ! $setup_wizard_completed ) {
            //     Admin\Setup_Wizard::get_instance()->init_hooks();
            // }

            /**
             * Fires after admin components are loaded.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_admin_components_loaded_hook', $this ); // تغییر نام هوک
        }


        /**
         * بارگذاری فایل ترجمه (.mo) افزونه.
         * این متد از طریق هوک 'plugins_loaded' فراخوانی می‌شود.
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
                SEOKAR_LANG_DIR_NAME // مسیر نسبی به پوشه languages از ریشه افزونه
            );

            /**
             * Fires after the plugin text domain is loaded.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_after_load_textdomain', $this );
        }

        /**
         * تابع اجرا شونده هنگام فعال‌سازی افزونه توسط کاربر.
         * این متد به صورت استاتیک تعریف شده و از طریق register_activation_hook فراخوانی می‌شود.
         *
         * @since  0.1.0
         * @access public
         * @static
         */
        public static function activate() {
            $current_version = defined('SEOKAR_VERSION') ? SEOKAR_VERSION : '0.1.0';

            // ایجاد یا به‌روزرسانی گزینه اصلی تنظیمات عمومی
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

            // ذخیره نسخه اولیه نصب افزونه
            if ( false === get_option( 'seokar_initial_version' ) ) {
                update_option( 'seokar_initial_version', $current_version );
            }
            update_option( 'seokar_current_version', $current_version );

            // تنظیم یک transient برای ریدایرکت به جادوگر راه‌اندازی در اولین فعال‌سازی
            if ( ! $general_settings['setup_wizard_completed'] ) {
                 set_transient( 'seokar_activation_redirect', true, 30 );
            }

            /**
             * Fires when the SeoKar plugin is activated.
             * @since 0.1.0
             * @param string $current_version The current version of the plugin being activated.
             */
            do_action( 'seokar_activated_hook', $current_version ); // تغییر نام هوک

            flush_rewrite_rules();
        }

        /**
         * تابع اجرا شونده هنگام غیرفعال‌سازی افزونه توسط کاربر.
         * این متد به صورت استاتیک تعریف شده و از طریق register_deactivation_hook فراخوانی می‌شود.
         *
         * @since  0.1.0
         * @access public
         * @static
         */
        public static function deactivate() {
            // مثال: پاک کردن cron job های تعریف شده توسط افزونه
            // wp_clear_scheduled_hook( 'seokar_example_cron_hook' );

            delete_transient( 'seokar_activation_redirect' );

            /**
             * Fires when the SeoKar plugin is deactivated.
             * @since 0.1.0
             */
            do_action( 'seokar_deactivated_hook' ); // تغییر نام هوک

            flush_rewrite_rules();
        }

        /**
         * متد اصلی برای اجرای منطق اصلی افزونه و بارگذاری ماژول‌ها.
         * این متد از طریق هوک 'plugins_loaded' با اولویت بالاتر (دیرتر) فراخوانی می‌شود.
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
            do_action( 'seokar_before_run_hook', $this ); // تغییر نام هوک

            // مثال: بارگذاری و نمونه‌سازی مدیر ماژول‌ها
            // if ( class_exists( 'SeoKar\\Core\\Module_Manager' ) ) {
            //     Core\Module_Manager::get_instance()->load_modules();
            // }

            // مثال: نمونه‌سازی و اجرای کلاس‌های بخش عمومی (frontend) اگر در فرانت‌اند هستیم
            // if ( ! is_admin() && class_exists( 'SeoKar\\Public_Facing\\Output_Controller' ) ) {
            //     Public_Facing\Output_Controller::get_instance()->init_hooks();
            // }

            /**
             * Fires after the main plugin logic in run() method has been executed.
             * @since 0.1.0
             * @param Main $this Instance of the Main class.
             */
            do_action( 'seokar_run_complete_hook', $this ); // تغییر نام هوک
        }

    } // پایان کلاس SeoKar\Main

} // پایان if ( ! class_exists( 'SeoKar\\Main' ) )
