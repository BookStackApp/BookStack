
<div class="toggle-switch-list dual-column-content">
    <input type="hidden" name="{{ $name }}[0]" value="0">
    @foreach($roles as $role)
        <div>
            @include('form.custom-checkbox', [
                'name' => $name . '[' . strval($role->id) . ']',
                'label' => $role->display_name,
                'value' => $role->id,
                'checked' => old($name . '.' . strval($role->id)) || (!old('name') && isset($model) && $model->hasRole($role->id))
            ])
        </div>
    @endforeach
</div>

@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif