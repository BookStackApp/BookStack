
<div class="page-editor flex-fill flex" id="page-editor" drafts-enabled="{{ $draftsEnabled ? 'true' : 'false' }}" editor-type="{{ setting('app-editor') }}" page-id="{{ $model->id or 0 }}" page-new-draft="{{ $model->draft or 0 }}" page-update-draft="{{ $model->isDraft or 0 }}">

    {{ csrf_field() }}

    {{--Header Bar--}}
    <div class="faded-small toolbar">
        <div class="container fluid">
            <div class="row">
                <div class="col-sm-4 faded">
                    <div class="action-buttons text-left">
                        <a href="{{ back()->getTargetUrl() }}" class="text-button text-primary"><i class="zmdi zmdi-arrow-left"></i>{{ trans('common.back') }}</a>
                        <a onclick="$('body>header').slideToggle();" class="text-button text-primary"><i class="zmdi zmdi-swap-vertical"></i>{{ trans('entities.pages_edit_toggle_header') }}</a>
                    </div>
                </div>
                <div class="col-sm-4 faded text-center">

                    <div v-show="draftsEnabled" dropdown class="dropdown-container draft-display">
                        <a dropdown-toggle class="text-primary text-button"><span class="faded-text" v-text="draftText"></span>&nbsp; <i class="zmdi zmdi-more-vert"></i></a>
                        <i class="zmdi zmdi-check-circle text-pos draft-notification" :class="{visible: draftUpdated}"></i>
                        <ul>
                            <li>
                                <a @click="saveDraft()" class="text-pos"><i class="zmdi zmdi-save"></i>{{ trans('entities.pages_edit_save_draft') }}</a>
                            </li>
                            <li v-if="isNewDraft">
                                <a href="{{ $model->getUrl('/delete') }}" class="text-neg"><i class="zmdi zmdi-delete"></i>{{ trans('entities.pages_edit_delete_draft') }}</a>
                            </li>
                            <li v-if="isUpdateDraft">
                                <a type="button" @click="discardDraft" class="text-neg"><i class="zmdi zmdi-close-circle"></i>{{ trans('entities.pages_edit_discard_draft') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-4 faded">
                    <div class="action-buttons" v-cloak>
                        <div dropdown class="dropdown-container">
                            <a dropdown-toggle class="text-primary text-button"><i class="zmdi zmdi-edit"></i> <span v-text="changeSummaryShort"></span></a>
                            <ul class="wide">
                                <li class="padded">
                                    <p class="text-muted">{{ trans('entities.pages_edit_enter_changelog_desc') }}</p>
                                    <input name="summary" id="summary-input" type="text" placeholder="{{ trans('entities.pages_edit_enter_changelog') }}" v-model="changeSummary" />
                                </li>
                            </ul>
                        </div>

                        <button type="submit" id="save-button" class="text-button text-pos"><i class="zmdi zmdi-floppy"></i>{{ trans('entities.pages_save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Title input--}}
    <div class="title-input page-title clearfix" v-pre>
        <div class="input">
            @include('form/text', ['name' => 'name', 'placeholder' => trans('entities.pages_title')])
        </div>
    </div>

    {{--Editors--}}
    <div class="edit-area flex-fill flex">

        {{--WYSIWYG Editor--}}
        @if(setting('app-editor') === 'wysiwyg')
            <div wysiwyg-editor class="flex-fill flex">
                <textarea id="html-editor"  name="html" rows="5" v-pre
                    @if($errors->has('html')) class="neg" @endif>@if(isset($model) || old('html')){{htmlspecialchars( old('html') ? old('html') : $model->html)}}@endif</textarea>
            </div>

            @if($errors->has('html'))
                <div class="text-neg text-small">{{ $errors->first('html') }}</div>
            @endif
        @endif

        {{--Markdown Editor--}}
        @if(setting('app-editor') === 'markdown')
            <div v-pre id="markdown-editor" markdown-editor class="flex-fill flex code-fill">

                <div class="markdown-editor-wrap">
                    <div class="editor-toolbar">
                        <span class="float left">{{ trans('entities.pages_md_editor') }}</span>
                        <div class="float right buttons">
                            <button class="text-button" type="button" data-action="insertImage"><i class="zmdi zmdi-image"></i>{{ trans('entities.pages_md_insert_image') }}</button>
                            &nbsp;|&nbsp;
                            <button class="text-button" type="button" data-action="insertLink"><i class="zmdi zmdi-link"></i>{{ trans('entities.pages_md_insert_link') }}</button>
                        </div>
                    </div>

                    <div markdown-input class="flex flex-fill">
                        <textarea  id="markdown-editor-input"  name="markdown" rows="5"
                            @if($errors->has('markdown')) class="neg" @endif>@if(isset($model) || old('markdown')){{htmlspecialchars( old('markdown') ? old('markdown') : ($model->markdown === '' ? $model->html : $model->markdown))}}@endif</textarea>
                    </div>

                </div>

                <div class="markdown-editor-wrap">
                    <div class="editor-toolbar">
                        <div class="">{{ trans('entities.pages_md_preview') }}</div>
                    </div>
                    <div class="markdown-display page-content">
                    </div>
                </div>
                <input type="hidden" name="html"/>

            </div>



            @if($errors->has('markdown'))
                <div class="text-neg text-small">{{ $errors->first('markdown') }}</div>
            @endif
        @endif

    </div>
</div>