(function ($) {
  "use strict";

  $(document).ready(function () {
    // Handle form submission
    $(document).on("submit", "#woo-phone-order-form", function (e) {
      e.preventDefault();

      var $form = $(this);
      var $message = $form.find(".woo-phone-order__message");
      var $phoneInput = $form.find('input[name="phone"]');
      var $submitButton = $form.find(".woo-phone-order__submit-button");
      var phone = $phoneInput.val().trim();
      var productId = $form.data("product-id");

      // Check if form is disabled
      if ($form.data("disabled")) {
        showMessage($message, "This product is currently unavailable", "error");
        return;
      }

      // Basic validation
      if (phone.length < 5) {
        showMessage($message, "Please enter a valid phone number", "error");
        return;
      }

      // Disable form and show loading
      $submitButton.prop("disabled", true);
      $phoneInput.prop("disabled", true);
      $form.addClass("woo-phone-order__form--loading");
      showMessage($message, "Processing your order...", "info");

      $.ajax({
        url: woo_phone_order_params.ajax_url,
        type: "POST",
        data: {
          action: "woo_phone_order_submit",
          nonce: woo_phone_order_params.nonce,
          phone: phone,
          product_id: productId,
        },
        success: function (response) {
          if (response.success) {
            showMessage($message, response.data.message, "success");
            $phoneInput.val("");
          } else {
            showMessage(
              $message,
              response.data || "An error occurred. Please try again.",
              "error"
            );
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("Ajax error:", textStatus, errorThrown);
          showMessage(
            $message,
            "An error occurred. Please try again.",
            "error"
          );
        },
        complete: function () {
          $submitButton.prop("disabled", false);
          $phoneInput.prop("disabled", false);
          $form.removeClass("woo-phone-order__form--loading");
        },
      });
    });

    /**
     * Show message with proper styling
     */
    function showMessage($element, text, type) {
      $element
        .removeClass(
          "woo-phone-order__message--success woo-phone-order__message--error woo-phone-order__message--info"
        )
        .addClass("woo-phone-order__message--" + type)
        .addClass("woo-phone-order__message--visible")
        .text(text)
        .show();
    }
  });
})(jQuery);
