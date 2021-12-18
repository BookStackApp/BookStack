@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif