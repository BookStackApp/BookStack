{{--TODO - Make custom--}}
<label>
    <input value="true" id="{{$name}}[{{$role->id}}][{{$action}}]" type="checkbox" name="{{$name}}[{{$role->id}}][{{$action}}]"
           @if(isset($model) && $model->hasRestriction($role->id, $action)) checked="checked" @endif>
    {{ $label }}
</label>