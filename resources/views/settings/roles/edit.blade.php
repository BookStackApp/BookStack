@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'roles'])

    <div class="container">
        <h1>Edit Role <small> {{ $role->display_name }}</small></h1>

        <form action="">
            <div class="row">

                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th></th>
                            <th>Create</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        <tr>
                            <td>Books</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Chapters</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Pages</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Images</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Can only edit own content</label>
                        <hr class="even">
                        <label for="">Manage users</label>
                        <hr class="even">
                        <label for="">Manage user roles</label>
                        <hr class="even">
                        <label for="">Manage app settings</label>
                    </div>
                </div>

            </div>
            <button type="submit" class="button pos">Save Role</button>
        </form>
    </div>

@stop
