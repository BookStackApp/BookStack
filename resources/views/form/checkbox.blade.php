
<label>
    <input value="true" id="{{$name}}" type="checkbox" name="{{$name}}"
           @if($errors->has($name)) class="neg" @endif
           @if(old($name) || (!old() && isset($model) && $model->$name)) checked="checked" @endif
    >
    {{ $label }}
</label>

@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif