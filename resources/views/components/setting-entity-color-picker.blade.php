{{--
    @type - Name of entity type
--}}
<div setting-color-picker class="grid no-break half mb-l">
    <div>
        <label for="setting-{{ $type }}-color" class="text-dark">{{ trans('settings.'. str_replace('-', '_', $type) .'_color') }}</label>
        <button type="button" class="text-button text-muted" setting-color-picker-default>{{ trans('common.default') }}</button>
        <span class="sep mx-xs">|</span>
        <button type="button" class="text-button text-muted" setting-color-picker-reset>{{ trans('common.reset') }}</button>
    </div>
    <div>
        <input type="color"
               data-default="{{ config('setting-defaults.'. $type .'-color') }}"
               data-current="{{ setting($type .'-color') }}"
               value="{{ setting($type .'-color') }}"
               name="setting-{{ $type }}-color"
               id="setting-{{ $type }}-color"
               placeholder="{{ config('setting-defaults.'. $type .'-color') }}"
               class="small">
    </div>
</div>