<div class="page-editor flex-fill flex" id="page-editor"
     drafts-enabled="{{ $draftsEnabled ? 'true' : 'false' }}"
     drawio-enabled="{{ config('services.drawio') ? 'true' : 'false' }}"
     editor-type="{{ setting('app-editor') }}"
     page-id="{{ $model->id ?? 0 }}"
     text-direction="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"
     page-new-draft="{{ $model->draft ?? 0 }}"
     page-update-draft="{{ $model->isDraft ?? 0 }}">

    @exposeTranslations([
        'entities.pages_editing_draft',
        'entities.pages_editing_page',
        'errors.page_draft_autosave_fail',
        'entities.pages_editing_page',
        'entities.pages_draft_discarded',
        'entities.pages_edit_set_changelog',
    ])

    {{--Header Bar--}}
    <div class="primary-background-light toolbar page-edit-toolbar">
        <div class="grid third no-break v-center">

            <div class="action-buttons text-left px-m py-xs">
                <a href="{{ back()->getTargetUrl() }}" class="text-button text-primary">@icon('back')<span class="hide-under-l">{{ trans('common.back') }}</span></a>
            </div>

            <div class="text-center px-m py-xs">
                <div v-show="draftsEnabled" dropdown dropdown-move-menu class="dropdown-container draft-display text">
                    <a dropdown-toggle aria-haspopup="true" aria-expanded="false" title="{{ trans('entities.pages_edit_draft_options') }}" class="text-primary text-button"><span class="faded-text" v-text="draftText"></span>&nbsp; @icon('more')</a>
                    @icon('check-circle', ['class' => 'text-pos draft-notification svg-icon', ':class' => '{visible: draftUpdated}'])
                    <ul class="dropdown-menu" role="menu">
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
                <div dropdown dropdown-move-menu class="dropdown-container">
                    <a dropdown-toggle class="text-primary text-button">@icon('edit') <span v-text="changeSummaryShort"></span></a>
                    <ul class="wide dropdown-menu">
                        <li class="px-l py-m">
                            <p class="text-muted pb-s">{{ trans('entities.pages_edit_enter_changelog_desc') }}</p>
                            <input name="summary" id="summary-input" type="text" placeholder="{{ trans('entities.pages_edit_enter_changelog') }}" v-model="changeSummary" />
                        </li>
                    </ul>
                    <span>{{-- Prevents button jumping on menu show --}}</span>
                </div>

                <button type="submit" id="save-button" class="float-left text-primary text-button text-pos-hover hide-under-m">@icon('save')<span>{{ trans('entities.pages_save') }}</span></button>
            </div>
        </div>
    </div>

    {{--Title input--}}
    <div class="title-input page-title clearfix" v-pre>
        <div class="input">
            @include('form.text', ['name' => 'name', 'placeholder' => trans('entities.pages_title')])
        </div>
    </div>

    {{--Editors--}}
    <div class="edit-area flex-fill flex">

        {{--WYSIWYG Editor--}}
        @if(setting('app-editor') === 'wysiwyg')
            @include('pages.wysiwyg-editor', ['model' => $model])
        @endif

        {{--Markdown Editor--}}
        @if(setting('app-editor') === 'markdown')
            @include('pages.markdown-editor', ['model' => $model])
        @endif

    </div>

    <button type="submit" id="save-button-mobile" title="{{ trans('entities.pages_save') }}" class="text-primary text-button hide-over-m page-save-mobile-button">@icon('save')</button>
</div>