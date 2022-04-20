<div component="page-editor" class="page-editor flex-fill flex"
     option:page-editor:drafts-enabled="{{ $draftsEnabled ? 'true' : 'false' }}"
     @if(config('services.drawio'))
        drawio-url="{{ is_string(config('services.drawio')) ? config('services.drawio') : 'https://embed.diagrams.net/?embed=1&proto=json&spin=1' }}"
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
     option:page-editor:set-changelog-text="{{ trans('entities.pages_edit_set_changelog') }}">

    {{--Header Toolbar--}}
    @include('pages.parts.editor-toolbar', ['model' => $model, 'editor' => $editor, 'isDraft' => $isDraft, 'draftsEnabled' => $draftsEnabled])

    {{--Title input--}}
    <div class="title-input page-title clearfix">
        <div refs="page-editor@titleContainer" class="input">
            @include('form.text', ['name' => 'name', 'model' => $model, 'placeholder' => trans('entities.pages_title')])
        </div>
    </div>

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

    <button type="submit"
            id="save-button-mobile"
            title="{{ trans('entities.pages_save') }}"
            class="text-primary text-button hide-over-m page-save-mobile-button">@icon('save')</button>
</div>