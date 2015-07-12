<form action="{{$url}}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit" class="button neg">{{ isset($text) ? $text : 'Delete' }}</button>
</form>