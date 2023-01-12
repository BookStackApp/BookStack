
@include('form.custom-checkbox', [
       'name' => 'permissions[' . $permission . ']',
       'value' => 'true',
       'checked' => old('permissions'.$permission, false)|| (!old('display_name', false) && (isset($role) && $role->hasPermission($permission))),
       'label' => $label
])