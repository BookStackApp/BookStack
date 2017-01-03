<form action="{{$url}}" method="POST" class="inline">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit" class="button neg">{{ isset($text) ? $text : trans('common.delete') }}</button>
</form>