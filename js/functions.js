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
	
	$('#comment_cancel span').click(function (event) {
		commentCancel();
    });

	$('#add_comment').click(function () {
		commentCancel();
		showFormComment();
    });

	$('#comments .reply_comment').click(function (event) {
		commentCancel();

		var parent_id = $(event.target).parents('div').get(0).id;
		$('#form_add_comment').appendTo('#' + parent_id);
		$('#form_add_comment #parent_id').val(parent_id.substr('comment_'.length));
		showFormComment();
    });

	$('#form_add_comment .button').click(function (event) {
		if($('#form_add_comment textarea').val)
    })
});

function getTemplateComment(id, user_id, name, avatar, text, date)
{
	var str = '<div class="comment" id="comment_' + id + '">';
    str += '<img src="' + avatar + '" alt="' + name + '" />';
    str += '<span class="name">' + name + '</span>';
	str += '<span class="date">' + date + '</span>';
	str += '<p class="text">' + text + '</p>';
	str += '<div class="clear"></div>';
	str += '<p class = "functions">' +
		'<span class="reply_comment">Ответить</span>' +
		'<span class="edit_comment">Редактировать</span>' +
		'<span class="delete_comment">Удалить</span>';
	str += '</div>';

	return str;
}

function showFormComment()
{
	$('#form_add_comment').css('display', 'inline-block');
	$('#form_add_comment textarea').focus();
}

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

function ajax(data, func_error, func_sucess)
{
	$.ajax({
		url: '/api.php',
		type: 'POST',
		data: (data),
		dataType: 'text',
		error: func_error,
		success: function(result){
			result = $.parseJSON(result);
			func_sucess(result);
		}
	});
}