$(function () {

    $('.notification').click(function () {
        $(this).fadeOut(100);
    });

    // Dropdown toggles
    $('[data-dropdown]').dropDown();

});