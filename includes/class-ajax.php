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

    try {
      $order = wc_create_order();

      $order->add_product($product, 1);
      $order->set_billing_phone($phone);
      $order->set_status('processing');
      $order->set_payment_method('phone_order');
      $order->set_payment_method_title('Phone Order');
      $order->calculate_totals();
      $order->save();

      $order->add_order_note(__('Order placed via WooCommerce Phone Order', 'woocommerce-phone-order'));

      wc_reduce_stock_levels($order->get_id());

      do_action('wc_phone_order_created', $order->get_id(), $phone, $product_id);

      wp_send_json_success(array(
        'message' => __('Your order has been placed. We\'ll contact you shortly on the provided number to process your order.', 'woocommerce-phone-order'),
        'order_id' => $order->get_id()
      ));
    } catch (Exception $e) {
      wp_send_json_error(__('An error occurred while processing your order. Please try again.', 'woocommerce-phone-order'));
    }
  }

  private static function validate_phone($phone)
  {
    return !empty($phone) && strlen($phone) >= 5 && strlen($phone) <= 20 && preg_match('/^[0-9+\s()-]{5,20}$/', $phone);
  }
}
