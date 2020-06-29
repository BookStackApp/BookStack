{{--
@type - Type of term (exact, tag)
@currentList
--}}
<table component="add-remove-rows"
       option:add-remove-rows:remove-selector="button.text-neg"
       option:add-remove-rows:row-selector="tr"
       class="no-style">
    @foreach(array_merge($currentList, ['']) as $term)
        <tr @if(empty($term)) class="hidden" refs="add-remove-rows@model" @endif>
            <td class="pb-s pr-m">
                <input class="exact-input outline" type="text" name="{{$type}}[]" value="{{ $term }}">
            </td>
            <td>
                <button type="button" class="text-neg text-button">@icon('close')</button>
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2">
            <button refs="add-remove-rows@add" type="button" class="text-button">
                @icon('add-circle'){{ trans('common.add') }}
            </button>
        </td>
    </tr>
</table>