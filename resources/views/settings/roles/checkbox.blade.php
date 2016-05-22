<input type="checkbox" name="permissions[{{ $permission }}]"
       @if(old('permissions'.$permission, false)|| (!old('display_name', false) && (isset($role) && $role->hasPermission($permission)))) checked="checked" @endif
       value="true">