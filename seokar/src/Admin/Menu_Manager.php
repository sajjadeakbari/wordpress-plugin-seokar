<?php
/**
 * SeoKar Admin Menu Manager
 *
 * @package SeoKar\Admin
 * @since 0.1.0
 */
namespace SeoKar\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'SeoKar\\Admin\\Menu_Manager' ) ) {
    class Menu_Manager {
        private static $instance = null;
        public $main_menu_slug = 'seokar-dashboard';
        public $capability = 'manage_options';

        private function __construct() {
            add_action( 'admin_menu', array( $this, 'register_menus' ) );
        }

        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function register_menus() {
            add_menu_page(
                esc_html__( 'SeoKar Dashboard', 'seokar' ),
                esc_html__( 'سئوکار', 'seokar' ),
                $this->capability,
                $this->main_menu_slug,
                array( $this, 'display_dashboard_page' ),
                'dashicons-chart-area',
                26
            );
            add_submenu_page(
                $this->main_menu_slug,
                esc_html__( 'General Settings - SeoKar', 'seokar' ),
                esc_html__( 'تنظیمات عمومی', 'seokar' ),
                $this->capability,
                'seokar-general-settings',
                array( $this, 'display_general_settings_page' )
            );
            add_submenu_page(
                $this->main_menu_slug,
                esc_html__( 'SeoKar Dashboard', 'seokar' ),
                esc_html__( 'داشبورد', 'seokar' ),
                $this->capability,
                $this->main_menu_slug,
                array( $this, 'display_dashboard_page' )
            );
            do_action( 'seokar_register_submenu_pages', $this->main_menu_slug, $this->capability );
        }

        public function display_dashboard_page() {
            if ( defined( 'SEOKAR_TEMPLATES_DIR' ) ) {
                $template_path = SEOKAR_TEMPLATES_DIR . 'admin/dashboard-page.php';
                if ( file_exists( $template_path ) ) require_once $template_path;
                else echo '<div class="wrap"><h1>' . esc_html__( 'Template Error', 'seokar' ) . '</h1><p>' . sprintf( esc_html__( 'Dashboard template file not found at: %s', 'seokar' ), esc_html( $template_path ) ) . '</p></div>';
            } else echo '<div class="wrap"><h1>' . esc_html__( 'Configuration Error', 'seokar' ) . '</h1><p>' . esc_html__( 'The SEOKAR_TEMPLATES_DIR constant is not defined.', 'seokar' ) . '</p></div>';
        }

        public function display_general_settings_page() {
            if ( defined( 'SEOKAR_TEMPLATES_DIR' ) ) {
                $template_path = SEOKAR_TEMPLATES_DIR . 'admin/general-settings-page.php';
                if ( file_exists( $template_path ) ) require_once $template_path;
                else echo '<div class="wrap"><h1>' . esc_html__( 'Template Error', 'seokar' ) . '</h1><p>' . sprintf( esc_html__( 'General settings template file not found at: %s', 'seokar' ), esc_html( $template_path ) ) . '</p></div>';
            } else echo '<div class="wrap"><h1>' . esc_html__( 'Configuration Error', 'seokar' ) . '</h1><p>' . esc_html__( 'The SEOKAR_TEMPLATES_DIR constant is not defined.', 'seokar' ) . '</p></div>';
        }

        public function display_page_placeholder( $page_title = '' ) {
            if ( empty( $page_title ) ) $page_title = esc_html__( 'SeoKar Page', 'seokar' );
            echo '<div class="wrap seokar-admin-page seokar-placeholder-page"><h1>' . esc_html( $page_title ) . '</h1><hr class="wp-header-end"><p>' . esc_html__( 'This page is part of SeoKar plugin and is currently under development.', 'seokar' ) . '</p></div>';
        }
    }
}
