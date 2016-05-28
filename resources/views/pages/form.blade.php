
<div class="page-editor flex-fill flex" ng-controller="PageEditController" editor-type="{{ setting('app-editor') }}" page-id="{{ $model->id or 0 }}" page-new-draft="{{ $model->draft or 0 }}" page-update-draft="{{ $model->isDraft or 0 }}">

    {{ csrf_field() }}
    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-4 faded">
                    <div class="action-buttons text-left">
                        <a href="{{ back()->getTargetUrl() }}" class="text-button text-primary"><i class="zmdi zmdi-arrow-left"></i>Back</a>
                        <a onclick="$('body>header').slideToggle();" class="text-button text-primary"><i class="zmdi zmdi-swap-vertical"></i>Toggle Header</a>
                    </div>
                </div>
                <div class="col-sm-4 faded text-center">

                    <div dropdown class="dropdown-container">
                        <a dropdown-toggle class="text-primary text-button"><span class="faded-text" ng-bind="draftText"></span>&nbsp; <i class="zmdi zmdi-more-vert"></i></a>
                        <ul>
                            <li>
                                <a ng-click="forceDraftSave()" class="text-pos"><i class="zmdi zmdi-save"></i>Save Draft</a>
                            </li>
                            <li ng-if="isNewPageDraft">
                                <a href="{{$model->getUrl()}}/delete" class="text-neg"><i class="zmdi zmdi-delete"></i>Delete Draft</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-4 faded">
                    <div class="action-buttons" ng-cloak>

                        <button type="button" ng-if="isUpdateDraft" ng-click="discardDraft()" class="text-button text-neg"><i class="zmdi zmdi-close-circle"></i>Discard Draft</button>
                        <button type="submit" id="save-button" class="text-button  text-pos"><i class="zmdi zmdi-floppy"></i>Save Page</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="title-input page-title clearfix" ng-non-bindable>
        <div class="input">
            @include('form/text', ['name' => 'name', 'placeholder' => 'Page Title'])
        </div>
    </div>

    <div class="edit-area flex-fill flex">
        @if(setting('app-editor') === 'wysiwyg')
            <textarea id="html-editor" tinymce="editorOptions" mce-change="editorChange" mce-model="editContent"  name="html" rows="5"
                      @if($errors->has('html')) class="neg" @endif>@if(isset($model) || old('html')){{htmlspecialchars( old('html') ? old('html') : $model->html)}}@endif</textarea>
            @if($errors->has('html'))
                <div class="text-neg text-small">{{ $errors->first('html') }}</div>
            @endif
        @endif

        @if(setting('app-editor') === 'markdown')
            <div id="markdown-editor" markdown-editor class="flex-fill flex">

                <div class="markdown-editor-wrap">
                    <div class="editor-toolbar">
                        <span class="float left">Editor</span>
                        <div class="float right buttons">
                            <button class="text-button" type="button" data-action="insertImage"><i class="zmdi zmdi-image"></i>Insert Image</button>
                        </div>
                    </div>
                    <textarea markdown-input md-change="editorChange" id="markdown-editor-input" md-model="editContent"  name="markdown" rows="5"
                              @if($errors->has('markdown')) class="neg" @endif>@if(isset($model) || old('markdown')){{htmlspecialchars( old('markdown') ? old('markdown') : ($model->markdown === '' ? $model->html : $model->markdown))}}@endif</textarea>
                </div>

                <div class="markdown-editor-wrap">
                    <div class="editor-toolbar">
                        <div class="">Preview</div>
                    </div>
                    <div class="markdown-display">
                        <div class="page-content" ng-bind-html="displayContent"></div>
                    </div>
                </div>

            </div>

            <input type="hidden" name="html" ng-value="displayContent">

            @if($errors->has('markdown'))
                <div class="text-neg text-small">{{ $errors->first('markdown') }}</div>
            @endif

        @endif
    </div>
</div>