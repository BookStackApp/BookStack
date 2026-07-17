{{--
$target - CSS selector of items to expand
$key - Unique key for checking existing stored state.
--}}
<?php $isOpen = setting()->getForCurrentUser('section_expansion#'. $key); ?>
<button component="expand-toggle"
        option:expand-toggle:target-selector="{{ $target }}"
        option:expand-toggle:update-endpoint="{{ url('/preferences/change-expansion/' . $key) }}"
        option:expand-toggle:is-open="{{ $isOpen ? 'true' : 'false' }}"
        type="button"
        class="icon-list-item {{ $classes ?? '' }}">
    <span>@icon('expand-text')</span>
    <span>{{ trans('common.toggle_details') }}</span>
</button>
@if($isOpen)
    @push('head')
        <style>
            {{ $target }} {display: block;}
        </style>
    @endpush
@endif