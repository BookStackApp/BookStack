{{--
@checked - If the option should be pre-checked
@entity - Entity Name
@transKey - Translation Key
--}}
<label class="inline checkbox text-{{$entity}}">
    <input type="checkbox" name="types[]"
           @if($checked) checked @endif
           value="{{$entity}}">{{ trans('entities.' . $transKey) }}
</label>