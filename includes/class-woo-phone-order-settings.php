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
        'default'  => __('Quick Order', 'woo-phone-order'),
      ),
      array(
        'title'    => __('Form Description', 'woo-phone-order'),
        'id'       => 'woo_phone_order_description',
        'type'     => 'textarea',
        'default'  => __('Enter your phone number for a fast order:', 'woo-phone-order'),
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
