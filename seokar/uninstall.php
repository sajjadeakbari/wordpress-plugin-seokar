<?php
/**
 * SeoKar Uninstall
 *
 * این فایل مسئول پاکسازی کامل اطلاعات افزونه سئوکار هنگام حذف آن توسط کاربر است.
 *
 * @package SeoKar
 * @since 0.1.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// --- ۱. حذف گزینه‌های (Options) افزونه ---
$seokar_options_to_delete = array(
    'seokar_settings_general',
    'seokar_settings_titles_metas',
    'seokar_settings_sitemap',
    'seokar_settings_social_meta',
    'seokar_settings_breadcrumbs',
    'seokar_settings_analytics',
    'seokar_settings_search_console',
    'seokar_settings_redirections',
    'seokar_settings_404_monitor',
    'seokar_settings_local_seo',
    'seokar_settings_woocommerce',
    'seokar_settings_role_manager',
    'seokar_settings_advanced',
    'seokar_settings_license',
    'seokar_initial_version',
    'seokar_current_version',
    'seokar_active_modules',
    'seokar_db_version',
);

if ( ! empty( $seokar_options_to_delete ) ) {
    foreach ( $seokar_options_to_delete as $option_name ) {
        delete_option( $option_name );
        if ( is_multisite() ) {
            delete_site_option( $option_name );
        }
    }
}

// حذف تمام Transient های سئوکار
$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_transient_seokar_%'" );
$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_transient_timeout_seokar_%'" );
if ( is_multisite() ) {
    $wpdb->query( "DELETE FROM `{$wpdb->sitemeta}` WHERE `meta_key` LIKE '_site_transient_seokar_%'" );
    $wpdb->query( "DELETE FROM `{$wpdb->sitemeta}` WHERE `meta_key` LIKE '_site_transient_timeout_seokar_%'" );
}

// --- ۲. حذف متادیتاهای پست (Post Meta) ---
$seokar_post_meta_keys_to_delete = array(
    '_seokar_primary_term', '_seokar_focus_keyword', '_seokar_focus_keywords_additional',
    '_seokar_analysis_score_readability', '_seokar_analysis_score_seo',
    '_seokar_meta_title', '_seokar_meta_description', '_seokar_canonical_url',
    '_seokar_robots_meta_index', '_seokar_robots_meta_follow', '_seokar_robots_meta_advanced',
    '_seokar_og_title', '_seokar_og_description', '_seokar_og_image', '_seokar_og_image_id',
    '_seokar_twitter_card_type', '_seokar_twitter_title', '_seokar_twitter_description',
    '_seokar_twitter_image', '_seokar_twitter_image_id',
    '_seokar_schema_type', '_seokar_schema_data_custom', '_seokar_breadcrumb_title',
    '_seokar_redirect_active', '_seokar_redirect_url', '_seokar_redirect_type',
    '_seokar_woocommerce_gtin', '_seokar_woocommerce_isbn', '_seokar_woocommerce_mpn',
    '_seokar_woocommerce_show_rich_snippet',
);
if ( ! empty( $seokar_post_meta_keys_to_delete ) ) {
    $meta_keys_sql_in = "'" . implode( "','", array_map( 'esc_sql', $seokar_post_meta_keys_to_delete ) ) . "'";
    $wpdb->query( "DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` IN ({$meta_keys_sql_in})" );
}

// --- ۳. حذف متادیتاهای ترم (Term Meta) ---
$seokar_term_meta_keys_to_delete = array(
    'seokar_term_meta_title', 'seokar_term_meta_description', 'seokar_term_robots_meta_index',
    'seokar_term_robots_meta_follow', 'seokar_term_og_title', 'seokar_term_og_description',
    'seokar_term_og_image', 'seokar_term_twitter_title', 'seokar_term_twitter_description',
    'seokar_term_twitter_image', 'seokar_term_canonical_url',
);
if ( ! empty( $seokar_term_meta_keys_to_delete ) ) {
    $term_meta_keys_sql_in = "'" . implode( "','", array_map( 'esc_sql', $seokar_term_meta_keys_to_delete ) ) . "'";
    $wpdb->query( "DELETE FROM `{$wpdb->termmeta}` WHERE `meta_key` IN ({$term_meta_keys_sql_in})" );
}

// --- ۴. حذف متادیتاهای کاربر (User Meta) ---
$seokar_user_meta_keys_to_delete = array(
    'seokar_user_disable_seo_metabox', 'seokar_user_social_facebook_url',
    'seokar_user_social_twitter_handle', 'seokar_user_linkedin_url',
);
if ( ! empty( $seokar_user_meta_keys_to_delete ) ) {
    $user_meta_keys_sql_in = "'" . implode( "','", array_map( 'esc_sql', $seokar_user_meta_keys_to_delete ) ) . "'";
    $wpdb->query( "DELETE FROM `{$wpdb->usermeta}` WHERE `meta_key` IN ({$user_meta_keys_sql_in})" );
}

// --- ۵. حذف جداول سفارشی دیتابیس ---
$seokar_custom_tables_suffixes = array(
    'seokar_redirections', 'seokar_redirection_logs', 'seokar_404_logs',
    'seokar_seo_analysis_cache', 'seokar_keyword_rankings',
    'seokar_analytics_stats_cache', 'seokar_internal_links', 'seokar_schema_templates',
);
foreach ( $seokar_custom_tables_suffixes as $table_suffix ) {
    $table_name = $wpdb->prefix . $table_suffix;
    $wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );
}

// --- ۶. حذف Cron Job های برنامه‌ریزی شده ---
$seokar_cron_hooks_to_clear = array(
    'seokar_sitemap_generation_cron', 'seokar_sitemap_ping_cron', 'seokar_404_log_cleanup_cron',
    'seokar_rank_tracker_update_cron', 'seokar_analytics_data_fetch_cron',
    'seokar_broken_link_checker_cron', 'seokar_database_optimization_cron',
);
foreach ( $seokar_cron_hooks_to_clear as $hook_name ) {
    $timestamps = wp_get_scheduled_event( $hook_name, array(), null, true );
    if ( ! empty( $timestamps ) ) {
        foreach( $timestamps as $timestamp => $event_data ) {
            foreach( $event_data as $args_hash => $event ) {
                 wp_unschedule_event( $timestamp, $hook_name, $event['args'] );
            }
        }
    }
}

// --- ۷. حذف قابلیت‌های سفارشی ---
$seokar_custom_capabilities = array(
    'manage_seokar_settings', 'view_seokar_reports',
    'manage_seokar_redirections', 'manage_seokar_sitemap',
);
global $wp_roles;
if ( is_object( $wp_roles ) ) {
    foreach ( $wp_roles->roles as $role_name => $role_info ) {
        $role = get_role( $role_name );
        if ( $role ) {
            foreach ( $seokar_custom_capabilities as $cap ) {
                if ( $role->has_cap( $cap ) ) {
                    $role->remove_cap( $cap );
                }
            }
        }
    }
}
// حذف نقش‌های سفارشی (اگر وجود داشته باشند)
// if ( get_role( 'seokar_seo_manager' ) ) { remove_role( 'seokar_seo_manager' ); }

// --- ۸. پاک کردن کش‌ها ---
if ( function_exists( 'wp_cache_flush' ) ) {
    wp_cache_flush();
}

if ( has_action('seokar_uninstall_complete') ) {
    do_action( 'seokar_uninstall_complete' );
}
?>
