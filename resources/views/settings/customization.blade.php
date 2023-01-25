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
                    <label class="setting-list-label">{{ 'Application Icon' }}</label>
                    <p class="small">
                        This icon is used for browser tabs and shortcut icons.
                        This should be a 256px square PNG image.
                    </p>
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

            <!-- Primary Color -->
            <div class="grid half gap-xl">
                <div>
                    <label class="setting-list-label">{{ trans('settings.app_primary_color') }}</label>
                    <p class="small">{!! trans('settings.app_primary_color_desc') !!}</p>
                </div>
                <div component="setting-app-color-picker setting-color-picker"
                     option:setting-color-picker:default="#206ea7"
                     option:setting-color-picker:current="{{ setting('app-color') }}"
                     class="text-m-right pt-xs">
                    <input refs="setting-color-picker@input setting-app-color-picker@input" type="color" value="{{ setting('app-color') }}" name="setting-app-color" id="setting-app-color" placeholder="#206ea7">
                    <input refs="setting-app-color-picker@light-input" type="hidden" value="{{ setting('app-color-light') }}" name="setting-app-color-light" id="setting-app-color-light">
                    <div class="pr-s">
                        <button refs="setting-color-picker@default-button" type="button" class="text-button text-muted mt-s">{{ trans('common.default') }}</button>
                        <span class="sep">|</span>
                        <button refs="setting-color-picker@reset-button" type="button" class="text-button text-muted mt-s">{{ trans('common.reset') }}</button>
                    </div>

                </div>
            </div>

            <!-- Entity Color -->
            <div class="pb-l">
                <div>
                    <label class="setting-list-label">{{ trans('settings.content_colors') }}</label>
                    <p class="small">{!! trans('settings.content_colors_desc') !!}</p>
                </div>
                <div class="grid half pt-m">
                    <div>
                        @include('settings.parts.setting-entity-color-picker', ['type' => 'bookshelf'])
                        @include('settings.parts.setting-entity-color-picker', ['type' => 'book'])
                        @include('settings.parts.setting-entity-color-picker', ['type' => 'chapter'])
                    </div>
                    <div>
                        @include('settings.parts.setting-entity-color-picker', ['type' => 'page'])
                        @include('settings.parts.setting-entity-color-picker', ['type' => 'page-draft'])
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
