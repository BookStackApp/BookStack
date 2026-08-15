{{--
$columnBlocks - array - Blocks to list
$label - string - Section title
$id - string - identifier for location/column
--}}
<div class="form-group flex">
    <label id="layout-blocks-{{ $id }}">{{ $label }}</label>
    <ul refs="layout-editor@column"
        data-column="{{ $id }}"
        aria-labelledby="layout-blocks-{{ $id }}"
        class="scroll-box layout-editor-column-{{ $id }}">
        <li class="text-muted empty-state px-m py-s italic text-small">{{ trans('preferences.layout_edit_empty') }}</li>
        @foreach($columnBlocks as $block)
            @include('settings.layouts.parts.block', ['block' => $block])
        @endforeach
    </ul>
</div>