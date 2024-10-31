document.addEventListener("DOMContentLoaded", (function($) {
  var active = $(".pxp-nav-menu__item.active").attr('data-container');

  $(".pxp-nav-menu").on("click", ".pxp-nav-menu__item", menuClick);

  init();

  function init() {
    $("div[data-container='" + active + "']").removeClass("hidden");
  }

  function menuClick() {
    var active = $(this).attr('data-container');
    $(".pxp-nav-menu__item.active").removeClass("active");
    $(this).addClass("active");
    $("div[data-container]").addClass("hidden");
    $("div[data-container='" + active + "']").removeClass("hidden");
    if (active == "about") {
      // hide the form submission buttons
      $(".pxp-button-container").hide();
    } else {
      // show the form submission buttons
      $(".pxp-button-container").show();
    }
  }

})(jQuery));
