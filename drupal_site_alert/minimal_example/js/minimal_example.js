(function ($) {
  Drupal.behaviors.minimal_example_alert= {
    attach: function (context, settings) {
      // Define cookie name based on header_alert block being rendered
      var $cookie_name = $(".header_alert").attr("id");
      // If cookie is not already present, create it
      if ($.cookie($cookie_name) != 1) {
        // Create cookie that expires 30 days from now and is valid across entire site
        $.cookie($cookie_name, '1', { expires: 30, path: '/' });
//        // Create cookie that expires a variable number of days from now and is valid across entire site
//        var $expires = settings.header_alert_settings.hours;
//        $.cookie($cookie_name, '1', { expires: $expires, path: '/' });
        $('.header_alert').show();
      }
    }
  }
}(jQuery));