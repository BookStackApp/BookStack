
jQuery.fn.smoothScrollTo = function() {
    if(this.length === 0) return;
    $('body').animate({
        scrollTop: this.offset().top - 60 // Adjust to change final scroll position top margin
    }, 800); // Adjust to change animations speed (ms)
    return this;
};
$.expr[":"].contains = $.expr.createPseudo(function(arg) {
    return function( elem ) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

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

jQuery.fn.submitForm = function() {
    $(this).closest('form').submit();
};