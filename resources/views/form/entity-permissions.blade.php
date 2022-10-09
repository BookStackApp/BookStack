<form component="entity-permissions" action="{{ $model->getUrl('/permissions') }}" method="POST">
    {!! csrf_field() !!}
    <input type="hidden" name="_method" value="PUT">

    <div class="grid half left-focus v-center">
        <div>
            <p class="mb-none mt-m">{{ trans('entities.permissions_intro') }}</p>
        </div>
        <div>
            <div class="form-group">
                <label for="owner">{{ trans('entities.permissions_owner') }}</label>
                @include('form.user-select', ['user' => $model->ownedBy, 'name' => 'owned_by'])
            </div>
        </div>
    </div>

    @if($model instanceof \BookStack\Entities\Models\Bookshelf)
        <p class="text-warn">{{ trans('entities.shelves_permissions_cascade_warning') }}</p>
    @endif

    <div class="content-permissions mt-m mb-xl">
        @foreach($data->rolesWithPermissions() as $role)
            @include('form.entity-permissions-row', ['role' => $role, 'model' => $model])
        @endforeach
    </div>

    <div class="content-permissions mt-m mb-xl">
        @include('form.entity-permissions-row', ['role' => $data->everyoneElseRole(), 'model' => $model])
    </div>

    <div class="text-right">
        <a href="{{ $model->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
        <button type="submit" class="button">{{ trans('entities.permissions_save') }}</button>
    </div>
</form>