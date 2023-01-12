{{--
    @type - Name of entity type
--}}
<div component="setting-color-picker"
     option:setting-color-picker:default="{{ config('setting-defaults.'. $type .'-color') }}"
     option:setting-color-picker:current="{{ setting($type .'-color') }}"
     class="grid no-break half mb-l">
    <div>
        <label for="setting-{{ $type }}-color" class="text-dark">{{ trans('settings.'. str_replace('-', '_', $type) .'_color') }}</label>
        <button refs="setting-color-picker@default-button" type="button" class="text-button text-muted">{{ trans('common.default') }}</button>
        <span class="sep">|</span>
        <button refs="setting-color-picker@reset-button" type="button" class="text-button text-muted">{{ trans('common.reset') }}</button>
    </div>
    <div>
        <input type="color"
               refs="setting-color-picker@input"
               value="{{ setting($type .'-color') }}"
               name="setting-{{ $type }}-color"
               id="setting-{{ $type }}-color"
               placeholder="{{ config('setting-defaults.'. $type .'-color') }}"
               class="small">
    </div>
</div>
