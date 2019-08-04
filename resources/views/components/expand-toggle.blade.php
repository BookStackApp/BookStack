{{--
$target - CSS selector of items to expand
$key - Unique key for checking existing stored state.
--}}
<?php $isOpen = setting()->getForCurrentUser('section_expansion#'. $key); ?>
<a expand-toggle="{{ $target }}"
   expand-toggle-update-endpoint="{{ url('/settings/users/'. $currentUser->id .'/update-expansion-preference/' . $key) }}"
   expand-toggle-is-open="{{ $isOpen ? 'yes' : 'no' }}"
   class="text-muted icon-list-item text-primary">
    <span>@icon('expand-text')</span>
    <span>{{ trans('common.toggle_details') }}</span>
</a>
@if($isOpen)
    @push('head')
        <style>
            {{ $target }} {display: block;}
        </style>
    @endpush
@endif