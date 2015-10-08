
// Smooth scrolling
jQuery.fn.smoothScrollTo = function() {
    if(this.length === 0) return;
    $('body').animate({
        scrollTop: this.offset().top - 60 // Adjust to change final scroll position top margin
    }, 800); // Adjust to change animations speed (ms)
    return this;
};

// Making contains text expression not worry about casing
$.expr[":"].contains = $.expr.createPseudo(function(arg) {
    return function( elem ) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

// Show a success message after the element it's called upon.
jQuery.fn.showSuccess = function (message) {
    var elem = $(this);
    var success = $('<div class="text-pos" style="display:none;"><i class="zmdi zmdi-check-circle"></i>' + message + '</div>');
    elem.after(success);
    success.slideDown(400, function () {
        setTimeout(function () {
            success.slideUp(400, function () {
                success.remove();
            })
        }, 2000);
    });
};

// Show a failure messages from laravel. Searches for the name of the inputs.
jQuery.fn.showFailure = function (messageMap) {
    var elem = $(this);
    $.each(messageMap, function (key, messages) {
        var input = elem.find('[name="' + key + '"]').last();
        var fail = $('<div class="text-neg" style="display:none;"><i class="zmdi zmdi-alert-circle"></i>' + messages.join("\n") + '</div>');
        input.after(fail);
        fail.slideDown(400, function () {
            setTimeout(function () {
                fail.slideUp(400, function () {
                    fail.remove();
                })
            }, 2000);
        });
    });

};

// Submit the form that the called upon element sits in.
jQuery.fn.submitForm = function() {
    $(this).closest('form').submit();
};

// Dropdown menu display
jQuery.fn.dropDown = function() {
    var container = $(this),
        menu = container.find('ul');
        container.find('[data-dropdown-toggle]').on('click', function() {
        menu.show().addClass('anim menuIn');
        container.mouseleave(function() {
            menu.hide();
            menu.removeClass('anim menuIn');
        });
    });
};