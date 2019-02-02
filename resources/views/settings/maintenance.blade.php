@extends('simple-layout')

@section('body')
<div class="container small">

    <div class="grid left-focus v-center">
        <div class="py-m">
            @include('settings/navbar', ['selected' => 'maintenance'])
        </div>
        <div class="text-right mb-l px-m">
            <br>
            BookStack @if(strpos($version, 'v') !== 0) version @endif {{ $version }}
        </div>
    </div>


    <div id="image-cleanup" class="card content-wrap auto-height">
        <h2 class="list-heading">{{ trans('settings.maint_image_cleanup') }}</h2>
        <div class="grid half large-gap">
            <div>
                <p class="small muted">{{ trans('settings.maint_image_cleanup_desc') }}</p>
            </div>
            <div>
                <form method="POST" action="{{ baseUrl('/settings/maintenance/cleanup-images') }}">
                    {!! csrf_field()  !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <div>
                        @if(session()->has('cleanup-images-warning'))
                            <p class="text neg">
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

</div>
@stop
