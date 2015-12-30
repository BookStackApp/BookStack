"use strict";
// Configure ZeroClipboard
var zeroClipBoard = require('zeroclipboard');
zeroClipBoard.config({
    swfPath: '/ZeroClipboard.swf'
});

window.setupPageShow = module.exports = function (pageId) {

    // Set up pointer
    var $pointer = $('#pointer').detach();
    var $pointerInner = $pointer.children('div.pointer').first();
    var isSelection = false;

    // Select all contents on input click
    $pointer.on('click', 'input', function(e) {
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
        var selection = window.getSelection();
        if (selection.toString().length === 0) return;

        // Show pointer and set link
        var $elem = $(this);
        var link = window.location.protocol + "//" + window.location.host + '/link/' + pageId + '#' + $elem.attr('id');
        $pointer.find('input').val(link);
        $pointer.find('button').first().attr('data-clipboard-text', link);
        $elem.before($pointer);
        $pointer.show();

        // Set pointer to sit near mouse-up position
        var pointerLeftOffset = (e.pageX - $elem.offset().left - ($pointerInner.width() / 2));
        if (pointerLeftOffset < 0) pointerLeftOffset = 0;
        var pointerLeftOffsetPercent = (pointerLeftOffset / $elem.width()) * 100;
        $pointerInner.css('left', pointerLeftOffsetPercent + '%');

        e.stopPropagation();

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

};