<section class="overlay" style="display:none;">
{{--<section class="overlay">--}}
    <div id="image-manager">
        <div class="image-manager-left">
            <div class="image-manager-header">
                <button type="button" class="button neg float right" data-action="close">Close</button>
                <div class="image-manager-title">Image Library</div>
            </div>
            <div class="image-manager-display-wrap">
                <div class="image-manager-display">
                    <div class="uploads"></div>
                    <div class="images">
                        <div class="load-more">Load More</div>
                    </div>
                </div>
            </div>
            <form action="/upload/image"
                  class="dropzone"
                  id="image-upload-dropzone">
                {!! csrf_field() !!}
            </form>
        </div>
        {{--<div class="sidebar">--}}

        {{--</div>--}}
    </div>
</section>