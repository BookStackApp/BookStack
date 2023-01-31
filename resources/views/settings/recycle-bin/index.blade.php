@extends('layouts.simple')

@section('body')
    <div class="container">

        @include('settings.parts.navbar', ['selected' => 'maintenance'])

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.recycle_bin') }}</h2>

            <div class="flex-container-row items-center gap-x-l gap-y-m wrap">
                <div class="flex-2 min-width-l">
                    <p class="text-muted mb-none">{{ trans('settings.recycle_bin_desc') }}</p>
                </div>
                <div class="flex text-m-right min-width-m">
                    <div component="dropdown" class="dropdown-container">
                        <button refs="dropdown@toggle"
                                type="button"
                                class="button outline">{{ trans('settings.recycle_bin_empty') }} </button>
                        <div refs="dropdown@menu" class="dropdown-menu">
                            <p class="text-neg small px-m mb-xs">{{ trans('settings.recycle_bin_empty_confirm') }}</p>

                            <form action="{{ url('/settings/recycle-bin/empty') }}" method="POST">
                                {!! csrf_field() !!}
                                <button type="submit" class="text-link small delete text-item">{{ trans('common.confirm') }}</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <hr class="mt-l mb-s">

            <div class="py-m">
                {!! $deletions->links() !!}
            </div>

            <div class="item-list">
                <div class="item-list-row flex-container-row items-center px-s bold hide-under-l">
                    <div class="flex-2 px-m py-xs">{{ trans('settings.audit_deleted_item') }}</div>
                    <div class="flex-2 px-m py-xs">{{ trans('settings.recycle_bin_deleted_parent') }}</div>
                    <div class="flex-2 px-m py-xs">{{ trans('settings.recycle_bin_deleted_by') }}</div>
                    <div class="flex px-m py-xs">{{ trans('settings.recycle_bin_deleted_at') }}</div>
                    <div class="flex px-m py-xs text-right"></div>
                </div>
                @if(count($deletions) === 0)
                    <div class="item-list-row px-l py-m">
                        <p class="text-muted mb-none"><em>{{ trans('settings.recycle_bin_contents_empty') }}</em></p>
                    </div>
                @endif
                @foreach($deletions as $deletion)
                    @include('settings.recycle-bin.parts.recycle-bin-list-item', ['deletion' => $deletion])
                @endforeach
            </div>

            <div class="py-m">
                {!! $deletions->links() !!}
            </div>

        </div>

    </div>
@stop
