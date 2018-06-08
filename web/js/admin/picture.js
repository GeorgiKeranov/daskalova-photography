$(document).ready(function() {

    $('#input-picture').on("change", function(evt) {
        loadImage(evt);
    });

    $('#remove-portfolio-pic').on("click", function() {
        removeImage();
    });

    $('.btn-danger').on("click", function() {

        var tableRow = $(this).parent().parent();
        var idOfPicture = $(this).attr('data-id');

        swal({
            title: "Are you sure?",
            text: "Are you sure you want to delete this picture?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: "Delete it!",
            confirmButtonColor: "#ec6c62"
        }, function() {

            $.ajax({
                url: '/admin/pictures/' + idOfPicture + '/delete',
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (data) {
                    swal('Success', 'Picture deleted successfully.', 'success');

                    tableRow.remove();
                },
                error: function(error) {
                    swal('Error', 'Picture with that id is not existing!', 'error');
                }
            });

        });

    });

});


function loadImage(evt) {
    var tgt = evt.target || window.event.srcElement, files = tgt.files;

    if (FileReader && files && files.length) {
        var fr = new FileReader();
        fr.onload = function () {

            // Showing remove picture button.
            $('.fa-remove').removeClass('d-none');

            // Setting the image with the input file.
            $('#picture').attr('src', fr.result);
        };
        fr.readAsDataURL(files[0]);
    }
}

function removeImage() {
    // Hiding remove icon
    $('.fa-remove').addClass('d-none');

    // Removing value from input.
    $('#input-picture').val('');

    // Setting the image with default image.
    $('#picture').attr('src', '/img/no-picture-available.png');
}
