<div components="tag-manager add-remove-rows"
     option:add-remove-rows:row-selector=".card"
     option:add-remove-rows:remove-selector="button.text-neg"
     option:tag-manager:row-selector=".card:not(.hidden)"
     refs="tag-manager@add-remove"
     class="tags">

    <p class="text-muted small">
        {!! nl2br(e(trans('entities.tags_explain'))) !!} <br>
        <a href="{{ url('/tags') }}" target="_blank">{{ trans('entities.tags_view_existing_tags') }}</a>.
    </p>

    <div component="sortable-list"
         option:sortable-list:handle-selector=".handle">
        @include('entities.tag-manager-list', ['tags' => $entity ? $entity->tags->all() : []])
    </div>

    <button refs="add-remove-rows@add" type="button" class="text-button">{{ trans('entities.tags_add') }}</button>
</div>