<input type="date" id="{{ $name }}" name="{{ $name }}"
       @if($errors->has($name)) class="text-neg" @endif
       placeholder="{{ $placeholder ?? 'YYYY-MM-DD' }}"
       @if($autofocus ?? false) autofocus @endif
       @if($disabled ?? false) disabled="disabled" @endif
       @if(isset($model) || old($name)) value="{{ old($name) ?? $model->$name->format('Y-m-d') ?? ''}}" @endif>
@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif
