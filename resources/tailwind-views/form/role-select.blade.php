
<select id="{{ $name }}" name="{{ $name }}">
    @foreach($options as $option)
        <option value="{{$option->id}}"
                @if($errors->has($name)) class="text-neg" @endif
                @if(isset($model) || old($name)) @if(old($name) && old($name) === $option->id) selected @elseif(isset($model) && $model->role->id === $option->id) selected @endif @endif
                >
            {{ $option->display_name }}
        </option>
    @endforeach
</select>

@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif