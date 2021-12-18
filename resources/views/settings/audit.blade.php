@extends('layouts.simple')

@section('body')
<div class="container">

    <div class="grid left-focus v-center no-row-gap">
        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'audit'])
        </div>
    </div>

    <div class="card content-wrap auto-height">
        <h1 class="list-heading">{{ trans('settings.audit') }}</h1>
        <p class="text-muted">{{ trans('settings.audit_desc') }}</p>

        <div class="flex-container-row">
            <div component="dropdown" class="list-sort-type dropdown-container mr-m">
                <label for="">{{ trans('settings.audit_event_filter') }}</label>
                <button refs="dropdown@toggle" aria-haspopup="true" aria-expanded="false" aria-label="{{ trans('common.sort_options') }}" class="input-base text-left">{{ $listDetails['event'] ?: trans('settings.audit_event_filter_no_filter') }}</button>
                <ul refs="dropdown@menu" class="dropdown-menu">
                    <li @if($listDetails['event'] === '') class="active" @endif><a href="{{ sortUrl('/settings/audit', $listDetails, ['event' => '']) }}">{{ trans('settings.audit_event_filter_no_filter') }}</a></li>
                    @foreach($activityTypes as $type)
                        <li @if($type === $listDetails['event']) class="active" @endif><a href="{{ sortUrl('/settings/audit', $listDetails, ['event' => $type]) }}">{{ $type }}</a></li>
                    @endforeach
                </ul>
            </div>

            <form action="{{ url('/settings/audit') }}" method="get" class="flex-container-row mr-m">
                @if(!empty($listDetails['event']))
                    <input type="hidden" name="event" value="{{ $listDetails['event'] }}">
                @endif

                @foreach(['date_from', 'date_to'] as $filterKey)
                    <div class="mr-m">
                        <label for="audit_filter_{{ $filterKey }}">{{ trans('settings.audit_' . $filterKey) }}</label>
                        <input id="audit_filter_{{ $filterKey }}"
                               component="submit-on-change"
                               type="date"
                               name="{{ $filterKey }}"
                               value="{{ $listDetails[$filterKey] ?? '' }}">
                    </div>
                @endforeach

                <div class="form-group ml-auto mr-m"
                     component="submit-on-change"
                     option:submit-on-change:filter='[name="user"]'>
                    <label for="owner">{{ trans('settings.audit_table_user') }}</label>
                    @include('form.user-select', ['user' => $listDetails['user'] ? \BookStack\Auth\User::query()->find($listDetails['user']) : null, 'name' => 'user', 'compact' =>  true])
                </div>


                <div class="form-group ml-auto">
                    <label for="ip">{{ trans('settings.audit_table_ip') }}</label>
                    @include('form.text', ['name' => 'ip', 'model' => (object) $listDetails])
                    <input type="submit" style="display: none">
                </div>
            </form>
        </div>

        <hr class="mt-l mb-s">

        {{ $activities->links() }}

        <table class="table">
            <tbody>
            <tr>
                <th>{{ trans('settings.audit_table_user') }}</th>
                <th>
                    <a href="{{ sortUrl('/settings/audit', $listDetails, ['sort' => 'key']) }}">{{ trans('settings.audit_table_event') }}</a>
                </th>
                <th>{{ trans('settings.audit_table_related') }}</th>
                <th>{{ trans('settings.audit_table_ip') }}</th>
                <th>
                    <a href="{{ sortUrl('/settings/audit', $listDetails, ['sort' => 'created_at']) }}">{{ trans('settings.audit_table_date') }}</a></th>
            </tr>
            @foreach($activities as $activity)
                <tr>
                    <td>
                        @include('settings.parts.table-user', ['user' => $activity->user, 'user_id' => $activity->user_id])
                    </td>
                    <td>{{ $activity->type }}</td>
                    <td width="40%">
                        @if($activity->entity)
                            <a href="{{ $activity->entity->getUrl() }}" class="table-entity-item">
                                <span role="presentation" class="icon text-{{$activity->entity->getType()}}">@icon($activity->entity->getType())</span>
                                <div class="text-{{ $activity->entity->getType() }}">
                                    {{ $activity->entity->name }}
                                </div>
                            </a>
                        @elseif($activity->detail && $activity->isForEntity())
                            <div class="px-m">
                                {{ trans('settings.audit_deleted_item') }} <br>
                                {{ trans('settings.audit_deleted_item_name', ['name' => $activity->detail]) }}
                            </div>
                        @elseif($activity->detail)
                            <div class="px-m">{{ $activity->detail }}</div>
                        @endif
                    </td>
                    <td>{{ $activity->ip }}</td>
                    <td>{{ $activity->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $activities->links() }}
    </div>

</div>
@stop
