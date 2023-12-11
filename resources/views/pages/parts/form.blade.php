<div component="page-editor" class="page-editor page-editor-{{ $editor }} flex-fill flex"
     option:page-editor:drafts-enabled="{{ $draftsEnabled ? 'true' : 'false' }}"
     @if(config('services.drawio'))
        drawio-url="{{ is_string(config('services.drawio')) ? config('services.drawio') : 'https://embed.diagrams.net/?embed=1&proto=json&spin=1&configure=1' }}"
     @endif
     @if($model->name === trans('entities.pages_initial_name'))
        option:page-editor:has-default-title="true"
     @endif
     option:page-editor:editor-type="{{ $editor }}"
     option:page-editor:page-id="{{ $model->id ?? '0' }}"
     option:page-editor:page-new-draft="{{ $isDraft ? 'true' : 'false' }}"
     option:page-editor:draft-text="{{ ($isDraft || $isDraftRevision) ? trans('entities.pages_editing_draft') : trans('entities.pages_editing_page') }}"
     option:page-editor:autosave-fail-text="{{ trans('errors.page_draft_autosave_fail') }}"
     option:page-editor:editing-page-text="{{ trans('entities.pages_editing_page') }}"
     option:page-editor:draft-discarded-text="{{ trans('entities.pages_draft_discarded') }}"
     option:page-editor:draft-delete-text="{{ trans('entities.pages_draft_deleted') }}"
     option:page-editor:draft-delete-fail-text="{{ trans('errors.page_draft_delete_fail') }}"
     option:page-editor:set-changelog-text="{{ trans('entities.pages_edit_set_changelog') }}">

    {{--Header Toolbar--}}
    @include('pages.parts.editor-toolbar', ['model' => $model, 'editor' => $editor, 'isDraft' => $isDraft, 'draftsEnabled' => $draftsEnabled])

    <div class="flex flex-fill mx-s mb-s justify-center page-editor-page-area-wrap">
        <div class="page-editor-page-area flex-container-column flex flex-fill">
            {{--Title input--}}
            <div class="title-input page-title clearfix">
                <div refs="page-editor@titleContainer" class="input">
                    @include('form.text', ['name' => 'name', 'model' => $model, 'placeholder' => trans('entities.pages_title')])
                </div>
            </div>

            <div class="flex-fill flex">
                {{--Editors--}}
                <div class="edit-area flex-fill flex">

                    {{--WYSIWYG Editor--}}
                    @if($editor === 'wysiwyg')
                        @include('pages.parts.wysiwyg-editor', ['model' => $model])
                    @endif

                    {{--Markdown Editor--}}
                    @if($editor === 'markdown')
                        @include('pages.parts.markdown-editor', ['model' => $model])
                    @endif

                </div>

            </div>
        </div>

        <div class="relative flex-fill">
            @include('pages.parts.editor-toolbox')
        </div>
    </div>

    {{--Mobile Save Button--}}
    <button type="submit"
            id="save-button-mobile"
            title="{{ trans('entities.pages_save') }}"
            class="text-link text-button hide-over-m page-save-mobile-button">@icon('save')</button>

    {{--Editor Change Dialog--}}
    @component('common.confirm-dialog', ['title' => trans('entities.pages_editor_switch_title'), 'ref' => 'page-editor@switch-dialog'])
        <p>
            {{ trans('entities.pages_editor_switch_are_you_sure') }}
            <br>
            {{ trans('entities.pages_editor_switch_consider_following') }}
        </p>

        <ul>
            <li>{{ trans('entities.pages_editor_switch_consideration_a') }}</li>
            <li>{{ trans('entities.pages_editor_switch_consideration_b') }}</li>
            <li>{{ trans('entities.pages_editor_switch_consideration_c') }}</li>
        </ul>
    @endcomponent

    {{--Delete Draft Dialog--}}
    @component('common.confirm-dialog', ['title' => trans('entities.pages_edit_delete_draft'), 'ref' => 'page-editor@delete-draft-dialog'])
        <p>
            {{ trans('entities.pages_edit_delete_draft_confirm') }}
        </p>
    @endcomponent

    {{--Saved Drawing--}}
    @component('common.confirm-dialog', ['title' => trans('entities.pages_drawing_unsaved'), 'id' => 'unsaved-drawing-dialog'])
        <p>
            {{ trans('entities.pages_drawing_unsaved_confirm') }}
        </p>
    @endcomponent
</div>