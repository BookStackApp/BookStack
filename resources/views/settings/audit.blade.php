@extends('layouts.simple')

@section('body')
<div class="container">

    @include('settings.parts.navbar', ['selected' => 'audit'])

    <div class="card content-wrap auto-height">
        <h1 class="list-heading">{{ trans('settings.audit') }}</h1>
        <p class="text-muted">{{ trans('settings.audit_desc') }}</p>

        <form action="{{ url('/settings/audit') }}" method="get" class="flex-container-row wrap justify-flex-start gap-x-m gap-y-xs">

            @foreach(request()->only(['order', 'sort']) as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach

            <div component="dropdown" class="list-sort-type dropdown-container">
                <label for="">{{ trans('settings.audit_event_filter') }}</label>
                <button refs="dropdown@toggle"
                        type="button"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-label="{{ trans('common.sort_options') }}"
                        class="input-base text-left">{{ $filters['event'] ?: trans('settings.audit_event_filter_no_filter') }}</button>
                <ul refs="dropdown@menu" class="dropdown-menu">
                    <li @if($filters['event'] === '') class="active" @endif><a href="{{ sortUrl('/settings/audit', array_filter(request()->except('page')), ['event' => '']) }}" class="text-item">{{ trans('settings.audit_event_filter_no_filter') }}</a></li>
                    @foreach($activityTypes as $type)
                        <li @if($type === $filters['event']) class="active" @endif><a href="{{ sortUrl('/settings/audit', array_filter(request()->except('page')), ['event' => $type]) }}" class="text-item">{{ $type }}</a></li>
                    @endforeach
                </ul>
            </div>

            @if(!empty($filters['event']))
                <input type="hidden" name="event" value="{{ $filters['event'] }}">
            @endif

            @foreach(['date_from', 'date_to'] as $filterKey)
                <div class=>
                    <label for="audit_filter_{{ $filterKey }}">{{ trans('settings.audit_' . $filterKey) }}</label>
                    <input id="audit_filter_{{ $filterKey }}"
                           component="submit-on-change"
                           type="date"
                           name="{{ $filterKey }}"
                           value="{{ $filters[$filterKey] ?? '' }}">
                </div>
            @endforeach

            <div class="form-group"
                 component="submit-on-change"
                 option:submit-on-change:filter='[name="user"]'>
                <label for="owner">{{ trans('settings.audit_table_user') }}</label>
                @include('form.user-select', ['user' => $filters['user'] ? \BookStack\Auth\User::query()->find($filters['user']) : null, 'name' => 'user'])
            </div>


            <div class="form-group">
                <label for="ip">{{ trans('settings.audit_table_ip') }}</label>
                @include('form.text', ['name' => 'ip', 'model' => (object) $filters])
                <input type="submit" style="display: none">
            </div>
        </form>

        <hr class="mt-m mb-s">

        <div class="flex-container-row justify-space-between items-center wrap">
            <div class="flex-2 min-width-xl">{{ $activities->links() }}</div>
            <div class="flex-none min-width-m py-m">
                @include('common.sort', array_merge($listOptions->getSortControlData(), ['useQuery' => true]))
            </div>
        </div>

        <div class="item-list">
            <div class="item-list-row flex-container-row items-center bold hide-under-m">
                <div class="flex-2 px-m py-xs flex-container-row items-center">{{ trans('settings.audit_table_user') }}</div>
                <div class="flex-2 px-m py-xs">{{ trans('settings.audit_table_event') }}</div>
                <div class="flex-3 px-m py-xs">{{ trans('settings.audit_table_related') }}</div>
                <div class="flex-container-row flex-3">
                    <div class="flex px-m py-xs">{{ trans('settings.audit_table_ip') }}</div>
                    <div class="flex-2 px-m py-xs text-right">{{ trans('settings.audit_table_date') }}</div>
                </div>
            </div>
            @foreach($activities as $activity)
                <div class="item-list-row flex-container-row items-center wrap py-xxs">
                    <div class="flex-2 px-m py-xxs flex-container-row items-center min-width-m">
                        @include('settings.parts.table-user', ['user' => $activity->user, 'user_id' => $activity->user_id])
                    </div>
                    <div class="flex-2 px-m py-xxs min-width-m"><strong class="mr-xs hide-over-m">{{ trans('settings.audit_table_event') }}:</strong> {{ $activity->type }}</div>
                    <div class="flex-3 px-m py-xxs min-width-l">
                        @if($activity->entity)
                            <a href="{{ $activity->entity->getUrl() }}" class="flex-container-row items-center">
                                <span role="presentation" class="icon flex-none text-{{$activity->entity->getType()}}">@icon($activity->entity->getType())</span>
                                <div class="flex text-{{ $activity->entity->getType() }}">
                                    {{ $activity->entity->name }}
                                </div>
                            </a>
                        @elseif($activity->detail && $activity->isForEntity())
                            <div>
                                {{ trans('settings.audit_deleted_item') }} <br>
                                {{ trans('settings.audit_deleted_item_name', ['name' => $activity->detail]) }}
                            </div>
                        @elseif($activity->detail)
                            <div>{{ $activity->detail }}</div>
                        @endif
                    </div>
                    <div class="flex-container-row flex-3">
                        <div class="flex px-m py-xxs min-width-xs"><strong class="mr-xs hide-over-m">{{ trans('settings.audit_table_ip') }}:<br></strong> {{ $activity->ip }}</div>
                        <div class="flex-2 px-m py-xxs text-m-right min-width-xs"><strong class="mr-xs hide-over-m">{{ trans('settings.audit_table_date') }}:<br></strong> {{ $activity->created_at }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="py-m">
            {{ $activities->links() }}
        </div>
    </div>

</div>
@stop
