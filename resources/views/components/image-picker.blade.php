<div class="image-picker" image-picker="{{$name}}" data-default-image="{{ $defaultImage }}" data-resize-height="{{ $resizeHeight }}" data-resize-width="{{ $resizeWidth }}" data-current-id="{{ $currentId or '' }}" data-resize-crop="{{ $resizeCrop or '' }}">

    <div>
        <img @if($currentImage && $currentImage !== 'none') src="{{$currentImage}}" @else src="{{$defaultImage}}" @endif  class="{{$imageClass}} @if($currentImage=== 'none') none @endif" alt="{{ trans('components.image_preview') }}">
    </div>

    <button class="button" type="button" data-action="show-image-manager">{{ trans('components.image_select_image') }}</button>
    <br>
    <button class="text-button" data-action="reset-image" type="button">{{ trans('common.reset') }}</button>

    @if ($showRemove)
        <span class="sep">|</span>
        <button class="text-button neg" data-action="remove-image" type="button">{{ trans('common.remove') }}</button>
    @endif

    <input type="hidden" name="{{$name}}" id="{{$name}}" value="{{ isset($currentId) && ($currentId !== 0 && $currentId !== false) ? $currentId : $currentImage}}">
</div>

<script>
    (function(){

        var picker = document.querySelector('[image-picker="{{$name}}"]');
        picker.addEventListener('click', function(event) {
            if (event.target.nodeName.toLowerCase() !== 'button') return;
             var button = event.target;
             var action = button.getAttribute('data-action');
             var resize = picker.getAttribute('data-resize-height') && picker.getAttribute('data-resize-width');
             var usingIds = picker.getAttribute('data-current-id') !== '';
             var resizeCrop = picker.getAttribute('data-resize-crop') !== '';
             var imageElem = picker.querySelector('img');
             var input = picker.querySelector('input');

             function setImage(image) {
                 if (image === 'none') {
                     imageElem.src = picker.getAttribute('data-default-image');
                     imageElem.classList.add('none');
                     input.value = 'none';
                     return;
                 }
                 imageElem.src = image.url;
                 input.value = usingIds ? image.id : image.url;
                 imageElem.classList.remove('none');
             }

             if (action === 'show-image-manager') {
                 window.ImageManager.show((image) => {
                     if (!resize) {
                         setImage(image);
                         return;
                     }
                     var requestString = '/images/thumb/' + image.id + '/' + picker.getAttribute('data-resize-width') + '/' + picker.getAttribute('data-resize-height') + '/' + (resizeCrop ? 'true' : 'false');
                     $.get(window.baseUrl(requestString), resp => {
                         image.url = resp.url;
                         setImage(image);
                     });
                 });
             } else if (action === 'reset-image') {
                 setImage({id: 0, url: picker.getAttribute('data-default-image')});
             } else if (action === 'remove-image') {
                 setImage('none');
             }

            });

    })();
</script>