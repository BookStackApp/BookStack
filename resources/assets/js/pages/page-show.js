"use strict";
// Configure ZeroClipboard
var zeroClipBoard = require('zeroclipboard');
zeroClipBoard.config({
    swfPath: window.baseUrl('/ZeroClipboard.swf')
});

window.setupPageShow = module.exports = function (pageId) {

    // Set up pointer
    var $pointer = $('#pointer').detach();
    var $pointerInner = $pointer.children('div.pointer').first();
    var isSelection = false;

    // Select all contents on input click
    $pointer.on('click', 'input', function (e) {
        $(this).select();
        e.stopPropagation();
    });

    // Set up copy-to-clipboard
    new zeroClipBoard($pointer.find('button').first()[0]);

    // Hide pointer when clicking away
    $(document.body).find('*').on('click focus', function (e) {
        if (!isSelection) {
            $pointer.detach();
        }
    });

    // Show pointer when selecting a single block of tagged content
    $('.page-content [id^="bkmrk"]').on('mouseup keyup', function (e) {
        e.stopPropagation();
        var selection = window.getSelection();
        if (selection.toString().length === 0) return;

        // Show pointer and set link
        var $elem = $(this);
        let link = window.baseUrl('/link/' + pageId + '#' + $elem.attr('id'));
        if (link.indexOf('http') !== 0) link = window.location.protocol + "//" + window.location.host + link;
        $pointer.find('input').val(link);
        $pointer.find('button').first().attr('data-clipboard-text', link);
        $elem.before($pointer);
        $pointer.show();

        // Set pointer to sit near mouse-up position
        var pointerLeftOffset = (e.pageX - $elem.offset().left - ($pointerInner.width() / 2));
        if (pointerLeftOffset < 0) pointerLeftOffset = 0;
        var pointerLeftOffsetPercent = (pointerLeftOffset / $elem.width()) * 100;
        $pointerInner.css('left', pointerLeftOffsetPercent + '%');

        isSelection = true;
        setTimeout(() => {
            isSelection = false;
        }, 100);
    });

    // Go to, and highlight if necessary, the specified text.
    function goToText(text) {
        var idElem = $('.page-content #' + text).first();
        if (idElem.length !== 0) {
            idElem.smoothScrollTo();
            idElem.css('background-color', 'rgba(244, 249, 54, 0.25)');
        } else {
            $('.page-content').find(':contains("' + text + '")').smoothScrollTo();
        }
    }

    // Check the hash on load
    if (window.location.hash) {
        var text = window.location.hash.replace(/\%20/g, ' ').substr(1);
        goToText(text);
    }

    // Make the book-tree sidebar stick in view on scroll
    var $window = $(window);
    var $bookTree = $(".book-tree");
    var $bookTreeParent = $bookTree.parent();
    // Check the page is scrollable and the content is taller than the tree
    var pageScrollable = ($(document).height() > $window.height()) && ($bookTree.height() < $('.page-content').height());
    // Get current tree's width and header height
    var headerHeight = $("#header").height() + $(".toolbar").height();
    var isFixed = $window.scrollTop() > headerHeight;
    // Function to fix the tree as a sidebar
    function stickTree() {
        $bookTree.width($bookTreeParent.width() + 15);
        $bookTree.addClass("fixed");
        isFixed = true;
    }
    // Function to un-fix the tree back into position
    function unstickTree() {
        $bookTree.css('width', 'auto');
        $bookTree.removeClass("fixed");
        isFixed = false;
    }
    // Checks if the tree stickiness state should change
    function checkTreeStickiness(skipCheck) {
        var shouldBeFixed = $window.scrollTop() > headerHeight;
        if (shouldBeFixed && (!isFixed || skipCheck)) {
            stickTree();
        } else if (!shouldBeFixed && (isFixed || skipCheck)) {
            unstickTree();
        }
    }
    // The event ran when the window scrolls
    function windowScrollEvent() {
        checkTreeStickiness(false);
    }

    // If the page is scrollable and the window is wide enough listen to scroll events
    // and evaluate tree stickiness.
    if (pageScrollable && $window.width() > 1000) {
        $window.on('scroll', windowScrollEvent);
        checkTreeStickiness(true);
    }

    // Handle window resizing and switch between desktop/mobile views
    $window.on('resize', event => {
        if (pageScrollable && $window.width() > 1000) {
            $window.on('scroll', windowScrollEvent);
            checkTreeStickiness(true);
        } else {
            $window.off('scroll', windowScrollEvent);
            unstickTree();
        }
    });

};
