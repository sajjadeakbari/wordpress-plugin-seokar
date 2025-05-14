/**
 * SeoKar Admin General Settings Scripts
 *
 * این فایل شامل کدهای JavaScript برای صفحه تنظیمات عمومی افزونه سئوکار است.
 * - مدیریت نمایش شرطی فیلدهای دانش‌نامه (شخص/سازمان)
 * - مدیریت WordPress Media Uploader برای انتخاب لوگو
 *
 * @package SeoKar\Assets\JS
 * @since 0.1.0
 */

/* global jQuery, wp, seokarGeneralSettingsParams */ // seokarGeneralSettingsParams برای آینده (اگر با wp_localize_script داده‌ای ارسال شود)

(function($) {
    'use strict';

    /**
     * Document Ready Handler
     */
    $(function() {

        // راه‌اندازی اولیه تمام کنترل‌های تنظیمات
        initSiteKnowledgeTypeToggle();
        initMediaUploader();

    }); // End Document Ready

    /**
     * مقداردهی اولیه برای نمایش/مخفی کردن شرطی فیلدهای دانش‌نامه سایت.
     * بر اساس انتخاب کاربر بین "شخص" یا "سازمان".
     */
    function initSiteKnowledgeTypeToggle() {
        var $knowledgeTypeSelect = $('#seokar_site_knowledge_type'); // ID فیلد select

        if (!$knowledgeTypeSelect.length) {
            return; // اگر فیلد وجود نداشت، ادامه نده
        }

        // تابع برای نمایش/مخفی کردن فیلدهای وابسته
        function toggleKnowledgeFields() {
            var selectedType = $knowledgeTypeSelect.val();
            var $organizationFields = $('.seokar-field-organization').closest('tr');
            var $personFields = $('.seokar-field-person').closest('tr');

            if (selectedType === 'organization') {
                $organizationFields.show();
                $personFields.hide();
            } else if (selectedType === 'person') {
                $organizationFields.hide();
                $personFields.show();
            } else {
                // در صورت انتخاب مقدار نامشخص یا پیش‌فرض (اگر وجود داشته باشد)
                $organizationFields.hide();
                $personFields.hide();
            }
        }

        // اجرای اولیه هنگام بارگذاری صفحه
        toggleKnowledgeFields();

        // اتصال رویداد onchange به فیلد select
        $knowledgeTypeSelect.on('change', function() {
            toggleKnowledgeFields();
        });
    } // End initSiteKnowledgeTypeToggle

    /**
     * مقداردهی اولیه WordPress Media Uploader برای فیلد لوگوی سازمان.
     */
    function initMediaUploader() {
        var mediaUploader;
        var $uploadButton = $('.seokar-upload-image-button');
        var $removeButton = $('.seokar-remove-image-button');

        if (!$uploadButton.length) {
            return; // اگر دکمه آپلود وجود نداشت، ادامه نده
        }

        $uploadButton.on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var $fieldContainer = $button.closest('.seokar-image-uploader');
            var $idField = $fieldContainer.find('.seokar-image-id-field');
            var $preview = $fieldContainer.find('.seokar-image-preview');
            var $currentRemoveButton = $fieldContainer.find('.seokar-remove-image-button'); // دکمه حذف مربوط به همین آپلودر

            // اگر مدیا آپلودر قبلاً ایجاد شده، فقط بازش کن
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            // ایجاد یک نمونه جدید از مدیا آپلودر وردپرس
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title:    seokarGeneralSettingsParams && seokarGeneralSettingsParams.media_uploader_title ? seokarGeneralSettingsParams.media_uploader_title : 'انتخاب یا آپلود لوگو', // استفاده از رشته ترجمه شده از PHP (در آینده)
                button:   {
                    text: seokarGeneralSettingsParams && seokarGeneralSettingsParams.media_uploader_button_text ? seokarGeneralSettingsParams.media_uploader_button_text : 'استفاده از این لوگو'
                },
                multiple: false // فقط امکان انتخاب یک تصویر
            });

            // هنگامی که یک تصویر انتخاب می‌شود
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();

                $idField.val(attachment.id); // ذخیره ID تصویر در فیلد hidden

                // نمایش پیش‌نمایش تصویر
                // سعی در استفاده از اندازه 'medium' یا 'thumbnail' در صورت وجود، در غیر این صورت اندازه کامل
                var previewUrl = attachment.url;
                if (attachment.sizes) {
                    if (attachment.sizes.medium) {
                        previewUrl = attachment.sizes.medium.url;
                    } else if (attachment.sizes.thumbnail) {
                        previewUrl = attachment.sizes.thumbnail.url;
                    }
                }
                $preview.attr('src', previewUrl).show().removeClass('hidden');
                $currentRemoveButton.show().removeClass('hidden');
            });

            // باز کردن مدیا آپلودر
            mediaUploader.open();
        });

        // رویداد کلیک برای دکمه "حذف لوگو"
        $removeButton.on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var $fieldContainer = $button.closest('.seokar-image-uploader');
            var $idField = $fieldContainer.find('.seokar-image-id-field');
            var $preview = $fieldContainer.find('.seokar-image-preview');

            $idField.val(''); // خالی کردن مقدار فیلد hidden
            $preview.attr('src', '').hide().addClass('hidden'); // مخفی کردن پیش‌نمایش
            $button.hide().addClass('hidden'); // مخفی کردن دکمه حذف
        });

    } // End initMediaUploader

})(jQuery);
