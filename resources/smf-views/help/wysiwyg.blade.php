@extends('layouts.plain')
@section('document-class', 'bg-white ' .  (setting()->getForCurrentUser('dark-mode-enabled') ? 'dark-mode ' : ''))

@section('content')
    <div class="p-m">

        <h4 class="mt-s">{{ trans('editor.editor_license') }}</h4>
        <p>
            {!! trans('editor.editor_tiny_license', ['tinyLink' => '<a href="https://www.tiny.cloud/" target="_blank" rel="noopener noreferrer">TinyMCE</a>']) !!}
            <br>
            <a href="{{ url('/libs/tinymce/license.txt') }}" target="_blank">{{ trans('editor.editor_tiny_license_link') }}</a>
        </p>

        <h4>{{ trans('editor.shortcuts') }}</h4>

        <p>{{ trans('editor.shortcuts_intro') }}</p>
        <table>
            <thead>
            <tr>
                <th>{{ trans('editor.shortcut') }} {{ trans('editor.windows_linux') }}</th>
                <th>{{ trans('editor.shortcut') }} {{ trans('editor.mac') }}</th>
                <th>{{ trans('editor.description') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><code>Ctrl</code>+<code>S</code></td>
                <td><code>Cmd</code>+<code>S</code></td>
                <td>{{ trans('entities.pages_edit_save_draft') }}</td>
            </tr>
            <tr>
                <td><code>Ctrl</code>+<code>Enter</code></td>
                <td><code>Cmd</code>+<code>Enter</code></td>
                <td>{{ trans('editor.save_continue') }}</td>
            </tr>
            <tr>
                <td><code>Ctrl</code>+<code>B</code></td>
                <td><code>Cmd</code>+<code>B</code></td>
                <td>{{ trans('editor.bold') }}</td>
            </tr>
            <tr>
                <td><code>Ctrl</code>+<code>I</code></td>
                <td><code>Cmd</code>+<code>I</code></td>
                <td>{{ trans('editor.italic') }}</td>
            </tr>
            <tr>
                <td>
                    <code>Ctrl</code>+<code>1</code><br>
                    <code>Ctrl</code>+<code>2</code><br>
                    <code>Ctrl</code>+<code>3</code><br>
                    <code>Ctrl</code>+<code>4</code>
                </td>
                <td>
                    <code>Cmd</code>+<code>1</code><br>
                    <code>Cmd</code>+<code>2</code><br>
                    <code>Cmd</code>+<code>3</code><br>
                    <code>Cmd</code>+<code>4</code>
                </td>
                <td>
                    {{ trans('editor.header_large') }} <br>
                    {{ trans('editor.header_medium') }} <br>
                    {{ trans('editor.header_small') }} <br>
                    {{ trans('editor.header_tiny') }}
                </td>
            </tr>
            <tr>
                <td>
                    <code>Ctrl</code>+<code>5</code><br>
                    <code>Ctrl</code>+<code>D</code>
                </td>
                <td>
                    <code>Cmd</code>+<code>5</code><br>
                    <code>Cmd</code>+<code>D</code>
                </td>
                <td>{{ trans('editor.paragraph') }}</td>
            </tr>
            <tr>
                <td>
                    <code>Ctrl</code>+<code>6</code><br>
                    <code>Ctrl</code>+<code>Q</code>
                </td>
                <td>
                    <code>Cmd</code>+<code>6</code><br>
                    <code>Cmd</code>+<code>Q</code>
                </td>
                <td>{{ trans('editor.blockquote') }}</td>
            </tr>
            <tr>
                <td>
                    <code>Ctrl</code>+<code>7</code><br>
                    <code>Ctrl</code>+<code>E</code>
                </td>
                <td>
                    <code>Cmd</code>+<code>7</code><br>
                    <code>Cmd</code>+<code>E</code>
                </td>
                <td>{{ trans('editor.insert_code_block') }}</td>
            </tr>
            <tr>
                <td>
                    <code>Ctrl</code>+<code>Shift</code>+<code>8</code><br>
                    <code>Ctrl</code>+<code>Shift</code>+<code>E</code>
                </td>
                <td>
                    <code>Cmd</code>+<code>Shift</code>+<code>8</code><br>
                    <code>Cmd</code>+<code>Shift</code>+<code>E</code>
                </td>
                <td>{{ trans('editor.inline_code') }}</td>
            </tr>
            <tr>
                <td><code>Ctrl</code>+<code>9</code></td>
                <td><code>Cmd</code>+<code>9</code></td>
                <td>
                    {{ trans('editor.callouts') }} <br>
                    <small>{{ trans('editor.callouts_cycle') }}</small>
                </td>
            </tr>
            <tr>
                <td>
                    <code>Ctrl</code>+<code>O</code> <br>
                    <code>Ctrl</code>+<code>P</code>
                </td>
                <td>
                    <code>Cmd</code>+<code>O</code> <br>
                    <code>Cmd</code>+<code>P</code>
                </td>
                <td>
                    {{ trans('editor.list_numbered') }} <br>
                    {{ trans('editor.list_bullet') }}
                </td>
            </tr>
            <tr>
                <td>
                    <code>Ctrl</code>+<code>Shift</code>+<code>K</code>
                </td>
                <td>
                    <code>Cmd</code>+<code>Shift</code>+<code>K</code>
                </td>
                <td>{{ trans('editor.link_selector') }}</td>
            </tr>
            </tbody>
        </table>

    </div>
@endsection

