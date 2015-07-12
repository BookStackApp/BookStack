<textarea id="{{ $name }}" name="{{ $name }}"
          @if($errors->has($name)) class="neg" @endif>@if(isset($model) || old($name)){{ old($name) ? old($name) : $model->$name}}@endif</textarea>
@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif