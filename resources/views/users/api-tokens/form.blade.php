

<div class="grid half gap-xl v-center">
    <div>
        <label class="setting-list-label">{{ trans('settings.user_api_token_name') }}</label>
        <p class="small">{{ trans('settings.user_api_token_name_desc') }}</p>
    </div>
    <div>
        @include('form.text', ['name' => 'name'])
    </div>
</div>

<div class="grid half gap-xl v-center">
    <div>
        <label class="setting-list-label">{{ trans('settings.user_api_token_expiry') }}</label>
        <p class="small">{{ trans('settings.user_api_token_expiry_desc') }}</p>
    </div>
    <div class="text-right">
        @include('form.date', ['name' => 'expires_at'])
    </div>
</div>