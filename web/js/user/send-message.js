function sendMessage() {

    var fd = new FormData();
    fd.append('name', $('#name').val());
    fd.append('email', $('#email').val());
    fd.append('phone', $('#phone').val());
    fd.append('title', $('#title').val());
    fd.append('message', $('#message').val());

    $.ajax({
        url: window.location.href + "/message",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function(data) {

            // No error from the server.
            if(!data['error']) {

                $('#title').val('');
                $('#message').val('');

                swal('Success', 'Your message was sent!', 'success');
            }

            // Error from the server.
            else {
                swal('Error', data['error'], 'error');
            }
        },
        error: function() {
            swal('Error', 'Server error, try sending message later.', 'warning');
        }
    });
}