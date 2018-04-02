import jQuery from "jquery"

jQuery( document ).ready((e) => {
    const $ = jQuery;
    
    $('.ch-panel').each((index, self) => {
        const panelHeight = $(self).height();
        
        const $topBlock = $('<div class="ch-panel-top" />');
        const $botBlock = $('<div class="ch-panel-bot" />');
        const $moreButton = $('<button class="ch-panel-more" />');

        let linkHeights = 0;
        let moreCount = 0;

        $(self).find('a').each((index, self) => {
            linkHeights += $(self)[0].offsetHeight;
            
            if (linkHeights <= panelHeight) {
                $(self).appendTo($topBlock);
            } else {
                moreCount++;
                $(self).appendTo($botBlock);
            }

        });
        $topBlock.appendTo($(self));
        if ($botBlock.html() !== '') {
            $moreButton.text(`Еще ${moreCount}`);
            $botBlock.prepend($moreButton).appendTo($(self));
        }
    });

    $(document).on('click', '.ch-panel-more', (e) => {
        $('.ch-panel').removeClass('ch-panel--opened');
        $(e.currentTarget).parents('.ch-panel').addClass('ch-panel--opened');
    });
});