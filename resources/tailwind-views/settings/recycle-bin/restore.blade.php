@extends('layouts.simple')

@section('body')
    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'maintenance'])

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.recycle_bin_restore') }}</h2>
            <p class="text-muted">{{ trans('settings.recycle_bin_restore_confirm') }}</p>
            <form action="{{ $deletion->getUrl('/restore') }}" method="post">
                {!! csrf_field() !!}
                <a href="{{ url('/settings/recycle-bin') }}" class="button outline">{{ trans('common.cancel') }}</a>
                <button type="submit" class="button">{{ trans('settings.recycle_bin_restore') }}</button>
            </form>

            @if($deletion->deletable instanceof \BookStack\Entities\Models\Entity)
                <hr class="mt-m">
                <h5>{{ trans('settings.recycle_bin_restore_list') }}</h5>
                <div class="flex-container-row mb-s items-center">
                    @if($deletion->deletable->getParent() && $deletion->deletable->getParent()->trashed())
                        <div class="text-neg flex">{{ trans('settings.recycle_bin_restore_deleted_parent') }}</div>
                    @endif
                    @if($parentDeletion)
                        <div class="flex fit-content ml-m">
                            <a class="button outline" href="{{ $parentDeletion->getUrl('/restore') }}">{{ trans('settings.recycle_bin_restore_parent') }}</a>
                        </div>
                    @endif
                </div>

                @include('settings.recycle-bin.parts.deletable-entity-list', ['entity' => $deletion->deletable])
            @endif

        </div>

    </div>
@stop
