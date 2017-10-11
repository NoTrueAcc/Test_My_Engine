$(document).ready(function() {
	updateChat();
	setInterval(updateChat, 3000);
	$('#chat_messages').scrollTop(99999);

	$('#send_message').click(function () {
		var message = $('#chat_send textarea').val();

		if(message.length < 1)
		{
			alert('Вы не ввели текст сообщения!');
		}
		else
		{
			$('#chat_send textarea').val('');
			var query = 'func=add_chat_message&text=' + encodeURIComponent(message);
			ajax(query, errorChat, updateChat)
		}
	});

	$('#chat_send textarea').on('keyup', function (event) {
		if(event.keyCode == 13)
		{
			var message = $('#chat_send textarea').val();

			if(message.length < 1)
			{
				alert('Вы не ввели текст сообщения!');
			}
			else
			{
				$('#chat_send textarea').val('');
				var query = 'func=add_chat_message&text=' + encodeURIComponent(message);
				ajax(query, errorChat, updateChat)
			}
		}
	});
});

function getTemplateChatMessage(date, name, message, my_comment)
{
	if(my_comment)
	{
		var my_comment = "my_comment";
	}
	else
	{
		var my_comment = "chat_message";
	}

	var str = "<p class=\"" + my_comment + "\"><span>" + date + " <span>" + name + ":</span></span>" + message + "</p>";

	return str;
}

function ajax(data, func_error, func_success)
{
	$.ajax({
		url: '/api.php',
		type: 'POST',
		data: (data),
		dataType: 'text',
		error: func_error,
		success: function(result){
			result = $.parseJSON(result);
			func_success(result);
		}
	});
}

function errorChat()
{

}

function updateChat()
{
	var query = 'func=update_chat';
	ajax(query, errorChat, updateChatMessages);
}

function updateChatMessages(data)
{
	data = data['r'];
	data = JSON.parse(data);

	for(var i = 0; i < data.length; i++)
	{
		var lastMessage = $('#chat_messages p:last-child span:first-child').html();

		if(!lastMessage || (data[i].date > lastMessage))
		{
			var newMessage = getTemplateChatMessage(data[i].date, data[i].name, data[i].message, data[i].my_comment);

			$(newMessage).appendTo('#chat_messages');

			$('#chat_messages').scrollTop(99999);
		}
	}
}