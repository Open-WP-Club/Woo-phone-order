# Phone Order for WooCommerce

Quick order creation with just a phone number for WooCommerce stores. This plugin enables customers to place orders instantly by entering their phone number on product pages.

## Features

- **Quick Orders** - Customers order with just a phone number
- **Smart Customer Matching** - Automatically matches existing customers by phone
- **Guest Customer Support** - Creates guest accounts for new phone numbers
- **Flexible Display** - Show form after product summary, after add-to-cart, or use shortcode
- **Stock Management** - Respects product stock status with configurable behavior
- **Full Customization** - Configure all form text via settings page
- **HPOS Compatible** - Works with WooCommerce High Performance Order Storage
- **Modern UI** - Responsive design with loading states and visual feedback
- **Developer Friendly** - Hooks, filters, and clean code structure

## Requirements

- WordPress 6.0 or higher
- WooCommerce 8.0 or higher
- PHP 7.4 or higher

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate through the 'Plugins' menu in WordPress
3. Ensure WooCommerce is installed and activated
4. Configure settings at WooCommerce > Settings > Phone Order

## Configuration

Navigate to **WooCommerce > Settings > Phone Order** to configure:

### Form Content
- **Form Title** - Main heading (default: "Quick Purchase")
- **Form Subtitle** - Tagline/subtitle
- **Form Description** - Explanatory text
- **Button Text** - Submit button label (default: "Order Now")

### Display Options
- **After product summary** - Shows below product details
- **After add to cart button** - Shows near purchase button
- **Disabled** - Use shortcode only

### Stock Behavior
- **Hide form** - Don't show for out-of-stock products
- **Show form** - Display but reject orders
- **Show disabled** - Display with disabled state and notice

## Usage

### Automatic Display

Configure display position in settings. Form appears automatically on product pages.

### Shortcode

```
[woo_phone_order]
```

With specific product:
```
[woo_phone_order product_id="123"]
```

If no product ID specified, uses current product or latest product.

## How It Works

1. Customer enters phone number on product page
2. Plugin checks for existing customer with that phone
3. Creates guest account if customer is new
4. Creates WooCommerce order with "Processing" status
5. Store owner contacts customer to complete order

Orders use WooCommerce's standard `billing_phone` field for compatibility.

## Developer Hooks

### Actions

**`wc_phone_order_created`**
Fires when order is created via phone order.

```php
do_action('wc_phone_order_created', $order_id, $phone, $product_id);
```

### Filters

**`wc_phone_order_settings`**
Modify settings page fields.

```php
add_filter('wc_phone_order_settings', function($settings) {
    // Modify $settings array
    return $settings;
});
```

## Frequently Asked Questions

**Can customers order multiple products?**
No, this is designed for quick single-product orders. Customers should use regular checkout for complex orders.

**How are orders managed?**
Orders appear in WooCommerce Orders with "Processing" status. Filter by payment method "Phone Order".

**Does it work with HPOS?**
Yes! Fully compatible with WooCommerce High Performance Order Storage (Custom Order Tables).

**Can I style the form?**
Yes! Add custom CSS targeting `.woo-phone-order` classes. Uses BEM methodology for easy customization.

**What happens to customer data?**
Uses WooCommerce standard fields. Existing customers are matched by phone. New customers get guest accounts.

## Support

For issues or questions:
- Open an issue on [GitHub](https://github.com/Open-WP-Club/Woo-phone-order)
- Visit [OpenWPClub.com](https://openwpclub.com)

## Contributing

Contributions welcome! Fork the repository and submit pull requests.

## Changelog

### 1.1.0
- Added HPOS compatibility
- Added full settings page
- Added shortcode support
- Added customer matching by phone
- Added guest customer creation
- Added stock validation
- Added configurable form placement
- Added out-of-stock behavior options
- Improved CSS with BEM methodology
- Improved JavaScript error handling
- Fixed deprecated functions
- Updated compatibility for WooCommerce 9.x

### 1.0.0
- Initial release

## License

GPL v2 or later

## Credits

Developed and maintained by [OpenWPClub.com](https://openwpclub.com)
