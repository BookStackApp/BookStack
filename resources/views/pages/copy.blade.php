@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('pages._breadcrumbs', ['page' => $page])
    </div>
@stop

@section('body')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('copy') {{ trans('entities.pages_copy') }}</h3>
            <div class="body">
                <form action="{{ $page->getUrl('/copy') }}" method="POST">
                    {!! csrf_field() !!}

                    <div class="form-group title-input">
                        <label for="name">{{ trans('common.name') }}</label>
                        @include('form/text', ['name' => 'name'])
                    </div>

                    <div class="form-group" collapsible>
                        <div class="collapse-title text-primary" collapsible-trigger>
                            <label for="entity_selection">{{ trans('entities.pages_copy_desination') }}</label>
                        </div>
                        <div class="collapse-content" collapsible-content>
                            @include('components.entity-selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book,chapter', 'entityPermission' => 'page-create'])
                        </div>
                    </div>


                    <div class="form-group text-right">
                        <a href="{{ $page->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button pos">{{ trans('entities.pages_copy') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop
