<?php

/**
 * Plugin Name: WooCommerce Phone Order
 * Plugin URI:  https://openwpclub.com/plugins/woocommerce-phone-order/
 * Description: Fast order creation with just a phone number for WooCommerce
 * Author:      OpenWPClub.com
 * Author URI:  https://openwpclub.com/
 * Version:     1.0.0
 * Text Domain: woocommerce-phone-order
 * Domain Path: /languages/
 * Requires at least: 5.6
 * Requires PHP: 7.2
 * WC requires at least: 5.0
 * WC tested up to: 7.x
 */

defined('ABSPATH') || exit;

if (!class_exists('WooCommerce_Phone_Order')) :

  class WooCommerce_Phone_Order
  {
    protected static $instance = null;

    public static function get_instance()
    {
      if (null === self::$instance) {
        self::$instance = new self();
      }
      return self::$instance;
    }

    private function __construct()
    {
      add_action('plugins_loaded', array($this, 'init_plugin'));
    }

    public function init_plugin()
    {
      if (!class_exists('WooCommerce')) {
        add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
        return;
      }

      $this->define_constants();
      $this->includes();
      $this->init_hooks();
    }

    private function define_constants()
    {
      define('WC_PHONE_ORDER_VERSION', '1.0.0');
      define('WC_PHONE_ORDER_FILE', __FILE__);
      define('WC_PHONE_ORDER_PATH', plugin_dir_path(WC_PHONE_ORDER_FILE));
      define('WC_PHONE_ORDER_URL', plugin_dir_url(WC_PHONE_ORDER_FILE));
    }

    private function includes()
    {
      require_once WC_PHONE_ORDER_PATH . 'includes/class-form.php';
      require_once WC_PHONE_ORDER_PATH . 'includes/class-ajax.php';
    }

    private function init_hooks()
    {
      add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

      WC_Phone_Order_Form::init();
      WC_Phone_Order_Ajax::init();
    }

    public function enqueue_scripts()
    {
      if (is_product()) {
        wp_enqueue_style('wc-phone-order', WC_PHONE_ORDER_URL . 'assets/css/wc-phone-order.css', array(), WC_PHONE_ORDER_VERSION);
        wp_enqueue_script('wc-phone-order', WC_PHONE_ORDER_URL . 'assets/js/wc-phone-order.js', array('jquery'), WC_PHONE_ORDER_VERSION, true);
        wp_localize_script('wc-phone-order', 'wc_phone_order_params', array(
          'ajax_url' => admin_url('admin-ajax.php'),
          'nonce' => wp_create_nonce('wc-phone-order-nonce')
        ));
      }
    }

    public function woocommerce_missing_notice()
    {
      echo '<div class="error"><p>' . sprintf(__('WooCommerce Phone Order requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-phone-order'), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>') . '</p></div>';
    }
  }

  function woocommerce_phone_order()
  {
    return WooCommerce_Phone_Order::get_instance();
  }

  woocommerce_phone_order();

endif;
