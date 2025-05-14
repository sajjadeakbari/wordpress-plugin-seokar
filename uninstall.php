<?php
/**
 * SeoKar Uninstall
 *
 * این فایل مسئول پاکسازی کامل اطلاعات افزونه سئوکار هنگام حذف آن توسط کاربر است.
 * عملیات شامل حذف گزینه‌های دیتابیس، جداول سفارشی، متادیتاها، Cron Job ها
 * و سایر داده‌های مرتبط با افزونه می‌باشد.
 *
 * @package SeoKar
 * @since 0.1.0
 */

// بررسی امنیتی: اطمینان از اینکه این فایل مستقیماً فراخوانی نشده است
// و فقط در فرآیند حذف افزونه توسط وردپرس اجرا می‌شود.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit; // خروج اگر مستقیماً فراخوانی شده باشد
}

global $wpdb;

// --- ۱. حذف گزینه‌های (Options) افزونه از جدول wp_options ---
$seokar_options_to_delete = array(
    'seokar_settings_general',        // تنظیمات عمومی (آرایه)
    'seokar_settings_titles_metas',   // تنظیمات عناوین و متاها (آرایه)
    'seokar_settings_sitemap',        // تنظیمات نقشه سایت (آرایه)
    'seokar_settings_social_meta',    // تنظیمات متاهای اجتماعی (آرایه)
    'seokar_settings_breadcrumbs',    // تنظیمات بردکرامبز (آرایه)
    'seokar_settings_analytics',      // تنظیمات یکپارچه‌سازی با آنالیتیکس (آرایه)
    'seokar_settings_search_console', // تنظیمات یکپارچه‌سازی با سرچ کنسول (آرایه)
    'seokar_settings_redirections',   // تنظیمات ماژول ریدایرکت (اگر بخشی در options باشد)
    'seokar_settings_404_monitor',    // تنظیمات مانیتور 404 (اگر بخشی در options باشد)
    'seokar_settings_local_seo',      // تنظیمات سئو محلی (آرایه)
    'seokar_settings_woocommerce',    // تنظیمات سئو ووکامرس (آرایه)
    'seokar_settings_role_manager',   // تنظیمات مدیریت نقش‌ها (آرایه)
    'seokar_settings_advanced',       // تنظیمات پیشرفته (آرایه)
    'seokar_settings_license',        // تنظیمات لایسنس (آرایه، شامل کلید و وضعیت)
    'seokar_initial_version',
    'seokar_current_version',
    'seokar_active_modules',          // لیست ماژول‌های فعال (اگر جدا ذخیره شود)
    'seokar_db_version',              // نسخه دیتابیس (برای مایگریشن جداول)
    // Transient ها نیز در بخش بعدی با الگو حذف می شوند.
);

if ( ! empty( $seokar_options_to_delete ) ) {
    foreach ( $seokar_options_to_delete as $option_name ) {
        delete_option( $option_name );
        // برای چندسایته (اگر گزینه‌ها site-wide باشند):
        // delete_site_option( $option_name );
    }
}

// حذف تمام Transient های سئوکار با پیشوند مشخص
// این کوئری‌ها باید با دقت و با پیشوند منحصربه‌فرد افزونه استفاده شوند.
$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_transient_seokar_%'" );
$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_transient_timeout_seokar_%'" );
// برای چندسایته (حذف Site Transients)
if ( is_multisite() ) {
    $wpdb->query( "DELETE FROM `{$wpdb->sitemeta}` WHERE `meta_key` LIKE '_site_transient_seokar_%'" );
    $wpdb->query( "DELETE FROM `{$wpdb->sitemeta}` WHERE `meta_key` LIKE '_site_transient_timeout_seokar_%'" );
}

// --- ۲. حذف متادیتاهای پست (Post Meta) افزونه ---
// این لیست باید شامل تمام کلیدهای متادیتا باشد که توسط سئوکار برای پست‌ها، صفحات و پست‌تایپ‌های سفارشی ذخیره می‌شود.
$seokar_post_meta_keys_to_delete = array(
    '_seokar_primary_term',
    '_seokar_focus_keyword',
    '_seokar_focus_keywords_additional', // آرایه JSON از کلمات کلیدی اضافی
    '_seokar_analysis_score_readability',
    '_seokar_analysis_score_seo',
    '_seokar_meta_title',
    '_seokar_meta_description',
    '_seokar_canonical_url',
    '_seokar_robots_meta_index',        // 'default', 'index', 'noindex'
    '_seokar_robots_meta_follow',       // 'default', 'follow', 'nofollow'
    '_seokar_robots_meta_advanced',     // آرایه JSON از noimageindex, noarchive, nosnippet
    '_seokar_og_title',
    '_seokar_og_description',
    '_seokar_og_image',
    '_seokar_og_image_id',
    '_seokar_twitter_card_type',        // 'summary', 'summary_large_image'
    '_seokar_twitter_title',
    '_seokar_twitter_description',
    '_seokar_twitter_image',
    '_seokar_twitter_image_id',
    '_seokar_schema_type',              // نوع اسکیمای انتخاب شده برای این پست
    '_seokar_schema_data_custom',       // JSON اسکیمای سفارشی
    '_seokar_breadcrumb_title',         // عنوان سفارشی بردکرامبز
    '_seokar_redirect_active',          // برای ریدایرکت‌های تکی از یک پست
    '_seokar_redirect_url',
    '_seokar_redirect_type',
    // متادیتاهای خاص ووکامرس (مثال)
    '_seokar_woocommerce_gtin',
    '_seokar_woocommerce_isbn',
    '_seokar_woocommerce_mpn',
    '_seokar_woocommerce_show_rich_snippet', // 'default', 'yes', 'no'
    // ... هر کلید متادیتای دیگری که سئوکار استفاده می‌کند.
);

if ( ! empty( $seokar_post_meta_keys_to_delete ) ) {
    $meta_keys_sql_in = "'" . implode( "','", array_map( 'esc_sql', $seokar_post_meta_keys_to_delete ) ) . "'";
    $wpdb->query( "DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` IN ({$meta_keys_sql_in})" );
}

// --- ۳. حذف متادیتاهای ترم (Term Meta) افزونه ---
// اگر سئوکار تنظیمات سئو برای دسته‌بندی‌ها، برچسب‌ها یا سایر Taxonomy ها ذخیره می‌کند.
$seokar_term_meta_keys_to_delete = array(
    'seokar_term_meta_title',
    'seokar_term_meta_description',
    'seokar_term_robots_meta_index',
    'seokar_term_robots_meta_follow',
    'seokar_term_og_title',
    'seokar_term_og_description',
    'seokar_term_og_image',
    'seokar_term_twitter_title',
    'seokar_term_twitter_description',
    'seokar_term_twitter_image',
    'seokar_term_canonical_url',
    // ...
);

if ( ! empty( $seokar_term_meta_keys_to_delete ) ) {
    $term_meta_keys_sql_in = "'" . implode( "','", array_map( 'esc_sql', $seokar_term_meta_keys_to_delete ) ) . "'";
    $wpdb->query( "DELETE FROM `{$wpdb->termmeta}` WHERE `meta_key` IN ({$term_meta_keys_sql_in})" );
}

// --- ۴. حذف متادیتاهای کاربر (User Meta) افزونه ---
// اگر تنظیمات یا اطلاعات خاصی برای هر کاربر ذخیره می‌شود (مثلاً تنظیمات پروفایل سئو).
$seokar_user_meta_keys_to_delete = array(
    'seokar_user_disable_seo_metabox',
    'seokar_user_social_facebook_url',
    'seokar_user_social_twitter_handle',
    'seokar_user_linkedin_url',
    // ...
);

if ( ! empty( $seokar_user_meta_keys_to_delete ) ) {
    $user_meta_keys_sql_in = "'" . implode( "','", array_map( 'esc_sql', $seokar_user_meta_keys_to_delete ) ) . "'";
    $wpdb->query( "DELETE FROM `{$wpdb->usermeta}` WHERE `meta_key` IN ({$user_meta_keys_sql_in})" );
}

// --- ۵. حذف جداول سفارشی دیتابیس (Custom Database Tables) ---
// این لیست باید شامل تمام جداول سفارشی باشد که سئوکار ایجاد می‌کند.
$seokar_custom_tables_suffixes = array(
    'seokar_redirections',          // جدول برای مدیریت ریدایرکت‌ها
    'seokar_redirection_logs',      // جدول برای لاگ ریدایرکت‌ها
    'seokar_404_logs',              // جدول برای لاگ خطاهای 404
    'seokar_seo_analysis_cache',    // جدول برای کش نتایج تحلیل سئو (اگر استفاده شود)
    'seokar_keyword_rankings',      // جدول برای ردیابی رتبه کلمات کلیدی
    'seokar_analytics_stats_cache', // جدول برای کش آمار گوگل آنالیتیکس
    'seokar_internal_links',        // جدول برای تحلیل لینک‌های داخلی
    'seokar_schema_templates',      // جدول برای قالب‌های اسکیما
);

foreach ( $seokar_custom_tables_suffixes as $table_suffix ) {
    $table_name = $wpdb->prefix . $table_suffix;
    $wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );
}

// --- ۶. حذف Cron Job های برنامه‌ریزی شده ---
// لیست تمام هوک‌های Cron که توسط سئوکار ثبت شده‌اند.
$seokar_cron_hooks_to_clear = array(
    'seokar_sitemap_generation_cron',
    'seokar_sitemap_ping_cron',
    'seokar_404_log_cleanup_cron',
    'seokar_rank_tracker_update_cron',
    'seokar_analytics_data_fetch_cron',
    'seokar_broken_link_checker_cron',
    'seokar_database_optimization_cron',
    // ...
);

foreach ( $seokar_cron_hooks_to_clear as $hook_name ) {
    // پاک کردن تمام رویدادهای زمان‌بندی شده برای این هوک، صرف نظر از آرگومان‌ها
    $timestamps = wp_get_scheduled_event( $hook_name, array(), null, true ); // Get all timestamps for the hook
    if ( ! empty( $timestamps ) ) {
        foreach( $timestamps as $timestamp => $event_data ) {
            // $event_data will be an array of events scheduled for that hook at that timestamp
            foreach( $event_data as $args_hash => $event ) {
                 wp_unschedule_event( $timestamp, $hook_name, $event['args'] );
            }
        }
    }
    // یک راه ساده تر برای پاک کردن همه، اما ممکن است همه آرگومان ها را پوشش ندهد اگر تعداد زیادی باشند
    // wp_clear_scheduled_hook( $hook_name );
}

// --- ۷. حذف نقش‌ها و قابلیت‌های سفارشی (Custom Roles & Capabilities) ---
// اگر سئوکار نقش یا قابلیت جدیدی اضافه کرده باشد.
$seokar_custom_capabilities = array(
    'manage_seokar_settings',       // دسترسی به تنظیمات اصلی سئوکار
    'view_seokar_reports',          // مشاهده گزارش‌های سئوکار
    'manage_seokar_redirections',   // مدیریت ریدایرکت‌ها
    'manage_seokar_sitemap',        // مدیریت نقشه سایت
    // ...
);

global $wp_roles;
if ( is_object( $wp_roles ) ) {
    // حذف قابلیت‌ها از تمام نقش‌هایی که ممکن است این قابلیت‌ها را داشته باشند
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

// حذف نقش‌های سفارشی (اگر سئوکار نقشی مثل "SeoKar SEO Manager" ایجاد کرده باشد)
// if ( get_role( 'seokar_seo_editor' ) ) {
//     remove_role( 'seokar_seo_editor' );
// }
// if ( get_role( 'seokar_seo_manager' ) ) {
//     remove_role( 'seokar_seo_manager' );
// }


// --- ۸. پاک کردن کش‌های مرتبط (اختیاری اما مفید) ---
// پاک کردن کش اشیاء وردپرس
if ( function_exists( 'wp_cache_flush' ) ) {
    wp_cache_flush();
}
// اگر با افزونه‌های کش رایج تعامل دارید، می‌توانید API آن‌ها را برای پاک کردن کش فراخوانی کنید.
// مثال برای WP Rocket (باید فعال بودن افزونه چک شود):
// if ( function_exists( 'rocket_clean_domain' ) ) {
//     rocket_clean_domain();
// }
// مثال برای LiteSpeed Cache (باید فعال بودن افزونه چک شود):
// if ( class_exists( 'LiteSpeed_Cache_API' ) && method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) {
//     LiteSpeed_Cache_API::purge_all();
// }

// اجرای یک هوک نهایی قبل از پایان uninstall (برای توسعه‌پذیری یا لاگ‌گیری)
if ( has_action('seokar_uninstall_complete') ) { // فقط اگر هوکی ثبت شده باشد
    do_action( 'seokar_uninstall_complete' );
}

?>
