<input type="password" id="{{ $name }}" name="{{ $name }}"
       @if($errors->has($name)) class="text-neg" @endif
       @if(isset($placeholder)) placeholder="{{$placeholder}}" @endif
       @if(isset($tabindex)) tabindex="{{$tabindex}}" @endif
       @if(old($name)) value="{{ old($name)}}" @endif>
@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif