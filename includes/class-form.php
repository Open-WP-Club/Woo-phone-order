<?php
defined('ABSPATH') || exit;

class WC_Phone_Order_Form
{
    public static function init()
    {
        add_action('woocommerce_after_single_product_summary', array(__CLASS__, 'display_form'), 11);
    }

    public static function display_form()
    {
        global $product;

        if (!$product) {
            return;
        }

        $product_id = $product->get_id();

?>
        <div class="wc-phone-order" style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; background-color: #f8f8f8;">
            <h3 style="color: #333;"><?php echo esc_html__('Quick Purchase', 'woocommerce-phone-order'); ?></h3>
            <p><?php echo esc_html__('Enter your phone number for instant purchase:', 'woocommerce-phone-order'); ?></p>
            <form id="wc-phone-order-form" data-product-id="<?php echo esc_attr($product_id); ?>">
                <input type="tel" name="phone" required placeholder="<?php esc_attr_e('Your phone number', 'woocommerce-phone-order'); ?>" style="width: 200px; margin-right: 10px;">
                <button type="submit" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer;"><?php esc_html_e('Buy Now', 'woocommerce-phone-order'); ?></button>
                <div class="wc-phone-order-message" style="margin-top: 10px;"></div>
            </form>
        </div>
<?php
    }
}
