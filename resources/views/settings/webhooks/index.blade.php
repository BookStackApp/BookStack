@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'webhooks'])
        </div>

        <div class="card content-wrap auto-height">

            <div class="grid half v-center">
                <h1 class="list-heading">{{ trans('settings.webhooks') }}</h1>

                <div class="text-right">
                    <a href="{{ url("/settings/webhooks/create") }}"
                       class="button outline">{{ trans('settings.webhooks_create') }}</a>
                </div>
            </div>

            @if(count($webhooks) > 0)

                <table class="table">
                    <tr>
                        <th>{{ trans('common.name') }}</th>
                        <th width="100">{{ trans('settings.webhook_events_table_header') }}</th>
                        <th width="100">{{ trans('common.status') }}</th>
                    </tr>
                    @foreach($webhooks as $webhook)
                        <tr>
                            <td>
                                <a href="{{ $webhook->getUrl() }}">{{ $webhook->name }}</a> <br>
                                <span class="small text-muted italic">{{ $webhook->endpoint }}</span>
                            </td>
                            <td>
                                @if($webhook->tracksEvent('all'))
                                    {{ trans('settings.webhooks_events_all') }}
                                @else
                                    {{ $webhook->trackedEvents->count() }}
                                @endif
                            </td>
                            <td>
                                {{ trans('common.status_' . ($webhook->active ? 'active' : 'inactive')) }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p class="text-muted empty-text px-none">
                    {{ trans('settings.webhooks_none_created') }}
                </p>
            @endif


        </div>
    </div>

@stop
