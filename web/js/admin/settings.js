$(document).ready(function() {

    $('#input-image').on("change", function(evt) {
        loadImage(evt);
    });

    $('#remove-portfolio-pic').on("click", function() {
       removePortfolioPic();
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
            $('#portfolio-pic').attr('src', fr.result);
        };
        fr.readAsDataURL(files[0]);
    }
}

function removePortfolioPic() {
    // Hiding remove icon
    $('.fa-remove').addClass('d-none');

    // Removing value from input.
    $('#input-image').val('');

    // Setting the image with default image.
    $('#portfolio-pic').attr('src', '/img/alexandra_index_image.jpg');
}
