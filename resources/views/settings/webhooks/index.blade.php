@extends('layouts.simple')

@section('body')

    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'webhooks'])

        <div class="card content-wrap auto-height">

            <div class="flex-container-row items-center justify-space-between wrap">
                <h1 class="list-heading">{{ trans('settings.webhooks') }}</h1>

                <div>
                    <a href="{{ url("/settings/webhooks/create") }}"
                       class="button outline">{{ trans('settings.webhooks_create') }}</a>
                </div>
            </div>

            <p class="text-muted">{{ trans('settings.webhooks_index_desc') }}</p>

            <div class="flex-container-row items-center justify-space-between gap-m mt-m mb-l wrap">
                <div>
                    <div class="block inline mr-xs">
                        <form method="get" action="{{ url("/settings/webhooks") }}">
                            <input type="text" name="search" placeholder="{{ trans('common.search') }}" @if($listDetails['search']) value="{{$listDetails['search']}}" @endif>
                        </form>
                    </div>
                </div>
                <div class="justify-flex-end">
                    @include('common.sort', ['options' => [
                        'name' => trans('common.sort_name'),
                        'endpoint'  => trans('settings.webhooks_endpoint'),
                        'created_at' => trans('common.sort_created_at'),
                        'updated_at' => trans('common.sort_updated_at'),
                        'active'     => trans('common.status'),
                    ], 'order' => $listDetails['order'], 'sort' => $listDetails['sort'], 'type' => 'webhooks'])
                </div>
            </div>

            @if(count($webhooks) > 0)
                <div class="item-list">
                    @foreach($webhooks as $webhook)
                        @include('settings.webhooks.parts.webhooks-list-item', ['webhook' => $webhook])
                    @endforeach
                </div>
            @else
                <p class="text-muted empty-text px-none">
                    {{ trans('settings.webhooks_none_created') }}
                </p>
            @endif

            <div class="my-m">
                {{ $webhooks->links() }}
            </div>

        </div>
    </div>

@stop
