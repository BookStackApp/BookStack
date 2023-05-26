@foreach($images as $index => $image)
<div>
    <button component="event-emit-select"
         option:event-emit-select:name="image"
         option:event-emit-select:data="{{ json_encode($image) }}"
         class="image anim fadeIn text-link"
         style="animation-delay: {{ $index > 26 ? '160ms' : ($index * 25) . 'ms' }};">
        <img src="{{ $image->thumbs['gallery'] }}"
             alt="{{ $image->name }}"
             width="150"
             height="150"
             loading="lazy"
             title="{{ $image->name }}">
        <div class="image-meta">
            <span class="name">{{ $image->name }}</span>
            <span class="date">{{ trans('components.image_uploaded', ['uploadedDate' => $image->created_at->format('Y-m-d')]) }}</span>
        </div>
    </button>
</div>
@endforeach
@if(count($images) === 0)
    <p class="m-m text-bigger italic text-muted">{{ trans('common.no_items') }}</p>
@endif
@if($hasMore)
    <div class="load-more">
        <button type="button" class="button small outline">{{ trans('components.image_load_more') }}</button>
    </div>
@endif