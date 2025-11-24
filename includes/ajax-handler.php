<?php
defined('ABSPATH') || exit;

class WC_Phone_Order_Ajax
{
  public static function init()
  {
    add_action('wp_ajax_wc_phone_order_submit', array(__CLASS__, 'handle_submit'));
    add_action('wp_ajax_nopriv_wc_phone_order_submit', array(__CLASS__, 'handle_submit'));
  }

  public static function handle_submit()
  {
    check_ajax_referer('wc-phone-order-nonce', 'nonce');

    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

    if (empty($phone) || !self::validate_phone($phone)) {
      wp_send_json_error(__('Invalid phone number', 'woocommerce-phone-order'));
      return;
    }

    $product = wc_get_product($product_id);
    if (!$product || !$product->is_purchasable()) {
      wp_send_json_error(__('Invalid or unpurchasable product', 'woocommerce-phone-order'));
      return;
    }

    // Check if product is in stock
    if (!$product->is_in_stock()) {
      wp_send_json_error(__('This product is currently out of stock', 'woocommerce-phone-order'));
      return;
    }

    try {
      // Try to find existing customer by phone number
      $customer_id = self::find_customer_by_phone($phone);

      // If no customer found, create guest account
      if (!$customer_id) {
        $customer_id = self::create_guest_customer($phone);
      }

      $order = wc_create_order(array('customer_id' => $customer_id));

      $order->add_product($product, 1);
      $order->set_billing_phone($phone);
      $order->set_status('processing');
      $order->set_payment_method('phone_order');
      $order->set_payment_method_title('Phone Order');
      $order->calculate_totals();
      $order->save();

      $order->add_order_note(__('Order placed via WooCommerce Phone Order', 'woocommerce-phone-order'));

      // Use modern method - WooCommerce handles stock reduction automatically on order status change
      // But we can explicitly trigger it if needed
      wc_maybe_reduce_stock_levels($order->get_id());

      do_action('wc_phone_order_created', $order->get_id(), $phone, $product_id);

      wp_send_json_success(array(
        'message' => __('Your order has been placed. We\'ll contact you shortly on the provided number to process your order.', 'woocommerce-phone-order'),
        'order_id' => $order->get_id()
      ));
    } catch (Exception $e) {
      wp_send_json_error(__('An error occurred while processing your order. Please try again.', 'woocommerce-phone-order'));
    }
  }

  /**
   * Find customer by phone number
   */
  private static function find_customer_by_phone($phone)
  {
    global $wpdb;

    // Search in user meta for billing phone
    $user_id = $wpdb->get_var($wpdb->prepare(
      "SELECT user_id FROM {$wpdb->usermeta}
       WHERE meta_key = 'billing_phone'
       AND meta_value = %s
       LIMIT 1",
      $phone
    ));

    return $user_id ? absint($user_id) : 0;
  }

  /**
   * Create guest customer account
   */
  private static function create_guest_customer($phone)
  {
    // Generate unique username from phone
    $username = 'guest_' . preg_replace('/[^0-9]/', '', $phone) . '_' . time();

    // Generate email from phone (required for WooCommerce customer)
    $email = 'guest_' . preg_replace('/[^0-9]/', '', $phone) . '@phone-order.local';

    // Check if email exists, if so append random string
    if (email_exists($email)) {
      $email = 'guest_' . preg_replace('/[^0-9]/', '', $phone) . '_' . wp_generate_password(5, false) . '@phone-order.local';
    }

    // Create customer
    $customer = new WC_Customer();
    $customer->set_username($username);
    $customer->set_email($email);
    $customer->set_billing_phone($phone);
    $customer->set_role('customer');

    $customer_id = $customer->save();

    return $customer_id;
  }

  private static function validate_phone($phone)
  {
    return !empty($phone) && strlen($phone) >= 5 && strlen($phone) <= 20 && preg_match('/^[0-9+\s()-]{5,20}$/', $phone);
  }
}
