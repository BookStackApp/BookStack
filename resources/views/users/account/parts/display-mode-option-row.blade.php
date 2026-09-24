<div class="grid half gap-xl v-center">
    <div>
        <label for="user-language" class="setting-list-label">{{ trans('preferences.interface_display_mode') }}</label>
        <p class="small">
            {{ trans('preferences.interface_display_mode_desc') }}
        </p>
    </div>
    <div>
        @php
            $userOptions = setting()->getForCurrentUser('dark-mode-enabled') ? 'dark' : 'light';
            $value = old('display_mode') ?? $userOptions;
        @endphp
        <select name="display_mode" id="display-mode">
            <option @if($value === 'light') selected @endif value="light">{{ trans('common.light_mode') }}</option>
            <option @if($value === 'dark') selected @endif value="dark">{{ trans('common.dark_mode') }}</option>
        </select>
    </div>
</div>