{{--
$target - CSS selector of items to expand
$key - Unique key for checking existing stored state.
--}}
<?php $isOpen = setting()->getForCurrentUser('section_expansion#'. $key); ?>
<button type="button" expand-toggle="{{ $target }}"
   expand-toggle-update-endpoint="{{ url('/preferences/change-expansion/' . $key) }}"
   expand-toggle-is-open="{{ $isOpen ? 'yes' : 'no' }}"
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