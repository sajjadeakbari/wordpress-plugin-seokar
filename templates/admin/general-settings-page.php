<?php
/**
 * Template for the SeoKar General Settings page.
 *
 * @package SeoKar\Templates\Admin
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// این صفحه در آینده از طریق یک کلاس کنترلر برای Settings API مدیریت خواهد شد.
// $settings_controller = \SeoKar\Admin\Settings\General_Settings_Controller::get_instance();
// $active_tab = $settings_controller->get_active_tab();
?>
<div class="wrap seokar-admin-page seokar-settings-page seokar-general-settings-page">
    <h1><?php echo esc_html__( 'تنظیمات عمومی سئوکار', 'seokar' ); ?></h1>

    <?php settings_errors( 'seokar_settings_general_group' ); // نمایش خطاهای ذخیره تنظیمات برای گروه مشخص ?>

    <?php
    // در آینده تب‌ها در اینجا قرار خواهند گرفت
    // echo '<h2 class="nav-tab-wrapper wp-clearfix">';
    // $settings_controller->render_tabs();
    // echo '</h2>';
    ?>

    <form method="post" action="options.php" novalidate="novalidate">
        <?php
        /**
         * این گروه تنظیمات و بخش‌ها باید در کلاس مدیریت تنظیمات این صفحه تعریف شوند.
         * در حال حاضر، نام گروه و نامک صفحه به صورت رشته‌ای وارد شده‌اند.
         * در پیاده‌سازی واقعی Settings API، اینها از طریق کلاس مربوطه مدیریت می‌شوند.
         */
        settings_fields( 'seokar_settings_general_group' ); // نام گروه تنظیمات

        // محتوای بخش‌های تنظیمات بر اساس تب فعال (در آینده)
        // $settings_controller->render_sections_for_tab( $active_tab );
        // یا اگر تب وجود ندارد:
        do_settings_sections( 'seokar_page_general_settings' ); // نامک (slug) این صفحه تنظیمات برای نمایش بخش‌ها

        /**
         * Hook for adding custom content or fields before the submit button.
         *
         * @since 0.1.0
         * @param string $active_tab (In future, the active tab slug might be passed here)
         */
        do_action( 'seokar_before_general_settings_submit_button' /*, $active_tab */ );

        submit_button( esc_html__( 'ذخیره تغییرات', 'seokar' ) );
        ?>
    </form>
    <?php
    /**
     * Hook for adding custom content after the general settings form.
     *
     * @since 0.1.0
     */
    do_action( 'seokar_after_general_settings_form' );
    ?>
</div>
