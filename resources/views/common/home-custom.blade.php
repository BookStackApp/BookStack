@extends('simple-layout')

@section('body')
    <div class="container mt-l">
        <div class="grid right-focus gap-xl">
            <div>

                <div class="actions mb-xl">
                    <h5>{{ trans('common.actions') }}</h5>
                    <div class="icon-list text-primary">
                        <a expand-toggle=".entity-list.compact .entity-item-snippet" class="icon-list-item">
                            <span>@icon('expand-text')</span>
                            <span>{{ trans('common.toggle_details') }}</span>
                        </a>
                    </div>
                </div>

                @include('common.home-sidebar')
            </div>
            <div>
                <div class="content-wrap card">
                    <div class="page-content" page-display="{{ $customHomepage->id }}">
                        @include('pages.page-display', ['page' => $customHomepage])
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop