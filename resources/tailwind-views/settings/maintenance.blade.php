@extends('layouts.simple')

@section('body')
<div class="container small">

    @include('settings.parts.navbar', ['selected' => 'maintenance'])

    <div class="card content-wrap auto-height pb-xl">
        <h2 class="list-heading">{{ trans('settings.recycle_bin') }}</h2>
        <div class="grid half gap-xl">
            <div>
                <p class="small text-muted">{{ trans('settings.maint_recycle_bin_desc') }}</p>
            </div>
            <div>
                <div class="grid half no-gap mb-m">
                    <p class="mb-xs text-bookshelf">@icon('bookshelf'){{ trans('entities.shelves') }}: {{ $recycleStats['bookshelf'] }}</p>
                    <p class="mb-xs text-book">@icon('book'){{ trans('entities.books') }}: {{ $recycleStats['book'] }}</p>
                    <p class="mb-xs text-chapter">@icon('chapter'){{ trans('entities.chapters') }}: {{ $recycleStats['chapter'] }}</p>
                    <p class="mb-xs text-page">@icon('page'){{ trans('entities.pages') }}: {{ $recycleStats['page'] }}</p>
                </div>
                <a href="{{ url('/settings/recycle-bin') }}" class="button outline">{{ trans('settings.maint_recycle_bin_open') }}</a>
            </div>
        </div>
    </div>

    <div id="image-cleanup" class="card content-wrap auto-height">
        <h2 class="list-heading">{{ trans('settings.maint_image_cleanup') }}</h2>
        <div class="grid left-focus gap-xl">
            <div>
                <p class="small text-muted">{{ trans('settings.maint_image_cleanup_desc') }}</p>
                <p class="small text-muted italic">{{ trans('settings.maint_timeout_command_note') }}</p>
            </div>
            <div>
                <form method="POST" action="{{ url('/settings/maintenance/cleanup-images') }}">
                    {!! csrf_field()  !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="mb-s">
                        @if(session()->has('cleanup-images-warning'))
                            <p class="text-neg">
                                {{ session()->get('cleanup-images-warning') }}
                            </p>
                            <input type="hidden" name="ignore_revisions" value="{{ session()->getOldInput('ignore_revisions', 'false') }}">
                            <input type="hidden" name="confirm" value="true">
                        @else
                            <label class="flex-container-row">
                                <div class="mr-s"><input type="checkbox" name="ignore_revisions" value="true"></div>
                                <div>{{ trans('settings.maint_delete_images_only_in_revisions') }}</div>
                            </label>
                        @endif
                    </div>
                    <button class="button outline">{{ trans('settings.maint_image_cleanup_run') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div id="send-test-email" class="card content-wrap auto-height">
        <h2 class="list-heading">{{ trans('settings.maint_send_test_email') }}</h2>
        <div class="grid left-focus gap-xl">
            <div>
                <p class="small text-muted">{{ trans('settings.maint_send_test_email_desc') }}</p>
            </div>
            <div>
                <form method="POST" action="{{ url('/settings/maintenance/send-test-email') }}">
                    {!! csrf_field()  !!}
                    <button class="button outline">{{ trans('settings.maint_send_test_email_run') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div id="regenerate-references" class="card content-wrap auto-height">
        <h2 class="list-heading">{{ trans('settings.maint_regen_references') }}</h2>
        <div class="grid left-focus gap-xl">
            <div>
                <p class="small text-muted">{{ trans('settings.maint_regen_references_desc') }}</p>
                <p class="small text-muted italic">{{ trans('settings.maint_timeout_command_note') }}</p>
            </div>
            <div>
                <form method="POST" action="{{ url('/settings/maintenance/regenerate-references') }}">
                    {!! csrf_field()  !!}
                    <button class="button outline">{{ trans('settings.maint_regen_references') }}</button>
                </form>
            </div>
        </div>
    </div>

</div>
@stop
