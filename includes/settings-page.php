<?php
defined('ABSPATH') || exit;

if (class_exists('WC_Settings_Page', false)) :

  return new class extends WC_Settings_Page
  {
    public function __construct()
    {
      $this->id = 'wc_phone_order';
      $this->label = __('Phone Order', 'woocommerce-phone-order');

      parent::__construct();
    }

    public function get_settings()
    {
      $settings = array(
        array(
          'title' => __('Phone Order Settings', 'woocommerce-phone-order'),
          'type' => 'title',
          'desc' => __('Configure the phone order form appearance and behavior', 'woocommerce-phone-order'),
          'id' => 'wc_phone_order_settings',
        ),

        array(
          'title' => __('Form Title', 'woocommerce-phone-order'),
          'desc' => __('The main heading displayed on the form', 'woocommerce-phone-order'),
          'id' => 'wc_phone_order_form_title',
          'type' => 'text',
          'default' => __('Quick Purchase', 'woocommerce-phone-order'),
          'css' => 'min-width:300px;',
        ),

        array(
          'title' => __('Form Subtitle', 'woocommerce-phone-order'),
          'desc' => __('A subtitle or tagline for the form', 'woocommerce-phone-order'),
          'id' => 'wc_phone_order_form_subtitle',
          'type' => 'text',
          'default' => __('Order with just your phone number', 'woocommerce-phone-order'),
          'css' => 'min-width:300px;',
        ),

        array(
          'title' => __('Form Description', 'woocommerce-phone-order'),
          'desc' => __('Additional text to explain how the phone order works', 'woocommerce-phone-order'),
          'id' => 'wc_phone_order_form_description',
          'type' => 'textarea',
          'default' => __('Enter your phone number and we\'ll contact you to complete your order', 'woocommerce-phone-order'),
          'css' => 'min-width:300px; min-height: 75px;',
        ),

        array(
          'title' => __('Button Text', 'woocommerce-phone-order'),
          'desc' => __('The text displayed on the submit button', 'woocommerce-phone-order'),
          'id' => 'wc_phone_order_form_button_text',
          'type' => 'text',
          'default' => __('Order Now', 'woocommerce-phone-order'),
          'css' => 'min-width:300px;',
        ),

        array(
          'title' => __('Display on Product Pages', 'woocommerce-phone-order'),
          'desc' => __('Choose where to display the phone order form on product pages', 'woocommerce-phone-order'),
          'id' => 'wc_phone_order_display_position',
          'type' => 'select',
          'default' => 'after_summary',
          'options' => array(
            'disabled' => __('Disabled (use shortcode only)', 'woocommerce-phone-order'),
            'after_summary' => __('After product summary', 'woocommerce-phone-order'),
            'after_add_to_cart' => __('After add to cart button', 'woocommerce-phone-order'),
          ),
        ),

        array(
          'title' => __('Out of Stock Behavior', 'woocommerce-phone-order'),
          'desc' => __('How to handle the form when a product is out of stock', 'woocommerce-phone-order'),
          'id' => 'wc_phone_order_out_of_stock_behavior',
          'type' => 'select',
          'default' => 'hide',
          'options' => array(
            'hide' => __('Hide form completely', 'woocommerce-phone-order'),
            'show' => __('Show form (orders will be rejected)', 'woocommerce-phone-order'),
            'disabled' => __('Show form but disable it', 'woocommerce-phone-order'),
          ),
        ),

        array(
          'type' => 'sectionend',
          'id' => 'wc_phone_order_settings',
        ),

        array(
          'title' => __('Shortcode Usage', 'woocommerce-phone-order'),
          'type' => 'title',
          'desc' => __('Use the shortcode <code>[woo_phone_order]</code> to display the form anywhere. You can specify a product ID: <code>[woo_phone_order product_id="123"]</code>', 'woocommerce-phone-order'),
          'id' => 'wc_phone_order_shortcode_info',
        ),

        array(
          'type' => 'sectionend',
          'id' => 'wc_phone_order_shortcode_info',
        ),
      );

      return apply_filters('wc_phone_order_settings', $settings);
    }
  };

endif;
