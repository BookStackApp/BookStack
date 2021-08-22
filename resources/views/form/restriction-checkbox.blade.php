{{--
$name
$label
$role
$action
$model?
--}}
@include('form.custom-checkbox', [
    'name' => $name . '[' . $role->id . '][' . $action . ']',
    'label' => $label,
    'value' => 'true',
    'checked' => isset($model) && $model->hasRestriction($role->id, $action)
])