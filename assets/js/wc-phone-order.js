(function ($) {
  "use strict";

  $(document).ready(function () {
    $("#woo-phone-order-form").on("submit", function (e) {
      e.preventDefault();
      console.log("Form submitted");

      var $form = $(this);
      var $message = $form.find(".woo-phone-order-message");
      var phone = $form.find('input[name="phone"]').val().trim();
      var productId = $form.data("product-id");

      if (phone.length < 5) {
        $message.text("Please enter a valid phone number.").show();
        return;
      }

      $form.find("button").prop("disabled", true);
      $message.text("Processing your order...").show();

      console.log("Sending AJAX request for product: " + productId);

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
          console.log("Received response:", response);
          if (response.success) {
            $message.text(response.data.message).show();
            $form.find('input[name="phone"]').val("");
          } else {
            $message.text(response.data).show();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("Ajax error:", textStatus, errorThrown);
          $message.text("An error occurred. Please try again.").show();
        },
        complete: function () {
          $form.find("button").prop("disabled", false);
        },
      });
    });
  });
})(jQuery);
