var tmp_id = 0;
var tmp_comment = null;

$(document).ready(function() {
	var w = $(window).width();
	if (w <= 768) {
		var left = $("#left");
		var right = $("#right");
		left.html(left.html() + right.html());
		right.remove();
	}
	if (w <= 600) {
		var h2 = $("#course h2");
		h2.remove();
		h2.prependTo("#course");
	}
	if (w <= 468) {
		$("#top_sep").replaceWith("<br /><br />");
	}
	$("a[rel='external']").attr("target", "_blank");
	prettyPrint();
});