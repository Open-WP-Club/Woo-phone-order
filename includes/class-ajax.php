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

    error_log('Woo Phone Order - Received request: ' . print_r($_POST, true));

    if (empty($phone) || strlen($phone) < 5 || strlen($phone) > 20 || !preg_match('/^[0-9+\s()-]{5,20}$/', $phone)) {
      wp_send_json_error(__('Invalid phone number', 'woo-phone-order'));
    }

    $product = wc_get_product($product_id);
    if (!$product) {
      wp_send_json_error(__('Invalid product', 'woo-phone-order'));
    }

    error_log('Woo Phone Order - Creating order for product: ' . $product_id);

    try {
      // Create the order
      $order = wc_create_order();

      // Add the product to the order
      $order->add_product($product, 1);

      // Set billing phone
      $order->set_billing_phone($phone);

      // Set order status to completed
      $order->set_status('completed');

      // Calculate and set totals
      $order->calculate_totals();

      // Set payment method to 'Phone Order'
      $order->set_payment_method('phone_order');
      $order->set_payment_method_title('Phone Order');

      // Save the order
      $order->save();

      $order->add_order_note(__('Order placed and completed via Woo Phone Order', 'woo-phone-order'));

      // Reduce stock levels
      wc_reduce_stock_levels($order->get_id());

      error_log('Woo Phone Order - Order created successfully: ' . $order->get_id());

      do_action('woo_phone_order_created', $order->get_id(), $phone, $product_id);

      wp_send_json_success(array(
        'message' => __('Your order has been placed and completed. We\'ll contact you shortly on the provided number.', 'woo-phone-order'),
        'order_id' => $order->get_id()
      ));
    } catch (Exception $e) {
      error_log('Woo Phone Order - Error creating order: ' . $e->getMessage());
      wp_send_json_error(__('An error occurred while processing your order. Please try again.', 'woo-phone-order'));
    }
  }
}
