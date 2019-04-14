<a href="{{$shelf->getUrl()}}" class="bookshelf-grid-item grid-card"
   data-entity-type="bookshelf" data-entity-id="{{$shelf->id}}">
    <div class="bg-shelf featured-image-container-wrap">
        <div class="featured-image-container" @if($shelf->cover) style="background-image: url('{{ $shelf->getBookCover() }}')"@endif>
        </div>
        @icon('bookshelf')
    </div>
    <div class="grid-card-content">
        <h2>{{$shelf->getShortName(35)}}</h2>
        @if(isset($shelf->searchSnippet))
            <p class="text-muted">{!! $shelf->searchSnippet !!}</p>
        @else
            <p class="text-muted">{{ $shelf->getExcerpt(130) }}</p>
        @endif
    </div>
    <div class="grid-card-footer text-muted text-small">
        @icon('star')<span title="{{$shelf->created_at->toDayDateTimeString()}}">{{ trans('entities.meta_created', ['timeLength' => $shelf->created_at->diffForHumans()]) }}</span>
        <br>
        @icon('edit')<span title="{{ $shelf->updated_at->toDayDateTimeString() }}">{{ trans('entities.meta_updated', ['timeLength' => $shelf->updated_at->diffForHumans()]) }}</span>
    </div>
</a>