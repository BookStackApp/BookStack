<div id="code-editor">
    <div class="overlay" ref="overlay" v-cloak @click="hide()">
        <div class="popup-body" @click.stop>

            <div class="popup-header primary-background">
                <div class="popup-title">{{ trans('components.code_editor') }}</div>
                <button class="popup-close neg corner-button button" @click="hide()">x</button>
            </div>

            <div class="padded">
                <div class="form-group">
                    <label for="code-editor-language">{{ trans('components.code_language') }}</label>
                    <input @keypress.enter="save()" id="code-editor-language" type="text" @input="updateEditorMode(language)" v-model="language">
                </div>

                <div class="form-group">
                    <label for="code-editor-content">{{ trans('components.code_content') }}</label>
                    <textarea ref="editor" v-model="code"></textarea>
                </div>

                <div class="form-group">
                    <button type="button" class="button pos" @click="save()">{{ trans('components.code_save') }}</button>
                </div>

            </div>

        </div>
    </div>
</div>