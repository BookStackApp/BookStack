<input type="password" autocomplete="off" id="{{ $name }}" name="{{ $name }}"
       @if($errors->has($name)) class="neg" @endif
       @if(isset($placeholder)) placeholder="{{$placeholder}}" @endif
       @if(isset($tabindex)) tabindex="{{$tabindex}}" @endif
       @if(old($name)) value="{{ old($name)}}" @endif>
@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif
