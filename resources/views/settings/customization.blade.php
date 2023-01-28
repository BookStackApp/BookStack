@extends('settings.layout')

@section('card')
    <h1 id="customization" class="list-heading">{{ trans('settings.app_customization') }}</h1>
    <form action="{{ url("/settings/customization") }}" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <input type="hidden" name="section" value="customization">

        <div class="setting-list">

            <div class="grid half gap-xl">
                <div>
                    <label for="setting-app-name" class="setting-list-label">{{ trans('settings.app_name') }}</label>
                    <p class="small">{{ trans('settings.app_name_desc') }}</p>
                </div>
                <div class="pt-xs">
                    <input type="text" value="{{ setting('app-name', 'BookStack') }}" name="setting-app-name" id="setting-app-name">
                    @include('form.toggle-switch', [
                        'name' => 'setting-app-name-header',
                        'value' => setting('app-name-header'),
                        'label' => trans('settings.app_name_header'),
                    ])
                </div>
            </div>

            <div class="grid half gap-xl items-center">
                <div>
                    <label class="setting-list-label" for="setting-app-editor">{{ trans('settings.app_default_editor') }}</label>
                    <p class="small">{{ trans('settings.app_default_editor_desc') }}</p>
                </div>
                <div>
                    <select name="setting-app-editor" id="setting-app-editor">
                        <option @if(setting('app-editor') === 'wysiwyg') selected @endif value="wysiwyg">WYSIWYG</option>
                        <option @if(setting('app-editor') === 'markdown') selected @endif value="markdown">Markdown</option>
                    </select>
                </div>
            </div>

            <div class="grid half gap-xl">
                <div>
                    <label class="setting-list-label">{{ trans('settings.app_logo') }}</label>
                    <p class="small">{!! trans('settings.app_logo_desc') !!}</p>
                </div>
                <div class="pt-xs">
                    @include('form.image-picker', [
                             'removeName' => 'setting-app-logo',
                             'removeValue' => 'none',
                             'defaultImage' => url('/logo.png'),
                             'currentImage' => setting('app-logo'),
                             'name' => 'app_logo',
                             'imageClass' => 'logo-image',
                         ])
                </div>
            </div>

            <div class="grid half gap-xl">
                <div>
                    <label class="setting-list-label">{{ trans('settings.app_icon') }}</label>
                    <p class="small">{{ trans('settings.app_icon_desc') }}</p>
                </div>
                <div class="pt-xs">
                    @include('form.image-picker', [
                             'removeValue' => 'none',
                             'defaultImage' => url('/icon.png'),
                             'currentImage' => setting('app-icon'),
                             'name' => 'app_icon',
                             'imageClass' => 'logo-image',
                         ])
                </div>
            </div>

            <!-- App Color Scheme -->
            @php
                $darkMode = boolval(setting()->getForCurrentUser('dark-mode-enabled'));
            @endphp
            <div component="setting-app-color-scheme"
                 option:setting-app-color-scheme:mode="{{ $darkMode ? 'dark' : 'light' }}"
                 class="pb-l">
                <div class="mb-l">
                    <label class="setting-list-label">{{ trans('settings.color_scheme') }}</label>
                    <p class="small">{{ trans('settings.color_scheme_desc') }}</p>
                </div>

                <div component="tabs" class="tab-container">
                    <div role="tablist" class="controls-card">
                        <button type="button"
                                role="tab"
                                id="color-scheme-tab-light"
                                aria-selected="{{ $darkMode ? 'false' : 'true' }}"
                                aria-controls="color-scheme-panel-light">@icon('light-mode'){{ trans('common.light_mode') }}</button>
                        <button type="button"
                                role="tab"
                                id="color-scheme-tab-dark"
                                aria-selected="{{ $darkMode ? 'true' : 'false' }}"
                                aria-controls="color-scheme-panel-dark">@icon('dark-mode'){{ trans('common.dark_mode') }}</button>
                    </div>
                    <div class="sub-card">
                        <div id="color-scheme-panel-light"
                             refs="setting-app-color-scheme@lightContainer"
                             tabindex="0"
                             role="tabpanel"
                             aria-labelledby="color-scheme-tab-light"
                             @if($darkMode) hidden @endif
                             class="p-m">
                            @include('settings.parts.setting-color-scheme', ['mode' => 'light'])
                        </div>
                        <div id="color-scheme-panel-dark"
                             refs="setting-app-color-scheme@darkContainer"
                             tabindex="0"
                             role="tabpanel"
                             aria-labelledby="color-scheme-tab-light"
                             @if(!$darkMode) hidden @endif
                             class="p-m">
                            @include('settings.parts.setting-color-scheme', ['mode' => 'dark'])
                        </div>
                    </div>
                </div>
            </div>

            <div component="setting-homepage-control" id="homepage-control" class="grid half gap-xl items-center">
                <div>
                    <label for="setting-app-homepage-type" class="setting-list-label">{{ trans('settings.app_homepage') }}</label>
                    <p class="small">{{ trans('settings.app_homepage_desc') }}</p>
                </div>
                <div>
                    <select refs="setting-homepage-control@type-control"
                            name="setting-app-homepage-type"
                            id="setting-app-homepage-type">
                        <option @if(setting('app-homepage-type') === 'default') selected @endif value="default">{{ trans('common.default') }}</option>
                        <option @if(setting('app-homepage-type') === 'books') selected @endif value="books">{{ trans('entities.books') }}</option>
                        <option @if(setting('app-homepage-type') === 'bookshelves') selected @endif value="bookshelves">{{ trans('entities.shelves') }}</option>
                        <option @if(setting('app-homepage-type') === 'page') selected @endif value="page">{{ trans('entities.pages_specific') }}</option>
                    </select>

                    <div refs="setting-homepage-control@page-picker-container" style="display: none;" class="mt-m">
                        @include('settings.parts.page-picker', ['name' => 'setting-app-homepage', 'placeholder' => trans('settings.app_homepage_select'), 'value' => setting('app-homepage')])
                    </div>
                </div>
            </div>

            <div>
                <label for="setting-app-privacy-link" class="setting-list-label">{{ trans('settings.app_footer_links') }}</label>
                <p class="small mb-m">{{ trans('settings.app_footer_links_desc') }}</p>
                @include('settings.parts.footer-links', ['name' => 'setting-app-footer-links', 'value' => setting('app-footer-links', [])])
            </div>


            <div>
                <label for="setting-app-custom-head" class="setting-list-label">{{ trans('settings.app_custom_html') }}</label>
                <p class="small">{{ trans('settings.app_custom_html_desc') }}</p>
                <div class="mt-m">
                    <textarea component="code-textarea"
                              option:code-textarea:mode="html"
                              name="setting-app-custom-head"
                              id="setting-app-custom-head"
                              class="simple-code-input">{{ setting('app-custom-head', '') }}</textarea>
                </div>
                <p class="small text-right">{{ trans('settings.app_custom_html_disabled_notice') }}</p>
            </div>


        </div>

        <div class="form-group text-right">
            <button type="submit" class="button">{{ trans('settings.settings_save') }}</button>
        </div>
    </form>
@endsection

@section('after-content')
    @include('entities.selector-popup', ['entityTypes' => 'page'])
@endsection
