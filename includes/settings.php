<?php
defined('ABSPATH') || exit;

class WC_Phone_Order_Settings
{
  public static function init()
  {
    add_filter('woocommerce_get_settings_pages', array(__CLASS__, 'add_settings_page'));
  }

  public static function add_settings_page($settings)
  {
    $settings[] = include WC_PHONE_ORDER_PATH . 'includes/settings-page.php';
    return $settings;
  }

  /**
   * Get default settings values
   */
  public static function get_defaults()
  {
    return array(
      'form_title' => __('Quick Purchase', 'woocommerce-phone-order'),
      'form_subtitle' => __('Order with just your phone number', 'woocommerce-phone-order'),
      'form_description' => __('Enter your phone number and we\'ll contact you to complete your order', 'woocommerce-phone-order'),
      'form_button_text' => __('Order Now', 'woocommerce-phone-order'),
      'display_position' => 'after_summary',
      'out_of_stock_behavior' => 'hide',
    );
  }

  /**
   * Get a setting value with fallback to default
   */
  public static function get_option($key, $default = '')
  {
    $defaults = self::get_defaults();
    $option_key = 'wc_phone_order_' . $key;
    return get_option($option_key, isset($defaults[$key]) ? $defaults[$key] : $default);
  }
}
