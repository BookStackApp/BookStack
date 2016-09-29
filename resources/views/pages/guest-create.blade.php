@extends('base')

@section('content')

    <div class="container small" ng-non-bindable>
        <h1>Create Page</h1>
        <form action="{{  $parent->getUrl('/page/create/guest') }}" method="POST">

            {!! csrf_field() !!}

            <div class="form-group title-input">
                <label for="name">Page Name</label>
                @include('form/text', ['name' => 'name'])
            </div>

            <div class="form-group">
                <a href="{{ $parent->getUrl() }}" class="button muted">Cancel</a>
                <button type="submit" class="button pos">Continue</button>
            </div>

        </form>
    </div>


@stop