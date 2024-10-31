document.addEventListener("DOMContentLoaded", (function($) {
  if ($(".htaccess_writeable").length) {
    $("#cloudfrontConfigForm").submit(function(e) {
      e.preventDefault();
      var message = "It is strongly recommended that you backup your .htaccess file before proceeding. Do you want to continue saving?";
      if (window.confirm(message)) {
        this.submit();
      }
    });
  }
  $("#invalidateNow").click(function() {
    $("#cloudfrontInvalidateNow").submit();
  });
})(jQuery));
