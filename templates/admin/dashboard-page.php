<?php
/**
 * Template for the SeoKar Dashboard page.
 *
 * @package SeoKar\Templates\Admin
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<div class="wrap seokar-admin-page seokar-dashboard-page">
    <h1><?php echo esc_html__( 'داشبورد سئوکار', 'seokar' ); ?></h1>

    <div class="seokar-page-content">
        <p><?php esc_html_e( 'به داشبورد افزونه سئوکار خوش آمدید. در اینجا خلاصه‌ای از وضعیت سئو سایت، اعلان‌ها و لینک‌های مفید را مشاهده خواهید کرد.', 'seokar' ); ?></p>

        <div class="seokar-welcome-panel">
            <h2><?php esc_html_e( 'شروع سریع', 'seokar' ); ?></h2>
            <ul>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=seokar-general-settings' ) ); ?>"><?php esc_html_e( 'پیکربندی تنظیمات عمومی', 'seokar' ); ?></a></li>
                <?php
                // لینک جادوگر راه‌اندازی (اگر نیاز باشد و فعال باشد)
                // if ( ! get_option( 'seokar_setup_wizard_completed', false ) ) {
                //     echo '<li><a href="' . esc_url( admin_url( 'index.php?page=seokar-setup-wizard' ) ) . '">' . esc_html__( 'اجرای جادوگر راه‌اندازی', 'seokar' ) . '</a></li>';
                // }
                ?>
                <?php // <li><a href="https://seokar.click/docs" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'مشاهده مستندات', 'seokar' ); ?></a></li> ?>
            </ul>
        </div>

        <div class="seokar-boxes-grid">
            <div class="seokar-box">
                <h3><?php esc_html_e( 'امتیاز سئو کلی', 'seokar' ); ?></h3>
                <p class="seokar-score-placeholder"><?php esc_html_e( 'به زودی...', 'seokar' ); ?></p>
                <?php // در اینجا یک ویجت یا نمودار برای نمایش امتیاز کلی قرار می‌گیرد ?>
            </div>
            <div class="seokar-box">
                <h3><?php esc_html_e( 'اعلان‌های مهم', 'seokar' ); ?></h3>
                <div class="seokar-notifications-placeholder">
                    <?php
                    /**
                     * Hook for displaying dashboard notices.
                     *
                     * @since 0.1.0
                     */
                    // do_action( 'seokar_dashboard_notices' );
                    // Placeholder text if no notices are added via the hook
                    // if ( ! did_action( 'seokar_dashboard_notices' ) ) {
                         echo '<p>' . esc_html__( 'بدون اعلان جدید.', 'seokar' ) . '</p>';
                    // }
                    ?>
                </div>
                <?php // لیست اعلان‌ها در اینجا قرار می‌گیرد ?>
            </div>
        </div>
        <?php
        /**
         * Hook for adding custom widgets or content to the SeoKar dashboard.
         *
         * @since 0.1.0
         */
        do_action( 'seokar_dashboard_widgets' );
        ?>
    </div>
</div>
