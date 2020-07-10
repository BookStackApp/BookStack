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
                        <select  id="code-editor-language" refs="code-editor@languageSelect">
                            <?php $options = array(
                                "C++",
                                "C#",
                                "Fortran",
                                "Go",
                                "HTML",
                                "INI",
                                "Java",
                                "JavaScript",
                                "TypeScript",
                                "JSX",
                                "TSX",
                                "JSON",
                                "Lua",
                                "MarkDown",
                                "Nginx",
                                "PASCAL",
                                "Perl",
                                "PHP",
                                "Powershell",
                                "Python",
                                "Ruby",
                                "shell",
                                "SQL",
                                "XML",
                                "YAML");?>
                            <?php foreach ($options as &$option): ?>
                                <option value="<?php echo $option; ?>"><?php echo $option; ?></option>"
                            <?php endforeach; ?>
                        </select>
                    </div>
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