$(document).ready(function() {

    $('.btn-warning').on("click", function() {

        var nameColumn = $(this).parent().parent().find('.name');

        // Get current name of category
        var currentName = nameColumn.text();
        var currentLink = nameColumn.attr('data-link');

        swal({
            title: "Edit category name!",
            text: "New category name:",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Edit it!"
        }, function(inputValue) {

            if(inputValue == false) {
                return;
            }

            var link = inputValue.split(' ').join('-');

            var fd = new FormData();
            fd.append('name', inputValue);
            fd.append('currentName', currentName);

            $.ajax({
                url: '/admin/categories/edit',
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (data) {

                    if(data['error']) {
                        swal('Error', data['error'], 'error');
                    }

                    else {
                        swal('Success', 'Category edited successfully.', 'success');

                        // Change category name in html.
                        nameColumn.text(inputValue);
                        nameColumn.attr('data-link', link);

                        var menuLi = $('[data-link="' + '/gallery/' + currentLink + '"]');
                        var menuLink = menuLi.find('.nav-link');

                        menuLink.text(inputValue);
                        menuLink.attr('href', '../gallery/' + link);

                        menuLink.attr('data-link', link);
                    }
                }
            });

        });
    });

    $('.btn-danger').on("click", function() {

        var nameColumn = $(this).parent().parent().find('.name');
        var name = nameColumn.text();

        $.ajax({
            url: '/admin/categories/' + name + '/delete',
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(data) {
                // Error from the server.
                if(data['error']) {
                    swal('Error', data['error'], 'error');
                }
                // No error from the server.
                else {
                    swal('Success', 'Category is deleted!', 'success');

                    var link = nameColumn.attr('data-link');

                    nameColumn.parent().remove();

                    $('[data-link="' + '/gallery/' + link + '"]').remove();
                }
            },
            error: function() {
                swal('Error', 'Server error, try deleting category later.', 'warning');
            }
        });

    });

});



