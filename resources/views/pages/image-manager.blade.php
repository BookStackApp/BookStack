<section class="overlay" style="display:none;">
    <div id="image-manager">
        <div class="image-manager-left">
            <div class="image-manager-header">
                <button type="button" class="button neg float right" data-action="close">Close</button>
                <div class="image-manager-title">Image Library</div>
            </div>
            <div class="image-manager-display">
            </div>
            <form action="/upload/image" class="image-manager-dropzone">
                {{ csrf_field() }}
                Drag images or click here to upload
            </form>
        </div>
        {{--<div class="sidebar">--}}

        {{--</div>--}}
    </div>
</section>