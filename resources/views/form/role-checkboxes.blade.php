
@foreach($roles as $role)
    <label>
        <input value="{{ $role->id }}" id="{{$name}}-{{$role->name}}" type="checkbox" name="{{$name}}[{{$role->name}}]"
               @if($errors->has($name)) class="neg" @endif
               @if(old($name . '.' . $role->name) || (!old('name') && isset($model) && $model->hasRole($role->name))) checked="checked" @endif
        >
        {{ $role->display_name }}
    </label>
@endforeach

@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif