<?php

/**
 * Plugin Name: Woo Phone Order
 * Plugin URI:  https://openwpclub.com/plugins/woo-phone-order/
 * Description: Fast order creation with just a phone number for WooCommerce
 * Author:      OpenWPClub.com
 * Author URI:  https://openwpclub.com/
 * Version:     1.0.0
 * Text Domain: woo-phone-order
 * Domain Path: /languages/
 * Requires at least: 5.6
 * Requires PHP: 7.2
 * WC requires at least: 5.0
 * WC tested up to: 7.x
 */

defined('ABSPATH') || exit;

if (!class_exists('Woo_Phone_Order')) :

  class Woo_Phone_Order
  {
    /**
     * Plugin instance.
     *
     * @var Woo_Phone_Order
     */
    protected static $instance = null;

    /**
     * Get plugin instance.
     *
     * @return Woo_Phone_Order
     */
    public static function get_instance()
    {
      if (null === self::$instance) {
        self::$instance = new self();
      }
      return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct()
    {
      add_action('plugins_loaded', array($this, 'init_plugin'));
    }

    /**
     * Initialize plugin.
     */
    public function init_plugin()
    {
      if (!class_exists('WooCommerce')) {
        add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
        return;
      }

      $this->define_constants();
      $this->includes();
      $this->init_hooks();

      do_action('woo_phone_order_loaded');
    }

    /**
     * Define plugin constants.
     */
    private function define_constants()
    {
      define('WOO_PHONE_ORDER_VERSION', '1.0.0');
      define('WOO_PHONE_ORDER_FILE', __FILE__);
      define('WOO_PHONE_ORDER_PATH', plugin_dir_path(WOO_PHONE_ORDER_FILE));
      define('WOO_PHONE_ORDER_URL', plugin_dir_url(WOO_PHONE_ORDER_FILE));
    }

    /**
     * Include required files.
     */
    private function includes()
    {
      require_once WOO_PHONE_ORDER_PATH . 'includes/class-woo-phone-order-form.php';
      require_once WOO_PHONE_ORDER_PATH . 'includes/class-woo-phone-order-ajax.php';
      require_once WOO_PHONE_ORDER_PATH . 'includes/class-woo-phone-order-settings.php';
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks()
    {
      add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
      add_action('plugins_loaded', array($this, 'load_textdomain'));

      // Initialize components
      Woo_Phone_Order_Form::init();
      Woo_Phone_Order_Ajax::init();
      Woo_Phone_Order_Settings::init();
    }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_scripts()
    {
      if (is_product()) {
        wp_enqueue_style('woo-phone-order', WOO_PHONE_ORDER_URL . 'assets/css/woo-phone-order.css', array(), WOO_PHONE_ORDER_VERSION);
        wp_enqueue_script('woo-phone-order', WOO_PHONE_ORDER_URL . 'assets/js/woo-phone-order.js', array('jquery'), WOO_PHONE_ORDER_VERSION, true);
        wp_localize_script('woo-phone-order', 'woo_phone_order_params', array(
          'ajax_url' => admin_url('admin-ajax.php'),
          'nonce' => wp_create_nonce('woo-phone-order-nonce')
        ));
      }
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain()
    {
      load_plugin_textdomain('woo-phone-order', false, dirname(plugin_basename(WOO_PHONE_ORDER_FILE)) . '/languages/');
    }

    /**
     * WooCommerce missing notice.
     */
    public function woocommerce_missing_notice()
    {
      echo '<div class="error"><p>' . sprintf(__('Woo Phone Order requires WooCommerce to be installed and active. You can download %s here.', 'woo-phone-order'), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>') . '</p></div>';
    }

    /**
     * Activation hook.
     */
    public static function activate()
    {
      if (!current_user_can('activate_plugins')) {
        return;
      }

      // Add default settings
      add_option('woo_phone_order_title', __('Quick checkout', 'woo-phone-order'));
      add_option('woo_phone_order_subtitle', __('No registration required', 'woo-phone-order'));
      add_option('woo_phone_order_description', __('Just enter your phone number to place an order. We\'ll contact you to confirm the details.', 'woo-phone-order'));
      add_option('woo_phone_order_out_of_stock', 'hide');

      flush_rewrite_rules();
    }

    /**
     * Deactivation hook.
     */
    public static function deactivate()
    {
      if (!current_user_can('activate_plugins')) {
        return;
      }

      flush_rewrite_rules();
    }
  }

  // Installation and uninstallation hooks
  register_activation_hook(__FILE__, array('Woo_Phone_Order', 'activate'));
  register_deactivation_hook(__FILE__, array('Woo_Phone_Order', 'deactivate'));

  // Initialize the plugin
  function woo_phone_order()
  {
    return Woo_Phone_Order::get_instance();
  }

  // Start the plugin
  woo_phone_order();

endif;
