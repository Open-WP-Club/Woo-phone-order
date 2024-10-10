<?php
defined('ABSPATH') || exit;

class Woo_Phone_Order_Settings
{
  public static function init()
  {
    add_filter('woocommerce_get_settings_products', array(__CLASS__, 'add_settings'), 10, 2);
    add_action('woocommerce_update_options_products', array(__CLASS__, 'update_settings'));
  }

  public static function add_settings($settings, $current_section)
  {
    if ('woo_phone_order' !== $current_section) {
      return $settings;
    }

    $woo_phone_order_settings = array(
      array(
        'title' => __('Woo Phone Order Settings', 'woo-phone-order'),
        'type'  => 'title',
        'id'    => 'woo_phone_order_settings',
      ),
      array(
        'title'    => __('Form Title', 'woo-phone-order'),
        'id'       => 'woo_phone_order_title',
        'type'     => 'text',
        'default'  => __('Quick checkout', 'woo-phone-order'),
      ),
      array(
        'title'    => __('Form Subtitle', 'woo-phone-order'),
        'id'       => 'woo_phone_order_subtitle',
        'type'     => 'text',
        'default'  => __('No registration required', 'woo-phone-order'),
      ),
      array(
        'title'    => __('Form Description', 'woo-phone-order'),
        'id'       => 'woo_phone_order_description',
        'type'     => 'textarea',
        'default'  => __('Just enter your phone number to place an order. We\'ll contact you to confirm the details.', 'woo-phone-order'),
      ),
      array(
        'title'    => __('Out of Stock Behavior', 'woo-phone-order'),
        'id'       => 'woo_phone_order_out_of_stock',
        'type'     => 'select',
        'options'  => array(
          'hide'     => __('Hide form', 'woo-phone-order'),
          'show'     => __('Show form', 'woo-phone-order'),
          'disable'  => __('Show disabled form', 'woo-phone-order'),
        ),
        'default'  => 'hide',
      ),
      array(
        'type' => 'sectionend',
        'id'   => 'woo_phone_order_settings',
      ),
    );

    return $woo_phone_order_settings;
  }

  public static function update_settings()
  {
    woocommerce_update_options(self::add_settings(array(), 'woo_phone_order'));
  }
}
