<form action="{{ $model->getUrl() }}/restrict" method="POST">
    {!! csrf_field() !!}
    <input type="hidden" name="_method" value="PUT">

    <div class="form-group">
        @include('form/checkbox', ['name' => 'restricted', 'label' => 'Restrict this ' . $model->getClassName()])
    </div>

    <table class="table">
        <tr>
            <th>Role</th>
            <th @if($model->isA('page')) colspan="3" @else colspan="4" @endif>Actions</th>
        </tr>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->display_name }}</td>
                <td>@include('form/restriction-checkbox', ['name'=>'restrictions', 'label' => 'View', 'action' => 'view'])</td>
                @if(!$model->isA('page'))
                    <td>@include('form/restriction-checkbox', ['name'=>'restrictions', 'label' => 'Create', 'action' => 'create'])</td>
                @endif
                <td>@include('form/restriction-checkbox', ['name'=>'restrictions', 'label' => 'Update', 'action' => 'update'])</td>
                <td>@include('form/restriction-checkbox', ['name'=>'restrictions', 'label' => 'Delete', 'action' => 'delete'])</td>
            </tr>
        @endforeach
    </table>

    <a href="{{ $model->getUrl() }}" class="button muted">Cancel</a>
    <button type="submit" class="button pos">Save Restrictions</button>
</form>