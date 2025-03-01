
$("#gc-chat-form").on("submit",function(event) {
    event.preventDefault();
    event.stopPropagation();

    let msg = document.getElementById('gc-chat-message');
    let message = msg.value;

    jQuery.ajax({
        type: "POST",
        url: `/dashboard`,
        data: {
            'message': message
        },
    });
    msg.value = '';
    msg.focus();
});

