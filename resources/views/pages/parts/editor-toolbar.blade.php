<div class="primary-background-light toolbar page-edit-toolbar">
    <div class="grid third no-break v-center">

        <div class="action-buttons text-left px-m py-xs">
            <a href="{{ $isDraft ? $page->getParent()->getUrl() : $page->getUrl() }}"
               class="text-button text-primary">@icon('back')<span class="hide-under-l">{{ trans('common.back') }}</span></a>
        </div>

        <div class="text-center px-m">
            <div component="dropdown"
                 option:dropdown:move-menu="true"
                 class="dropdown-container draft-display text {{ $draftsEnabled ? '' : 'hidden' }}">
                <button type="button" refs="dropdown@toggle" aria-haspopup="true" aria-expanded="false" title="{{ trans('entities.pages_edit_draft_options') }}" class="text-primary text-button py-s px-m"><span refs="page-editor@draftDisplay" class="faded-text"></span>&nbsp; @icon('more')</button>
                @icon('check-circle', ['class' => 'text-pos draft-notification svg-icon', 'refs' => 'page-editor@draftDisplayIcon'])
                <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                    <li>
                        <button refs="page-editor@saveDraft" type="button" class="text-pos icon-item">
                            @icon('save')
                            <div>{{ trans('entities.pages_edit_save_draft') }}</div>
                        </button>
                    </li>
                    @if($isDraft)
                        <li>
                            <a href="{{ $model->getUrl('/delete') }}" class="text-neg icon-item">
                                @icon('delete')
                                {{ trans('entities.pages_edit_delete_draft') }}
                            </a>
                        </li>
                    @endif
                    <li refs="page-editor@discardDraftWrap" class="{{ $isDraft ? '' : 'hidden' }}">
                        <button refs="page-editor@discardDraft" type="button" class="text-neg icon-item">
                            @icon('cancel')
                            <div>{{ trans('entities.pages_edit_discard_draft') }}</div>
                        </button>
                    </li>
                    @if(userCan('editor-change'))
                        <li>
                            @if($editor === 'wysiwyg')
                                <a href="{{ $model->getUrl($isDraft ? '' : '/edit') }}?editor=markdown-clean" refs="page-editor@changeEditor" class="icon-item">
                                    @icon('swap-horizontal')
                                    <div>
                                        {{ trans('entities.pages_edit_switch_to_markdown') }}
                                        <br>
                                        <small>{{ trans('entities.pages_edit_switch_to_markdown_clean') }}</small>
                                    </div>
                                </a>
                                <a href="{{ $model->getUrl($isDraft ? '' : '/edit') }}?editor=markdown-stable" refs="page-editor@changeEditor" class="icon-item">
                                    @icon('swap-horizontal')
                                    <div>
                                        {{ trans('entities.pages_edit_switch_to_markdown') }}
                                        <br>
                                        <small>{{ trans('entities.pages_edit_switch_to_markdown_stable') }}</small>
                                    </div>
                                </a>
                            @else
                                <a href="{{ $model->getUrl($isDraft ? '' : '/edit') }}?editor=wysiwyg" refs="page-editor@changeEditor" class="icon-item">
                                    @icon('swap-horizontal')
                                    <div>{{ trans('entities.pages_edit_switch_to_wysiwyg') }}</div>
                                </a>
                            @endif
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="action-buttons px-m py-xs">
            <div component="dropdown" dropdown-move-menu class="dropdown-container">
                <button refs="dropdown@toggle" type="button" aria-haspopup="true" aria-expanded="false" class="text-primary text-button">@icon('edit') <span refs="page-editor@changelogDisplay">{{ trans('entities.pages_edit_set_changelog') }}</span></button>
                <ul refs="dropdown@menu" class="wide dropdown-menu">
                    <li class="px-l py-m">
                        <p class="text-muted pb-s">{{ trans('entities.pages_edit_enter_changelog_desc') }}</p>
                        <input refs="page-editor@changelogInput"
                               name="summary"
                               id="summary-input"
                               type="text"
                               placeholder="{{ trans('entities.pages_edit_enter_changelog') }}" />
                    </li>
                </ul>
                <span>{{-- Prevents button jumping on menu show --}}</span>
            </div>

            <button type="submit" id="save-button" class="float-left text-primary text-button text-pos-hover hide-under-m">@icon('save')<span>{{ trans('entities.pages_save') }}</span></button>
        </div>
    </div>
</div>