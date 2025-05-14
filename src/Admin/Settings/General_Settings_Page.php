<?php
/**
 * SeoKar General Settings Page
 *
 * این کلاس مسئولیت مدیریت و نمایش صفحه تنظیمات عمومی افزونه سئوکار
 * با استفاده از WordPress Settings API را بر عهده دارد.
 *
 * @package SeoKar\Admin\Settings
 * @since 0.1.0
 */

namespace SeoKar\Admin\Settings;

// جلوگیری از دسترسی مستقیم به فایل
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'SeoKar\\Admin\\Settings\\General_Settings_Page' ) ) {

    /**
     * کلاس General_Settings_Page
     *
     * مدیریت تنظیمات عمومی سئوکار.
     */
    class General_Settings_Page {

        /**
         * تنها نمونه از کلاس General_Settings_Page (Singleton Pattern).
         *
         * @var General_Settings_Page|null
         * @since 0.1.0
         * @access private
         * @static
         */
        private static $instance = null;

        /**
         * نام گروه تنظیمات (Option Group) برای Settings API.
         * این نام در settings_fields() استفاده می‌شود.
         *
         * @var string
         * @since 0.1.0
         * @access private
         */
        private $option_group = 'seokar_settings_general_group';

        /**
         * نام گزینه (Option Name) که تنظیمات عمومی در آن ذخیره می‌شوند (به صورت آرایه).
         *
         * @var string
         * @since 0.1.0
         * @access private
         */
        private $option_name = 'seokar_settings_general'; // همان نامی که در uninstall.php و activate() استفاده کردیم

        /**
         * نامک (Slug) صفحه تنظیمات در URL.
         * این نامک در add_settings_section و do_settings_sections استفاده می‌شود.
         *
         * @var string
         * @since 0.1.0
         * @access private
         */
        private $page_slug = 'seokar_page_general_settings'; // همان نامکی که در Menu_Manager برای do_settings_sections استفاده می‌شود

        /**
         * آرایه‌ای از تنظیمات عمومی با مقادیر پیش‌فرض.
         *
         * @var array
         * @since 0.1.0
         * @access private
         */
        private $default_settings;

        /**
         * Constructor خصوصی برای پیاده‌سازی الگوی Singleton.
         *
         * @since 0.1.0
         * @access private
         */
        private function __construct() {
            $this->set_default_settings();

            // ثبت تنظیمات، بخش‌ها و فیلدها در هوک admin_init
            add_action( 'admin_init', array( $this, 'register_settings_and_fields' ) );
        }

        /**
         * برگرداندن تنها نمونه از کلاس (Singleton Pattern).
         *
         * @static
         * @return General_Settings_Page - نمونه General_Settings_Page.
         * @since 0.1.0
         * @access public
         */
        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * تنظیم مقادیر پیش‌فرض برای تنظیمات عمومی.
         *
         * @since 0.1.0
         * @access private
         */
        private function set_default_settings() {
            $this->default_settings = apply_filters( 'seokar_general_settings_defaults', array(
                'site_knowledge_type'   => 'organization', // 'person' or 'organization'
                'site_organization_name'=> get_bloginfo( 'name' ),
                'site_organization_logo'=> '', // URL or Attachment ID
                'site_person_name'      => '',
                // فیلدهای بیشتر در آینده اضافه خواهند شد
                'separator_char'        => '-', // جداکننده عنوان
                'disable_author_archives' => false,
                'disable_date_archives' => false,
                // ...
            ) );
        }

        /**
         * دریافت مقدار یک گزینه تنظیماتی با در نظر گرفتن مقدار پیش‌فرض.
         *
         * @since 0.1.0
         * @access public
         * @param string $key کلید گزینه مورد نظر.
         * @return mixed مقدار گزینه یا مقدار پیش‌فرض آن.
         */
        public function get_option( $key ) {
            $options = get_option( $this->option_name, $this->default_settings );
            // اطمینان از اینکه همه کلیدهای پیش‌فرض در options وجود دارند
            $options = wp_parse_args( $options, $this->default_settings );
            return isset( $options[ $key ] ) ? $options[ $key ] : null;
        }


        /**
         * ثبت تنظیمات، بخش‌ها و فیلدها با استفاده از WordPress Settings API.
         * این متد توسط هوک 'admin_init' فراخوانی می‌شود.
         *
         * @since 0.1.0
         * @access public
         */
        public function register_settings_and_fields() {
            /**
             * ۱. ثبت گروه تنظیمات (register_setting)
             *    - $option_group: نام گروه (برای settings_fields())
             *    - $option_name: نام option در دیتابیس که تنظیمات در آن ذخیره می‌شوند
             *    - $args (اختیاری): شامل تابع callback برای اعتبارسنجی (sanitize_callback)
             */
            register_setting(
                $this->option_group,
                $this->option_name,
                array( $this, 'sanitize_general_settings' ) // تابع callback برای پاکسازی داده‌ها
            );

            /**
             * ۲. اضافه کردن بخش‌های تنظیمات (add_settings_section)
             *    - $id: شناسه منحصر به فرد بخش
             *    - $title: عنوان بخش (قابل ترجمه)
             *    - $callback: تابع callback برای نمایش توضیحات یا محتوای بالای بخش (اختیاری)
             *    - $page: نامک صفحه‌ای که این بخش در آن نمایش داده می‌شود (باید با do_settings_sections هماهنگ باشد)
             */

            // بخش اول: دانش‌نامه سایت (Site Knowledge)
            add_settings_section(
                'seokar_section_site_knowledge',                               // شناسه بخش
                esc_html__( 'دانش‌نامه سایت و اطلاعات پایه', 'seokar' ),     // عنوان بخش
                array( $this, 'render_section_site_knowledge_description' ), // تابع توضیحات بخش
                $this->page_slug                                               // نامک صفحه
            );

            // بخش دوم: تنظیمات عنوان و جداکننده (مثال)
            add_settings_section(
                'seokar_section_title_separator',
                esc_html__( 'جداکننده عنوان', 'seokar' ),
                null, // بدون توضیحات برای این بخش
                $this->page_slug
            );


            /**
             * ۳. اضافه کردن فیلدهای تنظیمات (add_settings_field)
             *    - $id: شناسه منحصر به فرد فیلد
             *    - $title: عنوان فیلد (قابل ترجمه)
             *    - $callback: تابع callback برای نمایش خود فیلد (input, select, textarea و ...)
             *    - $page: نامک صفحه‌ای که این فیلد در آن نمایش داده می‌شود
             *    - $section: شناسه بخشی که این فیلد به آن تعلق دارد
             *    - $args (اختیاری): آرایه‌ای از آرگومان‌ها که به تابع callback پاس داده می‌شود (مثلاً برای label_for)
             */

            // فیلدهای بخش "دانش‌نامه سایت"
            add_settings_field(
                'site_knowledge_type',
                esc_html__( 'نوع سایت (شخص یا سازمان)', 'seokar' ),
                array( $this, 'render_field_site_knowledge_type' ),
                $this->page_slug,
                'seokar_section_site_knowledge',
                array( 'label_for' => 'seokar_site_knowledge_type' ) // برای اتصال label به فیلد
            );

            add_settings_field(
                'site_organization_name',
                esc_html__( 'نام سازمان', 'seokar' ),
                array( $this, 'render_field_site_organization_name' ),
                $this->page_slug,
                'seokar_section_site_knowledge',
                array(
                    'label_for' => 'seokar_site_organization_name',
                    'class'     => 'seokar-field-organization', // برای نمایش/عدم نمایش با JS
                )
            );

            add_settings_field(
                'site_organization_logo',
                esc_html__( 'لوگوی سازمان', 'seokar' ),
                array( $this, 'render_field_site_organization_logo' ),
                $this->page_slug,
                'seokar_section_site_knowledge',
                array(
                    'label_for' => 'seokar_site_organization_logo',
                    'class'     => 'seokar-field-organization',
                )
            );

            add_settings_field(
                'site_person_name',
                esc_html__( 'نام شخص', 'seokar' ),
                array( $this, 'render_field_site_person_name' ),
                $this->page_slug,
                'seokar_section_site_knowledge',
                array(
                    'label_for' => 'seokar_site_person_name',
                    'class'     => 'seokar-field-person', // برای نمایش/عدم نمایش با JS
                )
            );

            // فیلدهای بخش "جداکننده عنوان"
            add_settings_field(
                'separator_char',
                esc_html__( 'کاراکتر جداکننده', 'seokar' ),
                array( $this, 'render_field_separator_char' ),
                $this->page_slug,
                'seokar_section_title_separator',
                array( 'label_for' => 'seokar_separator_char' )
            );

            // هوک برای اضافه کردن بخش‌ها و فیلدهای بیشتر توسط سایر ماژول‌ها
            do_action( 'seokar_register_general_settings_fields', $this->page_slug, $this->option_group, $this->option_name );
        }

        /**
         * نمایش توضیحات برای بخش "دانش‌نامه سایت".
         *
         * @since 0.1.0
         * @access public
         */
        public function render_section_site_knowledge_description() {
            echo '<p>' . esc_html__( 'این اطلاعات به موتورهای جستجو کمک می‌کند تا وب‌سایت شما را بهتر بشناسند و در نتایج جستجو (مانند گراف دانش گوگل) به درستی نمایش دهند.', 'seokar' ) . '</p>';
        }


        // --- توابع Callback برای نمایش فیلدها ---

        /**
         * نمایش فیلد "نوع سایت (شخص یا سازمان)".
         *
         * @since 0.1.0
         * @access public
         * @param array $args آرگومان‌های پاس داده شده از add_settings_field.
         */
        public function render_field_site_knowledge_type( $args ) {
            $field_id = 'seokar_site_knowledge_type';
            $value = $this->get_option( 'site_knowledge_type' );
            ?>
            <select id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $this->option_name . '[site_knowledge_type]' ); ?>">
                <option value="organization" <?php selected( $value, 'organization' ); ?>><?php esc_html_e( 'سازمان', 'seokar' ); ?></option>
                <option value="person" <?php selected( $value, 'person' ); ?>><?php esc_html_e( 'شخص', 'seokar' ); ?></option>
            </select>
            <p class="description">
                <?php esc_html_e( 'مشخص کنید که این وب‌سایت نماینده یک سازمان/شرکت است یا یک شخص.', 'seokar' ); ?>
            </p>
            <?php
            // JS ساده برای نمایش/مخفی کردن فیلدهای وابسته (در آینده با enqueue کردن یک فایل JS)
            // این کد می‌تواند به یک فایل JS منتقل شود و در اینجا فقط یک تریگر باشد.
            // برای سادگی فعلی، مستقیماً اینجا قرار داده شده است.
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    function toggleKnowledgeFields() {
                        var selectedType = $('#<?php echo esc_js( $field_id ); ?>').val();
                        if (selectedType === 'organization') {
                            $('.seokar-field-organization').closest('tr').show();
                            $('.seokar-field-person').closest('tr').hide();
                        } else if (selectedType === 'person') {
                            $('.seokar-field-organization').closest('tr').hide();
                            $('.seokar-field-person').closest('tr').show();
                        }
                    }
                    toggleKnowledgeFields(); // اجرا در بارگذاری اولیه
                    $('#<?php echo esc_js( $field_id ); ?>').on('change', toggleKnowledgeFields);
                });
            </script>
            <?php
        }

        /**
         * نمایش فیلد "نام سازمان".
         *
         * @since 0.1.0
         * @access public
         * @param array $args
         */
        public function render_field_site_organization_name( $args ) {
            $value = $this->get_option( 'site_organization_name' );
            ?>
            <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
                   name="<?php echo esc_attr( $this->option_name . '[site_organization_name]' ); ?>"
                   value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
            <p class="description"><?php esc_html_e( 'نام کامل سازمان یا شرکت شما.', 'seokar' ); ?></p>
            <?php
        }

        /**
         * نمایش فیلد "لوگوی سازمان" (با استفاده از Media Uploader وردپرس).
         *
         * @since 0.1.0
         * @access public
         * @param array $args
         */
        public function render_field_site_organization_logo( $args ) {
            $value = $this->get_option( 'site_organization_logo' ); // می‌تواند ID یا URL باشد
            $image_url = '';
            $image_id = 0;

            if ( is_numeric( $value ) && $value > 0 ) {
                $image_id = (int) $value;
                $image_src = wp_get_attachment_image_src( $image_id, 'medium' );
                if ( $image_src ) {
                    $image_url = $image_src[0];
                }
            } elseif ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
                $image_url = $value;
            }

            // برای فعال کردن Media Uploader
            wp_enqueue_media();
            ?>
            <div class="seokar-image-uploader">
                <input type="hidden" id="<?php echo esc_attr( $args['label_for'] ); ?>"
                       name="<?php echo esc_attr( $this->option_name . '[site_organization_logo]' ); ?>"
                       value="<?php echo esc_attr( $value ); ?>" class="seokar-image-id-field" />
                <img src="<?php echo esc_url( $image_url ); ?>" class="seokar-image-preview <?php echo empty($image_url) ? 'hidden' : ''; ?>" style="max-width: 150px; height: auto; border: 1px solid #ddd; margin-bottom: 10px; display: <?php echo empty($image_url) ? 'none' : 'block'; ?>;" />
                <button type="button" class="button seokar-upload-image-button"><?php esc_html_e( 'انتخاب/آپلود لوگو', 'seokar' ); ?></button>
                <button type="button" class="button seokar-remove-image-button <?php echo empty($image_url) ? 'hidden' : ''; ?>" style="display: <?php echo empty($image_url) ? 'none' : 'inline-block'; ?>;"><?php esc_html_e( 'حذف لوگو', 'seokar' ); ?></button>
            </div>
            <p class="description">
                <?php esc_html_e( 'لوگوی رسمی سازمان شما. حداقل اندازه پیشنهادی: ۱۱۲×۱۱۲ پیکسل. فرمت‌های PNG, JPG, یا GIF.', 'seokar' ); ?>
            </p>
            <?php
            // JS برای Media Uploader (در آینده به فایل JS منتقل شود)
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    var mediaUploader;
                    $('.seokar-upload-image-button').click(function(e) {
                        e.preventDefault();
                        var $button = $(this);
                        var $fieldContainer = $button.closest('.seokar-image-uploader');
                        var $idField = $fieldContainer.find('.seokar-image-id-field');
                        var $preview = $fieldContainer.find('.seokar-image-preview');
                        var $removeButton = $fieldContainer.find('.seokar-remove-image-button');

                        if (mediaUploader) {
                            mediaUploader.open();
                            return;
                        }
                        mediaUploader = wp.media.frames.file_frame = wp.media({
                            title: '<?php esc_html_e( "انتخاب لوگو", "seokar" ); ?>',
                            button: {
                                text: '<?php esc_html_e( "استفاده از این لوگو", "seokar" ); ?>'
                            },
                            multiple: false
                        });
                        mediaUploader.on('select', function() {
                            var attachment = mediaUploader.state().get('selection').first().toJSON();
                            $idField.val(attachment.id); // ذخیره ID تصویر
                            $preview.attr('src', attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url).show().removeClass('hidden');
                            $removeButton.show().removeClass('hidden');
                        });
                        mediaUploader.open();
                    });
                    $('.seokar-remove-image-button').click(function(e) {
                        e.preventDefault();
                        var $button = $(this);
                        var $fieldContainer = $button.closest('.seokar-image-uploader');
                        var $idField = $fieldContainer.find('.seokar-image-id-field');
                        var $preview = $fieldContainer.find('.seokar-image-preview');
                        $idField.val('');
                        $preview.attr('src', '').hide().addClass('hidden');
                        $button.hide().addClass('hidden');
                    });
                });
            </script>
            <?php
        }

        /**
         * نمایش فیلد "نام شخص".
         *
         * @since 0.1.0
         * @access public
         * @param array $args
         */
        public function render_field_site_person_name( $args ) {
            $value = $this->get_option( 'site_person_name' );
            ?>
            <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
                   name="<?php echo esc_attr( $this->option_name . '[site_person_name]' ); ?>"
                   value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
            <p class="description"><?php esc_html_e( 'نام کامل شخصی که این وب‌سایت نماینده اوست.', 'seokar' ); ?></p>
            <?php
        }

        /**
         * نمایش فیلد "کاراکتر جداکننده".
         *
         * @since 0.1.0
         * @access public
         * @param array $args
         */
        public function render_field_separator_char( $args ) {
            $value = $this->get_option( 'separator_char' );
            $separators = apply_filters('seokar_title_separators', array(
                '-'   => '-',
                '–'   => '– (en dash)',
                '—'   => '— (em dash)',
                '•'   => '• (bullet)',
                '*'   => '*',
                '|'   => '|',
                '~'   => '~',
                '«'   => '«',
                '»'   => '»',
                '‹'   => '‹',
                '›'   => '›',
            ));
            ?>
            <select id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $this->option_name . '[separator_char]' ); ?>">
                <?php foreach ( $separators as $char => $label ) : ?>
                    <option value="<?php echo esc_attr( $char ); ?>" <?php selected( $value, $char ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">
                <?php esc_html_e( 'کاراکتری که برای جدا کردن بخش‌های مختلف عنوان سئو استفاده می‌شود (مثلاً: عنوان پست - عنوان سایت).', 'seokar' ); ?>
                <?php // در اینجا می‌توانیم یک پیش‌نمایش زنده از عنوان با جداکننده انتخابی نمایش دهیم. ?>
            </p>
            <?php
        }


        /**
         * تابع Callback برای پاکسازی و اعتبارسنجی داده‌های ورودی تنظیمات عمومی.
         * این تابع توسط register_setting فراخوانی می‌شود.
         *
         * @since 0.1.0
         * @access public
         * @param array $input آرایه‌ای از داده‌های ورودی از فرم.
         * @return array آرایه‌ای از داده‌های پاکسازی شده.
         */
        public function sanitize_general_settings( $input ) {
            $sanitized_input = array();
            $current_options = get_option( $this->option_name, $this->default_settings );
            $current_options = wp_parse_args( $current_options, $this->default_settings ); // اطمینان از وجود همه کلیدها

            if ( isset( $input['site_knowledge_type'] ) ) {
                $sanitized_input['site_knowledge_type'] = sanitize_key( $input['site_knowledge_type'] );
                if ( ! in_array( $sanitized_input['site_knowledge_type'], array( 'person', 'organization' ) ) ) {
                    $sanitized_input['site_knowledge_type'] = $current_options['site_knowledge_type']; // بازگشت به مقدار قبلی یا پیش‌فرض
                    add_settings_error(
                        'seokar_settings_general_group', // $setting slug
                        'invalid_knowledge_type',        // $code
                        esc_html__( 'نوع سایت انتخاب شده نامعتبر است.', 'seokar' ), // $message
                        'error'                          // $type
                    );
                }
            } else {
                $sanitized_input['site_knowledge_type'] = $current_options['site_knowledge_type'];
            }

            if ( isset( $input['site_organization_name'] ) ) {
                $sanitized_input['site_organization_name'] = sanitize_text_field( $input['site_organization_name'] );
            } else {
                $sanitized_input['site_organization_name'] = $current_options['site_organization_name'];
            }

            if ( isset( $input['site_organization_logo'] ) ) {
                // اگر مقدار یک عدد صحیح (ID تصویر) بود یا یک URL معتبر، آن را ذخیره کن
                if ( is_numeric( $input['site_organization_logo'] ) && intval( $input['site_organization_logo'] ) > 0 ) {
                    $sanitized_input['site_organization_logo'] = intval( $input['site_organization_logo'] );
                } elseif ( filter_var( $input['site_organization_logo'], FILTER_VALIDATE_URL ) ) {
                    $sanitized_input['site_organization_logo'] = esc_url_raw( $input['site_organization_logo'] );
                } else {
                    $sanitized_input['site_organization_logo'] = ''; // مقدار نامعتبر، خالی ذخیره شود
                }
            } else {
                $sanitized_input['site_organization_logo'] = $current_options['site_organization_logo'];
            }

            if ( isset( $input['site_person_name'] ) ) {
                $sanitized_input['site_person_name'] = sanitize_text_field( $input['site_person_name'] );
            } else {
                $sanitized_input['site_person_name'] = $current_options['site_person_name'];
            }

            if ( isset( $input['separator_char'] ) ) {
                // فقط کاراکترهای خاصی مجاز هستند (برای امنیت بیشتر)
                // لیست جداکننده‌های مجاز را می‌توان از $this->render_field_separator_char گرفت
                 $allowed_separators = array_keys(apply_filters('seokar_title_separators', array(
                    '-' => '-', '–' => '–', '—' => '—', '•' => '•', '*' => '*', '|' => '|', '~' => '~', '«' => '«', '»' => '»', '‹' => '‹', '›' => '›',
                )));
                if ( in_array( $input['separator_char'], $allowed_separators, true ) ) {
                    $sanitized_input['separator_char'] = $input['separator_char'];
                } else {
                    $sanitized_input['separator_char'] = $current_options['separator_char'];
                    add_settings_error(
                        'seokar_settings_general_group',
                        'invalid_separator',
                        esc_html__( 'کاراکتر جداکننده انتخاب شده نامعتبر است.', 'seokar' ),
                        'error'
                    );
                }
            } else {
                $sanitized_input['separator_char'] = $current_options['separator_char'];
            }

            // ادغام با سایر تنظیماتی که ممکن است در این گروه باشند اما در این فرم نیستند
            $sanitized_input = wp_parse_args( $sanitized_input, $current_options );

            // پاکسازی تمام مقادیر نهایی (به عنوان یک اقدام احتیاطی عمومی)
            // این بخش می‌تواند بسته به نوع داده‌ها دقیق‌تر شود.
            // برای مثال، برای HTML از wp_kses_post، برای URL از esc_url_raw و ...
            // در اینجا یک پاکسازی عمومی‌تر انجام می‌دهیم.
            // $sanitized_input = array_map( 'sanitize_text_field', $sanitized_input ); // این برای همه انواع داده مناسب نیست

            // اجازه به سایر ماژول‌ها برای پاکسازی فیلدهای خودشان
            $sanitized_input = apply_filters( 'seokar_sanitize_general_settings_input', $sanitized_input, $input, $current_options );

            // نمایش پیام موفقیت آمیز بودن ذخیره (وردپرس به صورت خودکار این کار را انجام می‌دهد اگر خطایی نباشد)
            // add_settings_error('seokar_settings_general_group', 'settings_saved', __('Settings saved successfully.', 'seokar'), 'updated');

            return $sanitized_input;
        }

    } // پایان کلاس General_Settings_Page

} // پایان if ( ! class_exists( 'SeoKar\\Admin\\Settings\\General_Settings_Page' ) )
