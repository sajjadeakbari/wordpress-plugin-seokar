<?php
/**
 * SeoKar General Settings Page
 *
 * @package SeoKar\Admin\Settings
 * @since 0.1.0
 */
namespace SeoKar\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'SeoKar\\Admin\\Settings\\General_Settings_Page' ) ) {
    class General_Settings_Page {
        private static $instance = null;
        private $option_group = 'seokar_settings_general_group';
        private $option_name = 'seokar_settings_general';
        private $page_slug = 'seokar_page_general_settings';
        private $default_settings;

        private function __construct() {
            $this->set_default_settings();
            add_action( 'admin_init', array( $this, 'register_settings_and_fields' ) );
        }

        public static function get_instance() {
            if ( null === self::$instance ) self::$instance = new self();
            return self::$instance;
        }

        private function set_default_settings() {
            $this->default_settings = apply_filters( 'seokar_general_settings_defaults', array(
                'site_knowledge_type'   => 'organization',
                'site_organization_name'=> get_bloginfo( 'name' ),
                'site_organization_logo'=> '',
                'site_person_name'      => '',
                'separator_char'        => '-',
            ) );
        }

        public function get_option( $key ) {
            $options = get_option( $this->option_name, $this->default_settings );
            $options = wp_parse_args( $options, $this->default_settings );
            return isset( $options[ $key ] ) ? $options[ $key ] : null;
        }

        public function register_settings_and_fields() {
            register_setting( $this->option_group, $this->option_name, array( $this, 'sanitize_general_settings' ) );

            add_settings_section( 'seokar_section_site_knowledge', esc_html__( 'دانش‌نامه سایت و اطلاعات پایه', 'seokar' ), array( $this, 'render_section_site_knowledge_description' ), $this->page_slug );
            add_settings_section( 'seokar_section_title_separator', esc_html__( 'جداکننده عنوان', 'seokar' ), null, $this->page_slug );

            add_settings_field( 'site_knowledge_type', esc_html__( 'نوع سایت', 'seokar' ), array( $this, 'render_field_site_knowledge_type' ), $this->page_slug, 'seokar_section_site_knowledge', array( 'label_for' => 'seokar_site_knowledge_type' ) );
            add_settings_field( 'site_organization_name', esc_html__( 'نام سازمان', 'seokar' ), array( $this, 'render_field_site_organization_name' ), $this->page_slug, 'seokar_section_site_knowledge', array( 'label_for' => 'seokar_site_organization_name', 'class' => 'seokar-field-organization' ) );
            add_settings_field( 'site_organization_logo', esc_html__( 'لوگوی سازمان', 'seokar' ), array( $this, 'render_field_site_organization_logo' ), $this->page_slug, 'seokar_section_site_knowledge', array( 'label_for' => 'seokar_site_organization_logo', 'class' => 'seokar-field-organization' ) );
            add_settings_field( 'site_person_name', esc_html__( 'نام شخص', 'seokar' ), array( $this, 'render_field_site_person_name' ), $this->page_slug, 'seokar_section_site_knowledge', array( 'label_for' => 'seokar_site_person_name', 'class' => 'seokar-field-person' ) );
            add_settings_field( 'separator_char', esc_html__( 'کاراکتر جداکننده', 'seokar' ), array( $this, 'render_field_separator_char' ), $this->page_slug, 'seokar_section_title_separator', array( 'label_for' => 'seokar_separator_char' ) );

            do_action( 'seokar_register_general_settings_fields', $this->page_slug, $this->option_group, $this->option_name );
        }

        public function render_section_site_knowledge_description() {
            echo '<p>' . esc_html__( 'این اطلاعات به موتورهای جستجو کمک می‌کند تا وب‌سایت شما را بهتر بشناسند.', 'seokar' ) . '</p>';
        }

        public function render_field_site_knowledge_type( $args ) {
            $field_id = 'seokar_site_knowledge_type'; $value = $this->get_option( 'site_knowledge_type' );
            echo "<select id='" . esc_attr( $field_id ) . "' name='" . esc_attr( $this->option_name . '[site_knowledge_type]' ) . "'>";
            echo "<option value='organization' " . selected( $value, 'organization', false ) . ">" . esc_html__( 'سازمان', 'seokar' ) . "</option>";
            echo "<option value='person' " . selected( $value, 'person', false ) . ">" . esc_html__( 'شخص', 'seokar' ) . "</option>";
            echo "</select><p class='description'>" . esc_html__( 'مشخص کنید که این وب‌سایت نماینده یک سازمان/شرکت است یا یک شخص.', 'seokar' ) . "</p>";
        }
        public function render_field_site_organization_name( $args ) {
            $value = $this->get_option( 'site_organization_name' );
            echo "<input type='text' id='" . esc_attr( $args['label_for'] ) . "' name='" . esc_attr( $this->option_name . '[site_organization_name]' ) . "' value='" . esc_attr( $value ) . "' class='regular-text' /><p class='description'>" . esc_html__( 'نام کامل سازمان یا شرکت شما.', 'seokar' ) . "</p>";
        }
        public function render_field_site_organization_logo( $args ) {
            $value = $this->get_option( 'site_organization_logo' ); $image_url = ''; $image_id = 0;
            if ( is_numeric( $value ) && $value > 0 ) { $image_id = (int) $value; $image_src = wp_get_attachment_image_src( $image_id, 'medium' ); if ( $image_src ) $image_url = $image_src[0]; }
            elseif ( filter_var( $value, FILTER_VALIDATE_URL ) ) $image_url = $value;
            wp_enqueue_media();
            echo "<div class='seokar-image-uploader'><input type='hidden' id='" . esc_attr( $args['label_for'] ) . "' name='" . esc_attr( $this->option_name . '[site_organization_logo]' ) . "' value='" . esc_attr( $value ) . "' class='seokar-image-id-field' />";
            echo "<img src='" . esc_url( $image_url ) . "' class='seokar-image-preview" . (empty($image_url) ? ' hidden' : '') . "' style='max-width: 150px; height: auto; border: 1px solid #ddd; margin-bottom: 10px; display:" . (empty($image_url) ? 'none' : 'block') . ";' />";
            echo "<button type='button' class='button seokar-upload-image-button'>" . esc_html__( 'انتخاب/آپلود لوگو', 'seokar' ) . "</button> ";
            echo "<button type='button' class='button seokar-remove-image-button" . (empty($image_url) ? ' hidden' : '') . "' style='display:" . (empty($image_url) ? 'none' : 'inline-block') . ";'>" . esc_html__( 'حذف لوگو', 'seokar' ) . "</button></div>";
            echo "<p class='description'>" . esc_html__( 'لوگوی رسمی سازمان شما. حداقل ۱۱۲×۱۱۲ پیکسل.', 'seokar' ) . "</p>";
        }
        public function render_field_site_person_name( $args ) {
            $value = $this->get_option( 'site_person_name' );
            echo "<input type='text' id='" . esc_attr( $args['label_for'] ) . "' name='" . esc_attr( $this->option_name . '[site_person_name]' ) . "' value='" . esc_attr( $value ) . "' class='regular-text' /><p class='description'>" . esc_html__( 'نام کامل شخصی که این وب‌سایت نماینده اوست.', 'seokar' ) . "</p>";
        }
        public function render_field_separator_char( $args ) {
            $value = $this->get_option( 'separator_char' );
            $separators = apply_filters('seokar_title_separators', array( '-' => '-', '–' => '– (en dash)', '—' => '— (em dash)', '•' => '•', '*' => '*', '|' => '|', '~' => '~', '«' => '«', '»' => '»', '‹' => '‹', '›' => '›' ));
            echo "<select id='" . esc_attr( $args['label_for'] ) . "' name='" . esc_attr( $this->option_name . '[separator_char]' ) . "'>";
            foreach ( $separators as $char => $label ) echo "<option value='" . esc_attr( $char ) . "' " . selected( $value, $char, false ) . ">" . esc_html( $label ) . "</option>";
            echo "</select><p class='description'>" . esc_html__( 'کاراکتری که برای جدا کردن بخش‌های مختلف عنوان سئو استفاده می‌شود.', 'seokar' ) . "</p>";
        }

        public function sanitize_general_settings( $input ) {
            $sanitized_input = array(); $current_options = get_option( $this->option_name, $this->default_settings );
            $current_options = wp_parse_args( $current_options, $this->default_settings );

            if ( isset( $input['site_knowledge_type'] ) ) {
                $sanitized_input['site_knowledge_type'] = sanitize_key( $input['site_knowledge_type'] );
                if ( ! in_array( $sanitized_input['site_knowledge_type'], array( 'person', 'organization' ) ) ) {
                    $sanitized_input['site_knowledge_type'] = $current_options['site_knowledge_type'];
                    add_settings_error( $this->option_group, 'invalid_knowledge_type', esc_html__( 'نوع سایت انتخاب شده نامعتبر است.', 'seokar' ), 'error' );
                }
            } else $sanitized_input['site_knowledge_type'] = $current_options['site_knowledge_type'];

            if ( isset( $input['site_organization_name'] ) ) $sanitized_input['site_organization_name'] = sanitize_text_field( $input['site_organization_name'] );
            else $sanitized_input['site_organization_name'] = $current_options['site_organization_name'];

            if ( isset( $input['site_organization_logo'] ) ) {
                if ( is_numeric( $input['site_organization_logo'] ) && intval( $input['site_organization_logo'] ) > 0 ) $sanitized_input['site_organization_logo'] = intval( $input['site_organization_logo'] );
                elseif ( filter_var( $input['site_organization_logo'], FILTER_VALIDATE_URL ) ) $sanitized_input['site_organization_logo'] = esc_url_raw( $input['site_organization_logo'] );
                else $sanitized_input['site_organization_logo'] = '';
            } else $sanitized_input['site_organization_logo'] = $current_options['site_organization_logo'];

            if ( isset( $input['site_person_name'] ) ) $sanitized_input['site_person_name'] = sanitize_text_field( $input['site_person_name'] );
            else $sanitized_input['site_person_name'] = $current_options['site_person_name'];

            if ( isset( $input['separator_char'] ) ) {
                $allowed_separators = array_keys(apply_filters('seokar_title_separators', array( '-' => '-', '–' => '–', '—' => '—', '•' => '•', '*' => '*', '|' => '|', '~' => '~', '«' => '«', '»' => '»', '‹' => '‹', '›' => '›' )));
                if ( in_array( $input['separator_char'], $allowed_separators, true ) ) $sanitized_input['separator_char'] = $input['separator_char'];
                else {
                    $sanitized_input['separator_char'] = $current_options['separator_char'];
                    add_settings_error( $this->option_group, 'invalid_separator', esc_html__( 'کاراکتر جداکننده انتخاب شده نامعتبر است.', 'seokar' ), 'error' );
                }
            } else $sanitized_input['separator_char'] = $current_options['separator_char'];

            $sanitized_input = wp_parse_args( $sanitized_input, $current_options );
            return apply_filters( 'seokar_sanitize_general_settings_input', $sanitized_input, $input, $current_options );
        }
    }
}
