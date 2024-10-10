<?php
defined('ABSPATH') || exit;

class Woo_Phone_Order_Form
{
  public static function init()
  {
    add_action('woocommerce_single_product_summary', array(__CLASS__, 'display_form'), 31);
    add_shortcode('woo_phone_order', array(__CLASS__, 'shortcode'));
  }

  public static function display_form()
  {
    if (!is_product()) {
      return;
    }

    global $product;

    if (!$product->is_in_stock() && 'hide' === get_option('woo_phone_order_out_of_stock', 'hide')) {
      return;
    }

    echo self::get_form_html($product);
  }

  public static function shortcode($atts)
  {
    $atts = shortcode_atts(array(
      'product_id' => get_the_ID(),
    ), $atts, 'woo_phone_order');

    $product = wc_get_product($atts['product_id']);

    if (!$product) {
      return '';
    }

    return self::get_form_html($product);
  }

  private static function get_form_html($product)
  {
    ob_start();
?>
    <div class="woo-phone-order">
      <h3><?php echo esc_html(get_option('woo_phone_order_title', __('Quick checkout', 'woo-phone-order'))); ?></h3>
      <h4><?php echo esc_html(get_option('woo_phone_order_subtitle', __('No registration required', 'woo-phone-order'))); ?></h4>
      <p><?php echo esc_html(get_option('woo_phone_order_description', __('Just enter your phone number to place an order. We\'ll contact you to confirm the details.', 'woo-phone-order'))); ?></p>
      <form id="woo-phone-order-form" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
        <input type="tel" name="phone" required placeholder="<?php esc_attr_e('Enter your phone number', 'woo-phone-order'); ?>" pattern="[0-9+\s()-]{5,20}">
        <button type="submit"><?php esc_html_e('Place Order', 'woo-phone-order'); ?></button>
        <div class="woo-phone-order-message"></div>
      </form>
    </div>
<?php
    return ob_get_clean();
  }
}
