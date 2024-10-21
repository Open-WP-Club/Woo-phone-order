<?php
defined('ABSPATH') || exit;

class Woo_Phone_Order_Form
{
    public static function init()
    {
        // No additional initialization needed
    }

    public static function display_form()
    {
        if (!is_product()) {
            return;
        }

        global $product;

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
        $is_in_stock = $product->is_in_stock();

        ob_start();
        ?>
        <div class="woo-phone-order">
            <h3><?php echo esc_html(get_option('woo_phone_order_title', __('Quick Order', 'woo-phone-order'))); ?></h3>
            <p><?php echo esc_html(get_option('woo_phone_order_description', __('Enter your phone number for a fast order:', 'woo-phone-order'))); ?></p>
            <form id="woo-phone-order-form" data-product-id="<?php echo esc_attr($product->get_id()); ?>" <?php echo !$is_in_stock ? ' disabled' : ''; ?>>
                <input type="tel" name="phone" required placeholder="<?php esc_attr_e('Your phone number', 'woo-phone-order'); ?>" pattern="[0-9+\s()-]{5,20}" <?php echo !$is_in_stock ? ' disabled' : ''; ?>>
                <button type="submit" <?php echo !$is_in_stock ? ' disabled' : ''; ?>><?php esc_html_e('Order Now', 'woo-phone-order'); ?></button>
                <div class="woo-phone-order-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}