@extends('layouts.simple')

@section('body')
    <div class="container small">

        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'webhooks'])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading"> {{ trans('settings.webhooks_delete') }}</h1>

            <p>{{ trans('settings.webhooks_delete_warning', ['webhookName' => $webhook->name]) }}</p>


            <form action="{{ $webhook->getUrl() }}" method="POST">
                {!! csrf_field() !!}
                {!! method_field('DELETE') !!}

                <div class="grid half v-center">
                    <div>
                        <p class="text-neg">
                            <strong>{{ trans('settings.webhooks_delete_confirm') }}</strong>
                        </p>
                    </div>
                    <div>
                        <div class="form-group text-right">
                            <a href="{{ $webhook->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                            <button type="submit" class="button">{{ trans('common.confirm') }}</button>
                        </div>
                    </div>
                </div>


            </form>
        </div>

    </div>
@stop
