<form action="{{ $model->getUrl('/permissions') }}" method="POST">
    {!! csrf_field() !!}
    <input type="hidden" name="_method" value="PUT">

    <p class="mb-none">{{ trans('entities.permissions_intro') }}</p>

    <div class="form-group">
        @include('form.checkbox', [
            'name' => 'restricted',
            'label' => trans('entities.permissions_enable'),
        ])
    </div>

    {{--TODO - Add global and role "Select All" options--}}

    <table class="table toggle-switch-list">
        <tr>
            <th>{{ trans('common.role') }}</th>
            <th @if($model->isA('page')) colspan="3" @else colspan="4" @endif>{{ trans('common.actions') }}</th>
        </tr>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->display_name }}</td>
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
        <button type="submit" class="button primary">{{ trans('entities.permissions_save') }}</button>
    </div>
</form>