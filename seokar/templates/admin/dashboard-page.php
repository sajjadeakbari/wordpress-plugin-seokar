<?php
/**
 * Template for the SeoKar Dashboard page.
 * @package SeoKar\Templates\Admin
 * @since 0.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap seokar-admin-page seokar-dashboard-page">
    <h1><?php echo esc_html__( 'داشبورد سئوکار', 'seokar' ); ?></h1>
    <div class="seokar-page-content">
        <p><?php esc_html_e( 'به داشبورد افزونه سئوکار خوش آمدید. در اینجا خلاصه‌ای از وضعیت سئو سایت، اعلان‌ها و لینک‌های مفید را مشاهده خواهید کرد.', 'seokar' ); ?></p>
        <div class="seokar-welcome-panel">
            <h2><?php esc_html_e( 'شروع سریع', 'seokar' ); ?></h2>
            <ul>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=seokar-general-settings' ) ); ?>"><?php esc_html_e( 'پیکربندی تنظیمات عمومی', 'seokar' ); ?></a></li>
            </ul>
        </div>
        <div class="seokar-boxes-grid">
            <div class="seokar-box">
                <h3><?php esc_html_e( 'امتیاز سئو کلی', 'seokar' ); ?></h3>
                <p class="seokar-score-placeholder"><?php esc_html_e( 'به زودی...', 'seokar' ); ?></p>
            </div>
            <div class="seokar-box">
                <h3><?php esc_html_e( 'اعلان‌های مهم', 'seokar' ); ?></h3>
                <div class="seokar-notifications-placeholder"><p><?php esc_html_e( 'بدون اعلان جدید.', 'seokar' ); ?></p></div>
            </div>
        </div>
        <?php do_action( 'seokar_dashboard_widgets' ); ?>
    </div>
</div>
