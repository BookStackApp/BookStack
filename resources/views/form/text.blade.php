<input type="text" id="{{ $name }}" name="{{ $name }}"
       @if($errors->has($name)) class="text-neg" @endif
       @if(isset($placeholder)) placeholder="{{$placeholder}}" @endif
       @if(isset($disabled) && $disabled) disabled="disabled" @endif
       @if(isset($tabindex)) tabindex="{{$tabindex}}" @endif
       @if(isset($model) || old($name)) value="{{ old($name) ? old($name) : $model->$name}}" @endif>
@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif