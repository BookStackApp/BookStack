<form action="{{ $model->getUrl('/permissions') }}" method="POST" entity-permissions-editor>
    {!! csrf_field() !!}
    <input type="hidden" name="_method" value="PUT">

    <div class="grid half left-focus v-center">
        <div>
            <p class="mb-none mt-m">{{ trans('entities.permissions_intro') }}</p>
            <div>
                @include('form.checkbox', [
                    'name' => 'restricted',
                    'label' => trans('entities.permissions_enable'),
                ])
            </div>
        </div>
        <div>
            <div class="form-group">
                <label for="owner">{{ trans('entities.permissions_owner') }}</label>
                @include('components.user-select', ['user' => $model->ownedBy, 'name' => 'owned_by', 'compact' => false])
            </div>
        </div>
    </div>

    <hr>

    <table permissions-table class="table permissions-table toggle-switch-list" style="{{ !$model->restricted ? 'display: none' : '' }}">
        <tr>
            <th>{{ trans('common.role') }}</th>
            <th colspan="{{ $model->isA('page') ? '3' : '4'  }}">
                {{ trans('common.actions') }}
                <a href="#" permissions-table-toggle-all class="text-small ml-m text-primary">{{ trans('common.toggle_all') }}</a>
            </th>
        </tr>
        @foreach(\BookStack\Auth\Role::restrictable() as $role)
            <tr>
                <td width="33%" class="pt-m">
                    {{ $role->display_name }}
                    <a href="#" permissions-table-toggle-all-in-row class="text-small float right ml-m text-primary">{{ trans('common.toggle_all') }}</a>
                </td>
                <td>@include('form.restriction-checkbox', ['name'=>'restrictions', 'label' => trans('common.view'), 'action' => 'view'])</td>
                @if(!$model->isA('page'))
                    <td>@include('form.restriction-checkbox', ['name'=>'restrictions', 'label' => trans('common.create'), 'action' => 'create'])</td>
                @endif
                <td>@include('form.restriction-checkbox', ['name'=>'restrictions', 'label' => trans('common.update'), 'action' => 'update'])</td>
                <td>@include('form.restriction-checkbox', ['name'=>'restrictions', 'label' => trans('common.delete'), 'action' => 'delete'])</td>
            </tr>
        @endforeach
    </table>

    <div class="text-right">
        <a href="{{ $model->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
        <button type="submit" class="button">{{ trans('entities.permissions_save') }}</button>
    </div>
</form>