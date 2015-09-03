$(function () {

    // Notification hiding
    $('.notification').click(function () {
        $(this).fadeOut(100);
    });

    // Dropdown toggles
    $('[data-dropdown]').dropDown();

    // Chapter page list toggles
    $('.chapter-toggle').click(function(e) {
        e.preventDefault();
        $(this).toggleClass('open');
        $(this).closest('.book-child').find('.inset-list').slideToggle(180);
    });

});