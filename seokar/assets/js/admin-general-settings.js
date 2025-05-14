/**
 * SeoKar Admin General Settings Scripts
 *
 * @package SeoKar\Assets\JS
 * @since 0.1.0
 */

/* global jQuery, wp, seokarGeneralSettingsParams */

(function($) {
    'use strict';

    $(function() {
        initSiteKnowledgeTypeToggle();
        initMediaUploader();
    });

    function initSiteKnowledgeTypeToggle() {
        var $knowledgeTypeSelect = $('#seokar_site_knowledge_type');
        if (!$knowledgeTypeSelect.length) return;

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
                $organizationFields.hide();
                $personFields.hide();
            }
        }
        toggleKnowledgeFields();
        $knowledgeTypeSelect.on('change', toggleKnowledgeFields);
    }

    function initMediaUploader() {
        var mediaUploader;
        var $uploadButton = $('.seokar-upload-image-button');

        if (!$uploadButton.length) return;

        $('body').on('click', '.seokar-upload-image-button', function(e) { // Delegate event for dynamically added elements if any
            e.preventDefault();
            var $button = $(this);
            var $fieldContainer = $button.closest('.seokar-image-uploader');
            var $idField = $fieldContainer.find('.seokar-image-id-field');
            var $preview = $fieldContainer.find('.seokar-image-preview');
            var $currentRemoveButton = $fieldContainer.find('.seokar-remove-image-button');

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title:    (typeof seokarGeneralSettingsParams !== 'undefined' && seokarGeneralSettingsParams.media_uploader_title) ? seokarGeneralSettingsParams.media_uploader_title : 'انتخاب یا آپلود لوگو',
                button:   {
                    text: (typeof seokarGeneralSettingsParams !== 'undefined' && seokarGeneralSettingsParams.media_uploader_button_text) ? seokarGeneralSettingsParams.media_uploader_button_text : 'استفاده از این لوگو'
                },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $idField.val(attachment.id);
                var previewUrl = attachment.url;
                if (attachment.sizes) {
                    if (attachment.sizes.medium) previewUrl = attachment.sizes.medium.url;
                    else if (attachment.sizes.thumbnail) previewUrl = attachment.sizes.thumbnail.url;
                }
                $preview.attr('src', previewUrl).show().removeClass('hidden');
                $currentRemoveButton.show().removeClass('hidden');
            });
            mediaUploader.open();
        });

        $('body').on('click', '.seokar-remove-image-button', function(e) { // Delegate event
            e.preventDefault();
            var $button = $(this);
            var $fieldContainer = $button.closest('.seokar-image-uploader');
            var $idField = $fieldContainer.find('.seokar-image-id-field');
            var $preview = $fieldContainer.find('.seokar-image-preview');
            $idField.val('');
            $preview.attr('src', '').hide().addClass('hidden');
            $button.hide().addClass('hidden');
        });
    }
})(jQuery);
