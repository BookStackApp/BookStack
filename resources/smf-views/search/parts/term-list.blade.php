{{--
@type - Type of term (exact, tag)
@currentList
--}}
<div component="add-remove-rows"
       option:add-remove-rows:remove-selector="button.text-neg"
       option:add-remove-rows:row-selector=".flex-container-row"
        class="flex-container-column gap-xs">
    @foreach(array_merge($currentList, ['']) as $term)
        <div @if(empty($term)) refs="add-remove-rows@model" @endif
            class="{{ $term ? '' : 'hidden' }} flex-container-row items-center gap-x-xs">
            <div>
                <input class="exact-input outline" type="text" name="{{$type}}[]" value="{{ $term }}">
            </div>
            <div>
                <button type="button" class="text-neg text-button icon-button p-xs">@icon('close')</button>
            </div>
        </div>
    @endforeach
    <div class="flex py-xs">
        <button refs="add-remove-rows@add" type="button" class="text-button">
            @icon('add-circle'){{ trans('common.add') }}
        </button>
    </div>
</div>