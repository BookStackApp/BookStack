{{--
    @type - Name of color setting
--}}
@php
    $keyAppends = ($mode === 'light' ? '' : '-' . $mode);
@endphp
<div component="setting-color-picker"
     option:setting-color-picker:default="{{ config('setting-defaults.'. $type .'-color' . $keyAppends) }}"
     option:setting-color-picker:current="{{ setting($type .'-color' . $keyAppends) }}"
     class="grid no-break half mb-l">
    <div>
        <label for="setting-{{ $type }}-color{{ $keyAppends }}" class="text-dark">{{ trans('settings.'. str_replace('-', '_', $type) .'_color') }}</label>
        <button refs="setting-color-picker@default-button" type="button" class="text-button text-muted">{{ trans('common.default') }}</button>
        <span class="sep">|</span>
        <button refs="setting-color-picker@reset-button" type="button" class="text-button text-muted">{{ trans('common.reset') }}</button>
    </div>
    <div>
        <input type="color"
               refs="setting-color-picker@input"
               value="{{ setting($type . '-color' . $keyAppends) }}"
               name="setting-{{ $type }}-color{{ $keyAppends }}"
               id="setting-{{ $type }}-color{{ $keyAppends }}"
               placeholder="{{ config('setting-defaults.'. $type .'-color' . $keyAppends) }}"
               class="small">
    </div>
</div>
