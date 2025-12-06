<input type="number" id="{{ $name }}" name="{{ $name }}"
       @if($errors->has($name)) class="text-neg" @endif
       @if(isset($placeholder)) placeholder="{{$placeholder}}" @endif
       @if($autofocus ?? false) autofocus @endif
       @if($disabled ?? false) disabled="disabled" @endif
       @if($readonly ?? false) readonly="readonly" @endif
       @if($min ?? false) min="{{ $min }}" @endif
       @if($max ?? false) max="{{ $max }}" @endif
       @if($step ?? false) step="{{ $step }}" @endif
       @if(isset($model) || old($name) || isset($value)) value="{{ old($name) ?? $model->$name ?? $value }}" @endif>
@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif
