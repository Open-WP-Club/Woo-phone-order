<?php

/**
 * Plugin Name: WooCommerce Phone Order
 * Plugin URI:  https://openwpclub.com/plugins/woocommerce-phone-order/
 * Description: Fast order creation with just a phone number for WooCommerce
 * Author:      OpenWPClub.com
 * Author URI:  https://openwpclub.com/
 * Version:     1.1.0
 * Text Domain: woocommerce-phone-order
 * Domain Path: /languages/
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 8.0
 * WC tested up to: 9.5
 */

defined('ABSPATH') || exit;

// Declare HPOS compatibility
add_action('before_woocommerce_init', function () {
  if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
  }
});

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
      define('WC_PHONE_ORDER_VERSION', '1.1.0');
      define('WC_PHONE_ORDER_FILE', __FILE__);
      define('WC_PHONE_ORDER_PATH', plugin_dir_path(WC_PHONE_ORDER_FILE));
      define('WC_PHONE_ORDER_URL', plugin_dir_url(WC_PHONE_ORDER_FILE));
    }

    private function includes()
    {
      require_once WC_PHONE_ORDER_PATH . 'includes/class-settings.php';
      require_once WC_PHONE_ORDER_PATH . 'includes/class-form.php';
      require_once WC_PHONE_ORDER_PATH . 'includes/class-ajax.php';
    }

    private function init_hooks()
    {
      add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

      WC_Phone_Order_Settings::init();
      WC_Phone_Order_Form::init();
      WC_Phone_Order_Ajax::init();
    }

    public function enqueue_scripts()
    {
      global $post;

      // Check if we should load scripts
      $should_load = false;

      // Load on product pages
      if (is_product()) {
        $should_load = true;
      }

      // Load if shortcode is present in content
      if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'woo_phone_order')) {
        $should_load = true;
      }

      if ($should_load) {
        wp_enqueue_style('wc-phone-order', WC_PHONE_ORDER_URL . 'assets/css/wc-phone-order.css', array(), WC_PHONE_ORDER_VERSION);
        wp_enqueue_script('wc-phone-order', WC_PHONE_ORDER_URL . 'assets/js/wc-phone-order.js', array('jquery'), WC_PHONE_ORDER_VERSION, true);
        wp_localize_script('wc-phone-order', 'woo_phone_order_params', array(
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
