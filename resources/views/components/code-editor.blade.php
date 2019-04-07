<div id="code-editor">
    <div overlay ref="overlay" v-cloak @click="hide()">
        <div class="popup-body" @click.stop>

            <div class="popup-header primary-background">
                <div class="popup-title">{{ trans('components.code_editor') }}</div>
                <button class="popup-header-close" @click="hide()">x</button>
            </div>

            <div class="p-l popup-content">
                <div class="form-group">
                    <label for="code-editor-language">{{ trans('components.code_language') }}</label>
                    <div class="lang-options">
                        <small>
                            <a @click="updateLanguage('CSS')">CSS</a>
                            <a @click="updateLanguage('C')">C</a>
                            <a @click="updateLanguage('C++')">C++</a>
                            <a @click="updateLanguage('C#')">C#</a>
                            <a @click="updateLanguage('Go')">Go</a>
                            <a @click="updateLanguage('HTML')">HTML</a>
                            <a @click="updateLanguage('Java')">Java</a>
                            <a @click="updateLanguage('JavaScript')">JavaScript</a>
                            <a @click="updateLanguage('JSON')">JSON</a>
                            <a @click="updateLanguage('Lua')">Lua</a>
                            <a @click="updateLanguage('PHP')">PHP</a>
                            <a @click="updateLanguage('Powershell')">Powershell</a>
                            <a @click="updateLanguage('MarkDown')">MarkDown</a>
                            <a @click="updateLanguage('Nginx')">Nginx</a>
                            <a @click="updateLanguage('Python')">Python</a>
                            <a @click="updateLanguage('Ruby')">Ruby</a>
                            <a @click="updateLanguage('shell')">Shell/Bash</a>
                            <a @click="updateLanguage('SQL')">SQL</a>
                            <a @click="updateLanguage('XML')">XML</a>
                            <a @click="updateLanguage('YAML')">YAML</a>
                        </small>
                    </div>
                    <input @keypress.enter="save()" id="code-editor-language" type="text" @input="updateEditorMode(language)" v-model="language">
                </div>

                <div class="form-group">
                    <label for="code-editor-content">{{ trans('components.code_content') }}</label>
                    <textarea ref="editor" v-model="code"></textarea>
                </div>

                <div class="form-group">
                    <button type="button" class="button primary" @click="save()">{{ trans('components.code_save') }}</button>
                </div>

            </div>

        </div>
    </div>
</div>
