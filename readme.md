# Woo Phone Order

Woo Phone Order is a WooCommerce extension that allows customers to place quick orders using just their phone number. This plugin is perfect for businesses looking to streamline their ordering process and reduce friction in the customer journey.

## Features

- Easy order placement with just a phone number
- Customizable form placement on product pages
- Shortcode support for flexible form placement
- Configurable text for form title, subtitle, and description
- Out-of-stock product handling options
- AJAX-powered form submission for a smooth user experience
- Custom "Phone Order" status for easy order management
- Responsive design that works on all devices

## Installation

1. Upload the `woo-phone-order` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure that WooCommerce is installed and activated

## Configuration

1. Go to WooCommerce > Settings > Products > Woo Phone Order Settings
2. Configure the following options:
   - Form Title
   - Form Subtitle
   - Form Description
   - Display on Product Pages (Disabled, After product summary, or After add to cart button)
   - Out of Stock Behavior (Hide form, Show form, or Show disabled form)
3. Save your changes

## Usage

### Automatic Display on Product Pages

If you've configured the "Display on Product Pages" setting, the phone order form will automatically appear on your product pages in the selected location.

### Shortcode

You can use the `[woo_phone_order]` shortcode to display the form anywhere on your site. You can also specify a product ID:

```
[woo_phone_order product_id="123"]
```

If no product ID is specified, it will use the current product ID (on product pages) or the latest product.

## Customization

### CSS

The plugin includes basic styling to make the form look good out of the box. If you want to customize the appearance, you can add your own CSS rules to your theme's stylesheet.

### Hooks and Filters

Developers can use the following hooks to extend the plugin's functionality:

- `woo_phone_order_created` action: Fired when a new phone order is created. Parameters: `$order_id`, `$phone`, `$product_id`

## Frequently Asked Questions

**Q: Can customers order multiple products or specify quantities?**
A: Currently, the plugin is designed for quick, single-product orders. For more complex orders, customers should use the regular WooCommerce checkout process.

**Q: How are phone orders managed?**
A: Phone orders are created with a custom "Phone Order" status. You can manage these orders from the WooCommerce Orders page in your WordPress admin.

**Q: Is this plugin compatible with my theme?**
A: The plugin should work with any WooCommerce-compatible theme. If you encounter any styling issues, you may need to add some custom CSS to your theme.

## Support

If you encounter any issues or have questions about the plugin, please open an issue on our GitHub repository or contact us through our website.

## Contributing

We welcome contributions to improve Woo Phone Order. Please fork the repository and submit a pull request with your changes.

## License

Woo Phone Order is released under the GPL v2 or later license.

## Credits

Woo Phone Order is developed and maintained by OpenWPClub.com.
