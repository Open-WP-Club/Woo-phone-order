<?php
defined('ABSPATH') || exit;

class WC_Phone_Order_Form
{
  public static function init()
  {
    // Hook into different positions based on settings
    add_action('init', array(__CLASS__, 'setup_display_hooks'));

    // Register shortcode
    add_shortcode('woo_phone_order', array(__CLASS__, 'shortcode_handler'));
  }

  /**
   * Setup display hooks based on settings
   */
  public static function setup_display_hooks()
  {
    $display_position = WC_Phone_Order_Settings::get_option('display_position', 'after_summary');

    switch ($display_position) {
      case 'after_summary':
        add_action('woocommerce_after_single_product_summary', array(__CLASS__, 'display_form'), 11);
        break;
      case 'after_add_to_cart':
        add_action('woocommerce_after_add_to_cart_button', array(__CLASS__, 'display_form'));
        break;
      case 'disabled':
      default:
        // Don't display automatically
        break;
    }
  }

  /**
   * Display form on product pages
   */
  public static function display_form()
  {
    global $product;

    if (!$product) {
      return;
    }

    self::render_form($product->get_id());
  }

  /**
   * Shortcode handler
   */
  public static function shortcode_handler($atts)
  {
    $atts = shortcode_atts(array(
      'product_id' => 0,
    ), $atts, 'woo_phone_order');

    $product_id = absint($atts['product_id']);

    // If no product ID specified, try to get current product
    if (!$product_id) {
      global $product;
      if ($product) {
        $product_id = $product->get_id();
      }
    }

    // If still no product ID, get the latest product
    if (!$product_id) {
      $products = wc_get_products(array('limit' => 1, 'orderby' => 'date', 'order' => 'DESC'));
      if (!empty($products)) {
        $product_id = $products[0]->get_id();
      }
    }

    if (!$product_id) {
      return '';
    }

    ob_start();
    self::render_form($product_id);
    return ob_get_clean();
  }

  /**
   * Render the phone order form
   */
  private static function render_form($product_id)
  {
    $product = wc_get_product($product_id);

    if (!$product || !$product->is_purchasable()) {
      return;
    }

    // Get settings
    $form_title = WC_Phone_Order_Settings::get_option('form_title');
    $form_subtitle = WC_Phone_Order_Settings::get_option('form_subtitle');
    $form_description = WC_Phone_Order_Settings::get_option('form_description');
    $button_text = WC_Phone_Order_Settings::get_option('form_button_text');
    $out_of_stock_behavior = WC_Phone_Order_Settings::get_option('out_of_stock_behavior', 'hide');

    $is_in_stock = $product->is_in_stock();

    // Handle out of stock behavior
    if (!$is_in_stock && $out_of_stock_behavior === 'hide') {
      return;
    }

    $form_class = 'woo-phone-order';
    $form_disabled = false;

    if (!$is_in_stock && $out_of_stock_behavior === 'disabled') {
      $form_class .= ' woo-phone-order--disabled';
      $form_disabled = true;
    }

?>
    <div class="<?php echo esc_attr($form_class); ?>">
      <?php if ($form_title) : ?>
        <h3 class="woo-phone-order__title"><?php echo esc_html($form_title); ?></h3>
      <?php endif; ?>

      <?php if ($form_subtitle) : ?>
        <p class="woo-phone-order__subtitle"><?php echo esc_html($form_subtitle); ?></p>
      <?php endif; ?>

      <?php if ($form_description) : ?>
        <p class="woo-phone-order__description"><?php echo esc_html($form_description); ?></p>
      <?php endif; ?>

      <form id="woo-phone-order-form" class="woo-phone-order__form" data-product-id="<?php echo esc_attr($product_id); ?>" <?php echo $form_disabled ? 'data-disabled="true"' : ''; ?>>
        <div class="woo-phone-order__input-group">
          <input
            type="tel"
            name="phone"
            class="woo-phone-order__phone-input"
            autocomplete="tel"
            required
            placeholder="<?php esc_attr_e('Your phone number', 'woocommerce-phone-order'); ?>"
            <?php echo $form_disabled ? 'disabled' : ''; ?>>
          <button
            type="submit"
            class="woo-phone-order__submit-button"
            <?php echo $form_disabled ? 'disabled' : ''; ?>>
            <?php echo esc_html($button_text); ?>
          </button>
        </div>
        <?php if (!$is_in_stock && $out_of_stock_behavior === 'disabled') : ?>
          <p class="woo-phone-order__out-of-stock-notice"><?php esc_html_e('This product is currently out of stock', 'woocommerce-phone-order'); ?></p>
        <?php endif; ?>
        <div class="woo-phone-order__message"></div>
      </form>
    </div>
<?php
  }
}
