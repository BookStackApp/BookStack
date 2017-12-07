const Clipboard = require("clipboard");
const Code = require('../code');

let setupPageShow = window.setupPageShow = function (pageId) {

    Code.highlight();

    if (!pageId) return;

    // Set up pointer
    let $pointer = $('#pointer').detach();
    let pointerShowing = false;
    let $pointerInner = $pointer.children('div.pointer').first();
    let isSelection = false;
    let pointerModeLink = true;
    let pointerSectionId = '';

    // Select all contents on input click
    $pointer.on('click', 'input', function (e) {
        $(this).select();
        e.stopPropagation();
    });

    // Pointer mode toggle
    $pointer.on('click', 'span.icon', event => {
        let $icon = $(event.currentTarget);
        pointerModeLink = !pointerModeLink;
        $icon.html(pointerModeLink ? '<i class="zmdi zmdi-link"></i>' : '<i class="zmdi zmdi-square-down"></i>');
        updatePointerContent();
    });

    // Set up clipboard
    let clipboard = new Clipboard('#pointer button');

    // Hide pointer when clicking away
    $(document.body).find('*').on('click focus', event => {
        if (!pointerShowing || isSelection) return;
        let target = $(event.target);
        if (target.is('.zmdi') || $(event.target).closest('#pointer').length === 1) return;

        $pointer.detach();
        pointerShowing = false;
    });

    function updatePointerContent() {
        let inputText = pointerModeLink ? window.baseUrl(`/link/${pageId}#${pointerSectionId}`) : `{{@${pageId}#${pointerSectionId}}}`;
        if (pointerModeLink && inputText.indexOf('http') !== 0) inputText = window.location.protocol + "//" + window.location.host + inputText;

        $pointer.find('input').val(inputText);
    }

    // Show pointer when selecting a single block of tagged content
    $('.page-content [id^="bkmrk"]').on('mouseup keyup', function (e) {
        e.stopPropagation();
        let selection = window.getSelection();
        if (selection.toString().length === 0) return;

        // Show pointer and set link
        let $elem = $(this);
        pointerSectionId = $elem.attr('id');
        updatePointerContent();

        $elem.before($pointer);
        $pointer.show();
        pointerShowing = true;

        // Set pointer to sit near mouse-up position
        let pointerLeftOffset = (e.pageX - $elem.offset().left - ($pointerInner.width() / 2));
        if (pointerLeftOffset < 0) pointerLeftOffset = 0;
        let pointerLeftOffsetPercent = (pointerLeftOffset / $elem.width()) * 100;
        $pointerInner.css('left', pointerLeftOffsetPercent + '%');

        isSelection = true;
        setTimeout(() => {
            isSelection = false;
        }, 100);
    });

    // Go to, and highlight if necessary, the specified text.
    function goToText(text) {
        let idElem = document.getElementById(text);
        $('.page-content [data-highlighted]').attr('data-highlighted', '').css('background-color', '');
        if (idElem !== null) {
            window.scrollAndHighlight(idElem);
        } else {
            $('.page-content').find(':contains("' + text + '")').smoothScrollTo();
        }
    }

    // Check the hash on load
    if (window.location.hash) {
        let text = window.location.hash.replace(/\%20/g, ' ').substr(1);
        goToText(text);
    }

    // Sidebar page nav click event
    $('.sidebar-page-nav').on('click', 'a', event => {
        goToText(event.target.getAttribute('href').substr(1));
    });

    // Make the sidebar stick in view on scroll
    let $window = $(window);
    let $sidebar = $("#sidebar .scroll-body");
    let $bookTreeParent = $sidebar.parent();

    // Check the page is scrollable and the content is taller than the tree
    let pageScrollable = ($(document).height() > $window.height()) && ($sidebar.height() < $('.page-content').height());

    // Get current tree's width and header height
    let headerHeight = $("#header").height() + $(".toolbar").height();
    let isFixed = $window.scrollTop() > headerHeight;

    // Fix the tree as a sidebar
    function stickTree() {
        $sidebar.width($bookTreeParent.width() + 15);
        $sidebar.addClass("fixed");
        isFixed = true;
    }

    // Un-fix the tree back into position
    function unstickTree() {
        $sidebar.css('width', 'auto');
        $sidebar.removeClass("fixed");
        isFixed = false;
    }

    // Checks if the tree stickiness state should change
    function checkTreeStickiness(skipCheck) {
        let shouldBeFixed = $window.scrollTop() > headerHeight;
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


    // Check if support is present for IntersectionObserver
    if ('IntersectionObserver' in window &&
        'IntersectionObserverEntry' in window &&
        'intersectionRatio' in window.IntersectionObserverEntry.prototype) {
        addPageHighlighting();
    }

    function addPageHighlighting() {
      let pageNav = document.querySelector('.sidebar-page-nav');

      // fetch all the headings.
      let headings = document.querySelector('.page-content').querySelectorAll('h1, h2, h3, h4, h5, h6');
      // if headings are present, add observers.
      if (headings.length > 0 && pageNav !== null) {
          addNavObserver(headings);
      }

      function addNavObserver(headings) {
          // Setup the intersection observer.
          let intersectOpts = {
              rootMargin: '0px 0px 0px 0px',
              threshold: 1.0
          };
          let pageNavObserver = new IntersectionObserver(headingVisibilityChange, intersectOpts);

          // observe each heading
          for (let i = 0; i !== headings.length; ++i) {
              pageNavObserver.observe(headings[i]);
          }
      }

      function headingVisibilityChange(entries, observer) {
          for (let i = 0; i < entries.length; i++) {
              let currentEntry = entries[i];
              let isVisible = (currentEntry.intersectionRatio === 1);
              toggleAnchorHighlighting(currentEntry.target.id, isVisible);
          }
      }

        function toggleAnchorHighlighting(elementId, shouldHighlight) {
            let anchorsToHighlight = pageNav.querySelectorAll('a[href="#' + elementId + '"]');
            for (let i = 0; i < anchorsToHighlight.length; i++) {
                // Change below to use classList.toggle when IE support is dropped.
                if (shouldHighlight) {
                    anchorsToHighlight[i].classList.add('current-heading');
                } else {
                    anchorsToHighlight[i].classList.remove('current-heading');
                }
            }
        }
    }
};

module.exports = setupPageShow;