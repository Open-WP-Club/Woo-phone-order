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

      console.log("Submitting phone order for product: " + productId);

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
            $message.addClass("success").html(response.data.message).show();
            $form.find('input[name="phone"]').val("");
          } else {
            $message.addClass("error").text(response.data).show();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("Ajax error:", textStatus, errorThrown);
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

    // Prevent the default Add to Cart behavior
    $("form.cart").on("submit", function (e) {
      if ($(this).hasClass("woo-phone-order-form")) {
        e.preventDefault();
      }
    });
  });
})(jQuery);
