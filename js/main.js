/**
 * Created by Alex on 28.09.2017.
 */

$(document).ready(function(){
   $(document).click(function () {
       $('#auth .message').css({'display' : 'none'});
   });

    $('#smiles span.smile').click(function(event){
        var code = $(event.target).nextAll('input').val();
        var textarea = $('#chat_send textarea');
        textarea.val(textarea.val() + code);
        textarea.focus();
    });
});