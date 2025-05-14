<?php
/**
 * Template for the SeoKar General Settings page.
 * @package SeoKar\Templates\Admin
 * @since 0.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap seokar-admin-page seokar-settings-page seokar-general-settings-page">
    <h1><?php echo esc_html__( 'تنظیمات عمومی سئوکار', 'seokar' ); ?></h1>
    <?php settings_errors( 'seokar_settings_general_group' ); ?>
    <form method="post" action="options.php" novalidate="novalidate">
        <?php
        settings_fields( 'seokar_settings_general_group' );
        do_settings_sections( 'seokar_page_general_settings' );
        do_action( 'seokar_before_general_settings_submit_button' );
        submit_button( esc_html__( 'ذخیره تغییرات', 'seokar' ) );
        ?>
    </form>
    <?php do_action( 'seokar_after_general_settings_form' ); ?>
</div>
