@extends('layouts.simple')

@section('body')

    <div class="container small">
        @include('settings.parts.navbar', ['selected' => 'webhooks'])

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.webhooks_edit') }}</h1>


            <div class="setting-list">
            <div class="grid half">
                <div>
                    <label class="setting-list-label">{{ trans('settings.webhooks_status') }}</label>
                    <p class="mb-none">
                        @if($webhook->last_called_at)
                            <span title="{{ $dates->absolute($webhook->last_called_at) }}">{{ trans('settings.webhooks_last_called') }} {{  $dates->relative($webhook->last_called_at) }}</span>
                        @else
                            <span>{{ trans('settings.webhooks_last_called') }} {{ trans('common.never') }}</span>
                        @endif
                        <br>
                        @if($webhook->last_errored_at)
                            <span title="{{ $dates->absolute($webhook->last_errored_at) }}">{{ trans('settings.webhooks_last_errored') }} {{  $dates->relative($webhook->last_errored_at) }}</span>
                        @else
                            <span>{{ trans('settings.webhooks_last_errored') }} {{ trans('common.never') }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-muted">
                    <br>
                    @if($webhook->last_error)
                        {{ trans('settings.webhooks_last_error_message') }} <br>
                        <span class="text-warn text-small">{{ $webhook->last_error }}</span>
                    @endif
                </div>
            </div>
            </div>


            <hr>

            <form action="{{ $webhook->getUrl() }}" method="POST">
                {!! csrf_field() !!}
                {!! method_field('PUT') !!}
                @include('settings.webhooks.parts.form', ['model' => $webhook, 'title' => trans('settings.webhooks_edit')])

                <div class="form-group text-right">
                    <a href="{{ url("/settings/webhooks") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <a href="{{ $webhook->getUrl('/delete') }}" class="button outline">{{ trans('settings.webhooks_delete') }}</a>
                    <button type="submit" class="button">{{ trans('settings.webhooks_save') }}</button>
                </div>

            </form>
        </div>

        @include('settings.webhooks.parts.format-example')
    </div>

@stop
