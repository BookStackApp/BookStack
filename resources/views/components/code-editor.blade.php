<div>
    <div components="popup code-editor" class="popup-background code-editor">
        <div refs="code-editor@container" class="popup-body" tabindex="-1">

            <div class="popup-header primary-background">
                <div class="popup-title">{{ trans('components.code_editor') }}</div>
                <button class="popup-header-close" refs="popup@hide">x</button>
            </div>

            <div class="p-l popup-content">
                <div class="form-group">
                    <label for="code-editor-language">{{ trans('components.code_language') }}</label>
                    <div class="lang-options">
                        <small>
                            <a refs="code-editor@languageLink" data-lang="CSS">CSS</a>
                            <a refs="code-editor@languageLink" data-lang="C">C</a>
                            <a refs="code-editor@languageLink" data-lang="C++">C++</a>
                            <a refs="code-editor@languageLink" data-lang="C#">C#</a>
                            <a refs="code-editor@languageLink" data-lang="Fortran">Fortran</a>
                            <a refs="code-editor@languageLink" data-lang="Go">Go</a>
                            <a refs="code-editor@languageLink" data-lang="HTML">HTML</a>
                            <a refs="code-editor@languageLink" data-lang="INI">INI</a>
                            <a refs="code-editor@languageLink" data-lang="Java">Java</a>
                            <a refs="code-editor@languageLink" data-lang="JavaScript">JavaScript</a>
                            <a refs="code-editor@languageLink" data-lang="JSON">JSON</a>
                            <a refs="code-editor@languageLink" data-lang="Lua">Lua</a>
                            <a refs="code-editor@languageLink" data-lang="MarkDown">MarkDown</a>
                            <a refs="code-editor@languageLink" data-lang="Nginx">Nginx</a>
                            <a refs="code-editor@languageLink" data-lang="PASCAL">Pascal</a>
                            <a refs="code-editor@languageLink" data-lang="Perl">Perl</a>
                            <a refs="code-editor@languageLink" data-lang="PHP">PHP</a>
                            <a refs="code-editor@languageLink" data-lang="Powershell">Powershell</a>
                            <a refs="code-editor@languageLink" data-lang="Python">Python</a>
                            <a refs="code-editor@languageLink" data-lang="Ruby">Ruby</a>
                            <a refs="code-editor@languageLink" data-lang="shell">Shell/Bash</a>
                            <a refs="code-editor@languageLink" data-lang="SQL">SQL</a>
                            <a refs="code-editor@languageLink" data-lang="VBScript">VBScript</a>
                            <a refs="code-editor@languageLink" data-lang="XML">XML</a>
                            <a refs="code-editor@languageLink" data-lang="YAML">YAML</a>
                        </small>
                    </div>
                    <input refs="code-editor@languageInput" id="code-editor-language" type="text">
                </div>

                <div class="form-group">
                    <div class="grid half no-break v-end mb-xs">
                        <div>
                            <label for="code-editor-content">{{ trans('components.code_content') }}</label>
                        </div>
                        <div class="text-right">
                            <div component="dropdown" refs="code-editor@historyDropDown" class="inline block">
                                <button refs="dropdown@toggle" class="text-button text-small">@icon('history') {{ trans('components.code_session_history') }}</button>
                                <ul refs="dropdown@menu code-editor@historyList" class="dropdown-menu"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <textarea refs="code-editor@editor"></textarea>
                </div>

                <div class="form-group">
                    <button refs="code-editor@saveButton" type="button" class="button">{{ trans('components.code_save') }}</button>
                </div>

            </div>

        </div>
    </div>
</div>
