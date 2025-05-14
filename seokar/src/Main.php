<?php
/**
 * کلاس اصلی افزونه سئوکار.
 *
 * @package SeoKar
 * @since 0.1.0
 */

namespace SeoKar;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'SeoKar\\Main' ) ) {

    final class Main {

        private static $instance = null;
        public $version = '0.1.0';
        private $plugin_file_path;

        private function __construct( $plugin_file_path ) {
            $this->plugin_file_path = $plugin_file_path;

            do_action( 'seokar_before_constants_defined', $this );
            $this->define_constants();
            do_action( 'seokar_after_constants_defined', $this );

            do_action( 'seokar_before_init_hooks', $this );
            $this->init_hooks();
            do_action( 'seokar_after_init_hooks', $this );

            do_action( 'seokar_before_core_components_loaded', $this );
            $this->load_core_components();
            do_action( 'seokar_after_core_components_loaded', $this );
        }

        private function __clone() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is not allowed for the SeoKar Main class.', 'seokar' ), $this->version );
        }

        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of the SeoKar Main class is not allowed.', 'seokar' ), $this->version );
        }

        public static function get_instance( $plugin_file_path = null ) {
            if ( null === self::$instance ) {
                if ( null === $plugin_file_path ) {
                    throw new \Exception( 'Plugin file path must be provided on first instantiation of SeoKar\\Main class.' );
                }
                self::$instance = new self( $plugin_file_path );
            }
            return self::$instance;
        }

        private function define_constants() {
            if ( ! defined( 'SEOKAR_VERSION' ) ) define( 'SEOKAR_VERSION', $this->version );
            if ( ! defined( 'SEOKAR_PLUGIN_FILE' ) ) define( 'SEOKAR_PLUGIN_FILE', $this->plugin_file_path );
            if ( ! defined( 'SEOKAR_PLUGIN_BASENAME' ) ) define( 'SEOKAR_PLUGIN_BASENAME', plugin_basename( SEOKAR_PLUGIN_FILE ) );
            if ( ! defined( 'SEOKAR_TEXT_DOMAIN' ) ) define( 'SEOKAR_TEXT_DOMAIN', 'seokar' );
            if ( ! defined( 'SEOKAR_PLUGIN_DIR' ) ) define( 'SEOKAR_PLUGIN_DIR', plugin_dir_path( SEOKAR_PLUGIN_FILE ) );
            if ( ! defined( 'SEOKAR_SRC_DIR' ) ) define( 'SEOKAR_SRC_DIR', SEOKAR_PLUGIN_DIR . 'src/' );
            if ( ! defined( 'SEOKAR_ADMIN_FILES_DIR' ) ) define( 'SEOKAR_ADMIN_FILES_DIR', SEOKAR_PLUGIN_DIR . 'admin/' );
            if ( ! defined( 'SEOKAR_PUBLIC_FILES_DIR' ) ) define( 'SEOKAR_PUBLIC_FILES_DIR', SEOKAR_PLUGIN_DIR . 'public/' );
            if ( ! defined( 'SEOKAR_TEMPLATES_DIR' ) ) define( 'SEOKAR_TEMPLATES_DIR', SEOKAR_PLUGIN_DIR . 'templates/' );
            if ( ! defined( 'SEOKAR_ASSETS_DIR' ) ) define( 'SEOKAR_ASSETS_DIR', SEOKAR_PLUGIN_DIR . 'assets/' );
            if ( ! defined( 'SEOKAR_LANG_DIR_NAME' ) ) define( 'SEOKAR_LANG_DIR_NAME', 'languages' );
            if ( ! defined( 'SEOKAR_LANG_DIR' ) ) define( 'SEOKAR_LANG_DIR', SEOKAR_PLUGIN_DIR . SEOKAR_LANG_DIR_NAME . '/' );
            if ( ! defined( 'SEOKAR_VENDOR_DIR' ) ) define( 'SEOKAR_VENDOR_DIR', SEOKAR_PLUGIN_DIR . 'vendor/' );
            if ( ! defined( 'SEOKAR_PLUGIN_URL' ) ) define( 'SEOKAR_PLUGIN_URL', plugin_dir_url( SEOKAR_PLUGIN_FILE ) );
            if ( ! defined( 'SEOKAR_ASSETS_URL' ) ) define( 'SEOKAR_ASSETS_URL', SEOKAR_PLUGIN_URL . 'assets/' );
            do_action( 'seokar_define_additional_constants', $this );
        }

        private function init_hooks() {
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
            register_activation_hook( SEOKAR_PLUGIN_FILE, array( __CLASS__, 'activate' ) );
            register_deactivation_hook( SEOKAR_PLUGIN_FILE, array( __CLASS__, 'deactivate' ) );
            if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
                add_action( 'plugins_loaded', array( $this, 'load_admin_components' ), 15 );
            }
            add_action( 'plugins_loaded', array( $this, 'run' ), 20 );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        }

        private function load_core_components() {
            do_action( 'seokar_core_components_loaded_hook', $this );
        }

        public function load_admin_components() {
            if ( class_exists( 'SeoKar\\Admin\\Menu_Manager' ) ) {
                \SeoKar\Admin\Menu_Manager::get_instance();
            }
            if ( class_exists( 'SeoKar\\Admin\\Settings\\General_Settings_Page' ) ) {
                \SeoKar\Admin\Settings\General_Settings_Page::get_instance();
            }
            do_action( 'seokar_admin_components_loaded_hook', $this );
        }

        public function load_textdomain() {
            do_action( 'seokar_before_load_textdomain', $this );
            load_plugin_textdomain( SEOKAR_TEXT_DOMAIN, false, SEOKAR_LANG_DIR_NAME );
            do_action( 'seokar_after_load_textdomain', $this );
        }

        public function enqueue_admin_assets( $hook_suffix ) {
            $current_screen = get_current_screen();
            if ( $current_screen && $current_screen->id === 'seokar_page_seokar-general-settings' ) {
                wp_enqueue_script(
                    'seokar-admin-general-settings',
                    SEOKAR_ASSETS_URL . 'js/admin-general-settings.js',
                    array( 'jquery', 'wp-media' ),
                    SEOKAR_VERSION,
                    true
                );
                // Placeholder for future localization
                // wp_localize_script(
                //     'seokar-admin-general-settings',
                //     'seokarGeneralSettingsParams',
                //     array(
                //         'media_uploader_title' => __( 'Select or Upload Logo', 'seokar' ),
                //         'media_uploader_button_text' => __( 'Use this logo', 'seokar' ),
                //     )
                // );
            }
            do_action( 'seokar_enqueue_admin_assets', $hook_suffix );
        }

        public static function activate() {
            $current_version = defined('SEOKAR_VERSION') ? SEOKAR_VERSION : '0.1.0';
            $general_settings_option_name = 'seokar_settings_general';
            $general_settings = get_option( $general_settings_option_name, array() );
            if ( ! is_array( $general_settings ) ) $general_settings = array();
            $general_settings['version'] = $current_version;
            if ( ! isset( $general_settings['setup_wizard_completed'] ) ) $general_settings['setup_wizard_completed'] = false;
            update_option( $general_settings_option_name, $general_settings );
            if ( false === get_option( 'seokar_initial_version' ) ) update_option( 'seokar_initial_version', $current_version );
            update_option( 'seokar_current_version', $current_version );
            if ( ! $general_settings['setup_wizard_completed'] ) set_transient( 'seokar_activation_redirect', true, 30 );
            do_action( 'seokar_activated_hook', $current_version );
            flush_rewrite_rules();
        }

        public static function deactivate() {
            delete_transient( 'seokar_activation_redirect' );
            do_action( 'seokar_deactivated_hook' );
            flush_rewrite_rules();
        }

        public function run() {
            do_action( 'seokar_before_run_hook', $this );
            // Main plugin logic and module loading will go here
            do_action( 'seokar_run_complete_hook', $this );
        }
    }
}
