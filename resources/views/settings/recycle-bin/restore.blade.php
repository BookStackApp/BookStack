@extends('simple-layout')

@section('body')
    <div class="container small">

        <div class="py-m">
            @include('settings.navbar', ['selected' => 'maintenance'])
        </div>

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.recycle_bin_restore') }}</h2>
            <p class="text-muted">{{ trans('settings.recycle_bin_restore_confirm') }}</p>
            <form action="{{ url('/settings/recycle-bin/' . $deletion->id . '/restore') }}" method="post">
                {!! csrf_field() !!}
                <a href="{{ url('/settings/recycle-bin') }}" class="button outline">{{ trans('common.cancel') }}</a>
                <button type="submit" class="button">{{ trans('settings.recycle_bin_restore') }}</button>
            </form>

            @if($deletion->deletable instanceof \BookStack\Entities\Models\Entity)
                <hr class="mt-m">
                <h5>{{ trans('settings.recycle_bin_restore_list') }}</h5>
                @if($deletion->deletable->getParent() && $deletion->deletable->getParent()->trashed())
                    <p class="text-neg">{{ trans('settings.recycle_bin_restore_deleted_parent') }}</p>
                @endif
                @include('settings.recycle-bin.deletable-entity-list', ['entity' => $deletion->deletable])
            @endif

        </div>

    </div>
@stop
