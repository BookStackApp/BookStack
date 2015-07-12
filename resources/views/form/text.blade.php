<input type="text" id="{{ $name }}" name="{{ $name }}"
       @if($errors->has($name)) class="neg" @endif
       @if(isset($model) || old($name)) value="{{ old($name) ? old($name) : $model->$name}}" @endif>
@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif