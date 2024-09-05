jQuery(function ($) {
  // Search toggle
  $(".nav-secondary .search-toggle").click(function (e) {
    e.preventDefault();
    $(this).parent().toggleClass("active").find('input[type="search"]').focus();
  });
  $(".search-submit").click(function (e) {
    if ($(this).parent().find(".search-field").val() == "") {
      e.preventDefault();
      $(this).parent().parent().removeClass("active");
    }
  });
	$(".search-form-input").keyup(function(e) {
		// Ref https://stackoverflow.com/questions/3369593/how-to-detect-escape-key-press-with-pure-js-or-jquery
		// Close search if esc key pressed
		if (e.key == "Escape") {
		  $(this).parent().parent().removeClass("active");
		}
	});
});

