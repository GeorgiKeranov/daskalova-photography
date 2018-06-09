var currentLink = getCurrentLink();

$(document).ready(function() {

    loadAllCategoriesAJAX();

});

function getCurrentLink() {

    var currentLink = window.location.href;
    currentLink = currentLink.split('/');
    currentLink = currentLink.slice(3);

    if (currentLink.length > 1) {
        currentLink = [currentLink[0], currentLink[1]];
    }

    currentLink = '/' + currentLink.join('/');

    return currentLink;
}

function setActiveLink(link) {
    $('[data-link="' + link + '"]').addClass('active');
}

var menuOptionPrototype =
    "<li class=\"nav-item\" data-link=\"/gallery/__LINK__\">\n" +
    "<a class=\"nav-link\" href=\"/gallery/__LINK__\">__NAME__</a>\n" +
    "</li>";

function loadAllCategoriesAJAX() {

    $.ajax({
        url: '/categories',
        type: 'GET',
        success: function (categories) {

            var newMenuOption = null;
            var leftNavigation = $('#left-navigation');

            for(var category in categories) {
                newMenuOption = menuOptionPrototype.replace(/__LINK__/g, categories[category]['link']);
                newMenuOption = newMenuOption.replace('__NAME__', categories[category]['name']);

                leftNavigation.append(newMenuOption);
            }

            setActiveLink(currentLink);
        }
    });
}