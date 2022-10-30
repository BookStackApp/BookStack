<div class="item-list-row py-s">
    <div class="flex-container-row">
        <div class="flex-2 py-xxs px-m flex-container-row items-center gap-s">
            @include('common.status-indicator', ['status' => $webhook->active])
            <a href="{{ $webhook->getUrl() }}">{{ $webhook->name }}</a>
        </div>
        <div class="flex py-xxs px-m text-right text-muted">
            @if($webhook->tracksEvent('all'))
                {{ trans('settings.webhooks_events_all') }}
            @else
                {{ trans_choice('settings.webhooks_x_trigger_events', $webhook->tracked_events_count, ['count' =>  $webhook->tracked_events_count]) }}
            @endif
        </div>
    </div>
    <div class="px-m py-xxs text-muted italic text-limit-lines-1">
        <small>{{ $webhook->endpoint }}</small>
    </div>
</div>