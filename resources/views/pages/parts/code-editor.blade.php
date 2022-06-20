<div>
    <div components="popup code-editor" class="popup-background code-editor">
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

            <div class="flex-container-row flex-fill">
                <div class="code-editor-language-list flex-container-column flex-fill">
                    <label for="code-editor-language">{{ trans('components.code_language') }}</label>
                    <input refs="code-editor@languageInput" id="code-editor-language" type="text">
                    <div class="lang-options">
                        <button type="button" refs="code-editor@languageLink" data-lang="CSS">CSS</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="C">C</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="C++">C++</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="C#">C#</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="diff">Diff</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Fortran">Fortran</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="F#">F#</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Go">Go</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Haskell">Haskell</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="HTML">HTML</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="INI">INI</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Java">Java</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="JavaScript">JavaScript</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="JSON">JSON</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Julia">Julia</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="kotlin">Kotlin</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="LaTeX">LaTeX</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Lua">Lua</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="MarkDown">MarkDown</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Nginx">Nginx</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="ocaml">OCaml</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="PASCAL">Pascal</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Perl">Perl</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="PHP">PHP</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Powershell">Powershell</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Python">Python</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="Ruby">Ruby</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="rust">Rust</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="shell">Shell/Bash</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="SQL">SQL</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="typescript">TypeScript</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="VBScript">VBScript</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="VB.NET">VB.NET</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="XML">XML</button>
                        <button type="button" refs="code-editor@languageLink" data-lang="YAML">YAML</button>
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
