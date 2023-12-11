<div>
    <div components="popup code-editor"
         option:code-editor:favourites="{{ setting()->getForCurrentUser('code-language-favourites', '') }}"
         class="popup-background code-editor">
        <div refs="code-editor@container" class="popup-body" tabindex="-1">

            <div class="popup-header flex-container-row primary-background">
                <div class="popup-title">{{ trans('components.code_editor') }}</div>
                <div component="dropdown" refs="code-editor@historyDropDown" class="flex-container-row">
                    <button refs="dropdown@toggle">
                        <span>@icon('history')</span>
                        <span>{{ trans('components.code_session_history') }}</span>
                    </button>
                    <ul refs="dropdown@menu code-editor@historyList" class="dropdown-menu"></ul>
                </div>
                <button class="popup-header-close" refs="popup@hide">@icon('close')</button>
            </div>

            <div class="code-editor-body-wrap flex-container-row flex-fill">
                <div class="code-editor-language-list flex-container-column flex-fill">
                    <label for="code-editor-language">{{ trans('components.code_language') }}</label>
                    <input refs="code-editor@languageInput" id="code-editor-language" type="text">
                    <div refs="code-editor@language-options-container" class="lang-options">
                        @php
                            $languages = [
                                'Bash', 'CSS', 'C', 'C++', 'C#', 'Clojure', 'Dart', 'Diff', 'Fortran', 'F#', 'Go', 'Haskell', 'HTML', 'INI',
                                'Java', 'JavaScript', 'JSON', 'Julia', 'Kotlin', 'LaTeX', 'Lua', 'MarkDown', 'MATLAB', 'MSSQL', 'MySQL', 'Nginx', 'OCaml',
                                'Octave', 'Pascal', 'Perl', 'PHP', 'PL/SQL', 'PostgreSQL', 'Powershell', 'Python', 'Ruby', 'Rust', 'Scheme', 'Shell', 'Smarty',
                                 'SQL', 'SQLite', 'Swift', 'Twig', 'TypeScript', 'VBScript', 'VB.NET', 'XML', 'YAML',
                            ];
                        @endphp

                        @foreach($languages as $language)
                            <div class="relative">
                                <button type="button" refs="code-editor@language-button" data-favourite="false" data-lang="{{ strtolower($language) }}">{{ $language }}</button>
                                <button class="lang-option-favorite-toggle action-favourite" data-title="{{ trans('common.favourite') }}">@icon('star-outline')</button>
                                <button class="lang-option-favorite-toggle action-unfavourite" data-title="{{ trans('common.unfavourite') }}">@icon('star')</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="code-editor-main flex-fill">
                    <textarea refs="code-editor@editor"></textarea>
                </div>

            </div>

            <div class="popup-footer">
                <button refs="code-editor@saveButton" type="button" class="button">{{ trans('components.code_save') }}</button>
            </div>

        </div>
    </div>
</div>
