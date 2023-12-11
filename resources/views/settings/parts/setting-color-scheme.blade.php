{{--
    @mode - 'light' or 'dark'.
--}}
<p class="small">{{ trans('settings.ui_colors_desc') }}</p>
<div class="grid half pt-m">
    <div>
        @include('settings.parts.setting-color-picker', ['type' => 'app', 'mode' => $mode])
    </div>
    <div>
        @include('settings.parts.setting-color-picker', ['type' => 'link', 'mode' => $mode])
    </div>
</div>
<hr>
<p class="small">{!! trans('settings.content_colors_desc') !!}</p>
<div class="grid half pt-m">
    <div>
        @include('settings.parts.setting-color-picker', ['type' => 'bookshelf', 'mode' => $mode])
        @include('settings.parts.setting-color-picker', ['type' => 'book', 'mode' => $mode])
        @include('settings.parts.setting-color-picker', ['type' => 'chapter', 'mode' => $mode])
    </div>
    <div>
        @include('settings.parts.setting-color-picker', ['type' => 'page', 'mode' => $mode])
        @include('settings.parts.setting-color-picker', ['type' => 'page-draft', 'mode' => $mode])
    </div>
</div>

<input type="hidden"
       value="{{ setting('app-color-light' . ($mode === 'dark' ? '-dark' : '')) }}"
       name="setting-app-color-light{{ $mode === 'dark' ? '-dark' : '' }}">