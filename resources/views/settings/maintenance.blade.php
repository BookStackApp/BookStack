@extends('simple-layout')

@section('body')
<div class="container small">

    <div class="grid left-focus v-center no-row-gap">
        <div class="py-m">
            @include('settings.navbar', ['selected' => 'maintenance'])
        </div>
        <div class="text-right p-m">
            <a target="_blank" rel="noopener noreferrer" href="https://github.com/BookStackApp/BookStack/releases">
            BookStack @if(strpos($version, 'v') !== 0) version @endif {{ $version }}
            </a>
        </div>
    </div>

    <div id="image-cleanup" class="card content-wrap auto-height">
        <h2 class="list-heading">{{ trans('settings.maint_image_cleanup') }}</h2>
        <div class="grid half gap-xl">
            <div>
                <p class="small text-muted">{{ trans('settings.maint_image_cleanup_desc') }}</p>
            </div>
            <div>
                <form method="POST" action="{{ url('/settings/maintenance/cleanup-images') }}">
                    {!! csrf_field()  !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <div>
                        @if(session()->has('cleanup-images-warning'))
                            <p class="text-neg">
                                {{ session()->get('cleanup-images-warning') }}
                            </p>
                            <input type="hidden" name="ignore_revisions" value="{{ session()->getOldInput('ignore_revisions', 'false') }}">
                            <input type="hidden" name="confirm" value="true">
                        @else
                            <label>
                                <input type="checkbox" name="ignore_revisions" value="true">
                                {{ trans('settings.maint_image_cleanup_ignore_revisions') }}
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
        <div class="grid half gap-xl">
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

</div>
@stop
