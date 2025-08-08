<div class="toolbar page-edit-toolbar py-xs">

    <div>
        <div class="inline block">
            <a href="{{ $isDraft ? $page->getParent()->getUrl() : $page->getUrl() }}"
               class="icon-list-item text-link"><span>@icon('back')</span><span class="hide-under-l">{{ trans('common.back') }}</span></a>
        </div>
    </div>

    <div class="text-center">
        <div component="dropdown"
             option:dropdown:move-menu="true"
             class="dropdown-container  draft-display text {{ $draftsEnabled ? '' : 'hidden' }}">
            <div class="flex-container-row items-center justify-center">
                <button type="button"
                        refs="dropdown@toggle"
                        aria-haspopup="menu"
                        aria-expanded="false"
                        title="{{ trans('entities.pages_edit_draft_options') }}"
                        class="text-link icon-list-item">
                    <span>@icon('time')</span>
                    <span><span refs="page-editor@draftDisplay" class="faded-text"></span>&nbsp; @icon('more')</span>
                </button>
                @icon('check-circle', ['class' => 'text-pos draft-notification svg-icon', 'refs' => 'page-editor@draftDisplayIcon'])
            </div>
            <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                <li>
                    <button refs="page-editor@saveDraft" type="button" role="menuitem" class="text-pos icon-item">
                        @icon('save')
                        <div>{{ trans('entities.pages_edit_save_draft') }}</div>
                    </button>
                </li>
                @if($isDraft)
                    <li>
                        <a href="{{ $model->getUrl('/delete') }}" role="menuitem" class="text-neg icon-item">
                            @icon('delete')
                            {{ trans('entities.pages_edit_delete_draft') }}
                        </a>
                    </li>
                @endif
                <li refs="page-editor@discard-draft-wrap" {{ $isDraftRevision ? '' : 'hidden' }}>
                    <button refs="page-editor@discard-draft" type="button" role="menuitem" class="text-warn icon-item">
                        @icon('cancel')
                        <div>{{ trans('entities.pages_edit_discard_draft') }}</div>
                    </button>
                </li>
                <li refs="page-editor@delete-draft-wrap" {{ $isDraftRevision ? '' : 'hidden' }}>
                    <button refs="page-editor@delete-draft" type="button" role="menuitem" class="text-neg icon-item">
                        @icon('delete')
                        <div>{{ trans('entities.pages_edit_delete_draft') }}</div>
                    </button>
                </li>
                @if(userCan('editor-change'))
                    <li role="presentation">
                        <hr>
                    </li>
                    <li>
                        @if($editor !== \BookStack\Entities\Tools\PageEditorType::Markdown)
                            <a href="{{ $model->getUrl($isDraft ? '' : '/edit') }}?editor=markdown-clean" refs="page-editor@changeEditor" role="menuitem" class="icon-item">
                                @icon('swap-horizontal')
                                <div>
                                    {{ trans('entities.pages_edit_switch_to_markdown') }}
                                    <br>
                                    <small>{{ trans('entities.pages_edit_switch_to_markdown_clean') }}</small>
                                </div>
                            </a>
                            <a href="{{ $model->getUrl($isDraft ? '' : '/edit') }}?editor=markdown-stable" refs="page-editor@changeEditor" role="menuitem" class="icon-item">
                                @icon('swap-horizontal')
                                <div>
                                    {{ trans('entities.pages_edit_switch_to_markdown') }}
                                    <br>
                                    <small>{{ trans('entities.pages_edit_switch_to_markdown_stable') }}</small>
                                </div>
                            </a>
                        @endif
                        @if($editor !== \BookStack\Entities\Tools\PageEditorType::WysiwygTinymce)
                            <a href="{{ $model->getUrl($isDraft ? '' : '/edit') }}?editor=wysiwyg" refs="page-editor@changeEditor" role="menuitem" class="icon-item">
                                @icon('swap-horizontal')
                                <div>{{ trans('entities.pages_edit_switch_to_wysiwyg') }}</div>
                            </a>
                        @endif
                        @if($editor !== \BookStack\Entities\Tools\PageEditorType::WysiwygLexical)
                            <a href="{{ $model->getUrl($isDraft ? '' : '/edit') }}?editor=wysiwyg2024" refs="page-editor@changeEditor" role="menuitem" class="icon-item">
                                @icon('swap-horizontal')
                                <div>
                                    {{ trans('entities.pages_edit_switch_to_new_wysiwyg') }}
                                    <br>
                                    <small>{{ trans('entities.pages_edit_switch_to_new_wysiwyg_desc') }}</small>
                                </div>
                            </a>
                        @endif
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="flex-container-row justify-flex-end gap-x-m items-center">
        <div component="dropdown"
             option:dropdown:move-menu="true"
             class="dropdown-container">
            <button refs="dropdown@toggle" type="button" aria-haspopup="true" aria-expanded="false" class="icon-list-item text-link">
                <span>@icon('edit')</span>
                <span refs="page-editor@changelogDisplay">{{ trans('entities.pages_edit_set_changelog') }}</span>
            </button>
            <ul refs="dropdown@menu" class="wide dropdown-menu">
                <li class="px-m py-s">
                    <p class="text-muted pb-s">{{ trans('entities.pages_edit_enter_changelog_desc') }}</p>
                    <textarea
                        refs="page-editor@changelogInput"
                        name="summary"
                        id="summary-input"
                        rows="2"
                        maxlength="180"
                        title="{{ trans('entities.pages_edit_enter_changelog') }}"
                        placeholder="{{ trans('entities.pages_edit_enter_changelog') }}"
                    ></textarea>
                    <small refs="page-editor@changelogCounter" class="text-muted mt-xs">0 / 180</small>
                </li>
            </ul>
            <span>{{-- Prevents button jumping on menu show --}}</span>
        </div>

        <div class="inline block">
            <button type="submit" id="save-button"
                    class="icon-list-item hide-under-m text-pos fill-width">
                <span>@icon('save')</span>
                <span>{{ trans('entities.pages_save') }}</span>
            </button>
        </div>
    </div>
</div>