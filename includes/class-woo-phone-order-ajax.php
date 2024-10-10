<?php
defined('ABSPATH') || exit;

class Woo_Phone_Order_Ajax
{
  public static function init()
  {
    add_action('wp_ajax_woo_phone_order_submit', array(__CLASS__, 'handle_submit'));
    add_action('wp_ajax_nopriv_woo_phone_order_submit', array(__CLASS__, 'handle_submit'));
  }

  public static function handle_submit()
  {
    check_ajax_referer('woo-phone-order-nonce', 'nonce');

    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

    if (empty($phone) || strlen($phone) < 5 || strlen($phone) > 20 || !preg_match('/^[0-9+\s()-]{5,20}$/', $phone)) {
      wp_send_json_error(__('Invalid phone number', 'woo-phone-order'));
    }

    $product = wc_get_product($product_id);
    if (!$product) {
      wp_send_json_error(__('Invalid product', 'woo-phone-order'));
    }

    // Create the order
    $order = wc_create_order();
    if (is_wp_error($order)) {
      wp_send_json_error(__('Unable to create order', 'woo-phone-order'));
    }

    $order->add_product($product);
    $order->set_billing_phone($phone);
    $order->set_status('wc-phone-order');
    $order->save();

    $order->add_order_note(__('Order placed via Woo Phone Order', 'woo-phone-order'));

    do_action('woo_phone_order_created', $order->get_id(), $phone, $product_id);

    wp_send_json_success(__('Your order has been placed. We\'ll contact you shortly to confirm the details.', 'woo-phone-order'));
  }
}
