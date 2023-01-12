@extends('layouts.simple')

@section('body')
    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'maintenance'])

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.recycle_bin_permanently_delete') }}</h2>
            <p class="text-muted">{{ trans('settings.recycle_bin_destroy_confirm') }}</p>
            <form action="{{ url('/settings/recycle-bin/' . $deletion->id) }}" method="post">
                {!! method_field('DELETE') !!}
                {!! csrf_field() !!}
                <a href="{{ url('/settings/recycle-bin') }}" class="button outline">{{ trans('common.cancel') }}</a>
                <button type="submit" class="button">{{ trans('common.delete_confirm') }}</button>
            </form>

            @if($deletion->deletable instanceof \BookStack\Entities\Models\Entity)
                <hr class="mt-m">
                <h5>{{ trans('settings.recycle_bin_destroy_list') }}</h5>
                @include('settings.recycle-bin.parts.deletable-entity-list', ['entity' => $deletion->deletable])
            @endif

        </div>

    </div>
@stop
