@foreach(array_merge($tags, [new \BookStack\Actions\Tag]) as $index => $tag)
    <div class="card drag-card">
        <div class="handle">@icon('grip')</div>
        @foreach(['name', 'value'] as $type)
            <div component="auto-suggest"
                 option:auto-suggest:url="{{ url('/ajax/tags/suggest/' . $type . 's') }}"
                 option:auto-suggest:type="{{ $type }}"
                 class="outline">
                <input value="{{ $tag->$type ?? '' }}"
                       placeholder="{{ trans('entities.tag_' . $type) }}"
                       aria-label="{{ trans('entities.tag_' . $type) }}"
                       name="tags[{{ $index }}][{{ $type }}]"
                       type="text"
                       refs="auto-suggest@input"
                       autocomplete="off"/>
                <ul refs="auto-suggest@list" class="suggestion-box dropdown-menu"></ul>
            </div>
        @endforeach
        <button refs="tag-manager@remove" type="button"
                aria-label="{{ trans('entities.tags_remove') }}"
                class="text-center drag-card-action text-neg {{ count($tags) > 0 ? '' : 'hidden' }}">
            @icon('close')
        </button>
    </div>
@endforeach