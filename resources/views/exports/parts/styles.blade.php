{{-- Fetch in our standard export styles --}}
<style>
    @if (!app()->runningUnitTests())
        {!! file_get_contents(public_path('/dist/export-styles.css')) !!}
    @endif
</style>

{{-- Apply any additional styles that can't be applied via our standard SCSS export styles --}}
@if ($format === 'pdf')
    <style>
        /* Patches for CSS variable colors within PDF exports */
        a {
            color: {{ setting('app-link') }};
        }

        blockquote {
            border-left-color: {{ setting('app-color') }};
        }
    </style>
@endif