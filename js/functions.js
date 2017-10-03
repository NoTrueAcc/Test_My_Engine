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

	$('.captcha img:first-child').bind('click', function(event){
		var captcha = $('.captcha img:last-child');
		var src = captcha.attr('src');

		if((i = src.indexOf('?')) == -1)
		{
			src += '?' + Math.random();
		}
		else
		{
			src = src.substring(0, i) + '?' + Math.random();
		}

		captcha.attr('src', src);
	});
	
	$(document).click('#comment_cancel span', function (event) {
		commentCancel();
    });


});

function commentCancel()
{
	if(tmp_comment)
	{
		successEditComment(true);
	}

	closeFormComment();
}

function closeFormComment()
{
	$('#form_add_comment #parent_id').val(0);
	$('#form_add_comment #text_comment').val('');
	$('#form_add_comment #comment_id').val(0);
	$('#form_add_comment').css('display', 'none');
}