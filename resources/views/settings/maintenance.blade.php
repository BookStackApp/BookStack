@extends('simple-layout')

@section('toolbar')
    @include('settings/navbar', ['selected' => 'maintenance'])
@stop

@section('body')
<div class="container small">

    <div class="text-right text-muted container">
        <br>
        BookStack @if(strpos($version, 'v') !== 0) version @endif {{ $version }}
    </div>


    <div class="card" id="image-cleanup">
        <h3>@icon('images') {{ trans('settings.maint_image_cleanup') }}</h3>
        <div class="body">
            <div class="row">
                <div class="col-sm-6">
                    <p class="small muted">{{ trans('settings.maint_image_cleanup_desc') }}</p>
                </div>
                <div class="col-sm-6">
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

</div>
@stop
