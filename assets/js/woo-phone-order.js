(function ($) {
  "use strict";

  $(document).ready(function () {
    $("#woo-phone-order-form").on("submit", function (e) {
      e.preventDefault();

      var $form = $(this);
      var $message = $form.find(".woo-phone-order-message");
      var phone = $form.find('input[name="phone"]').val();
      var productId = $form.data("product-id");

      $form.find("button").prop("disabled", true);
      $message.removeClass("success error").hide();

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
            $message.addClass("success").text(response.data).show();
            $form.find('input[name="phone"]').val("");
          } else {
            $message.addClass("error").text(response.data).show();
          }
        },
        error: function () {
          $message
            .addClass("error")
            .text("An error occurred. Please try again.")
            .show();
        },
        complete: function () {
          $form.find("button").prop("disabled", false);
        },
      });
    });
  });
})(jQuery);
