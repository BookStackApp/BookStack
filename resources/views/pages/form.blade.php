
<div class="page-editor flex-fill flex" id="page-editor"
     drafts-enabled="{{ $draftsEnabled ? 'true' : 'false' }}"
     drawio-enabled="{{ config('services.drawio') ? 'true' : 'false' }}"
     editor-type="{{ setting('app-editor') }}"
     page-id="{{ $model->id ?? 0 }}"
     text-direction="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"
     page-new-draft="{{ $model->draft ?? 0 }}"
     page-update-draft="{{ $model->isDraft ?? 0 }}">

    {{ csrf_field() }}

    {{--Header Bar--}}
    <div class="primary-background-light toolbar">
        <div class="grid third v-center">

            <div class="action-buttons text-left px-m py-xs">
                <a href="{{ back()->getTargetUrl() }}" class="text-button text-primary">@icon('back'){{ trans('common.back') }}</a>
                <a onclick="$('body>header').slideToggle();" class="text-button text-primary">@icon('swap-vertical'){{ trans('entities.pages_edit_toggle_header') }}</a>
            </div>

            <div class="text-center px-m py-xs">
                <div v-show="draftsEnabled" dropdown class="dropdown-container draft-display text">
                    <a dropdown-toggle class="text-primary text-button"><span class="faded-text" v-text="draftText"></span>&nbsp; @icon('more')</a>
                    @icon('check-circle', ['class' => 'text-pos draft-notification svg-icon', ':class' => '{visible: draftUpdated}'])
                    <ul>
                        <li>
                            <a @click="saveDraft()" class="text-pos">@icon('save'){{ trans('entities.pages_edit_save_draft') }}</a>
                        </li>
                        <li v-if="isNewDraft">
                            <a href="{{ $model->getUrl('/delete') }}" class="text-neg">@icon('delete'){{ trans('entities.pages_edit_delete_draft') }}</a>
                        </li>
                        <li v-if="isUpdateDraft">
                            <a type="button" @click="discardDraft" class="text-neg">@icon('cancel'){{ trans('entities.pages_edit_discard_draft') }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="action-buttons px-m py-xs" v-cloak>
                <div dropdown class="dropdown-container">
                    <a dropdown-toggle class="text-primary text-button">@icon('edit') <span v-text="changeSummaryShort"></span></a>
                    <ul class="wide">
                        <li class="padded">
                            <p class="text-muted pb-s">{{ trans('entities.pages_edit_enter_changelog_desc') }}</p>
                            <input name="summary" id="summary-input" type="text" placeholder="{{ trans('entities.pages_edit_enter_changelog') }}" v-model="changeSummary" />
                        </li>
                    </ul>
                </div>

                <button type="submit" id="save-button" class="text-button text-pos">@icon('save'){{ trans('entities.pages_save') }}</button>
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
                            @if(config('services.drawio'))
                                <button class="text-button" type="button" data-action="insertDrawing">@icon('drawing'){{ trans('entities.pages_md_insert_drawing') }}</button>
                                &nbsp;|&nbsp
                            @endif
                            <button class="text-button" type="button" data-action="insertImage">@icon('image'){{ trans('entities.pages_md_insert_image') }}</button>
                            &nbsp;|&nbsp;
                            <button class="text-button" type="button" data-action="insertLink">@icon('link'){{ trans('entities.pages_md_insert_link') }}</button>
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