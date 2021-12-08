{!! csrf_field() !!}

<div class="card content-wrap auto-height">
    <h1 class="list-heading">{{ $title }}</h1>

    <div class="setting-list">

        <div class="grid half">
            <div>
                <label class="setting-list-label">{{ trans('settings.webhooks_details') }}</label>
                <p class="small">{{ trans('settings.webhooks_details_desc') }}</p>
            </div>
            <div>
                <div class="form-group">
                    <label for="name">{{ trans('settings.webhooks_name') }}</label>
                    @include('form.text', ['name' => 'name'])
                </div>
                <div class="form-group">
                    <label for="endpoint">{{ trans('settings.webhooks_endpoint') }}</label>
                    @include('form.text', ['name' => 'endpoint'])
                </div>
            </div>
        </div>

        <div component="webhook-events">
            <label class="setting-list-label">{{ trans('settings.webhooks_events') }}</label>
            <p class="small">{{ trans('settings.webhooks_events_desc') }}</p>
            <p class="text-warn small">{{ trans('settings.webhooks_events_warning') }}</p>

            <div>
                <label><input type="checkbox"
                              name="events[]"
                              value="all"
                              refs="webhook-events@all">
                    {{ trans('settings.webhooks_events_all') }}</label>
            </div>

            <hr class="my-m">

            <div class="dual-column-content">
                @foreach(\BookStack\Actions\ActivityType::all() as $activityType)
                    <label><input type="checkbox" name="events[]" value="{{ $activityType }}">{{ $activityType }}</label>
                @endforeach
            </div>
        </div>

    </div>

    <div class="form-group text-right">
        <a href="{{ url("/settings/webhooks") }}" class="button outline">{{ trans('common.cancel') }}</a>
        @if ($webhook->id ?? false)
            <a href="{{ url("/settings/roles/delete/{$webhook->id}") }}" class="button outline">{{ trans('settings.webhooks_delete') }}</a>
        @endif
        <button type="submit" class="button">{{ trans('settings.webhooks_save') }}</button>
    </div>

</div>
