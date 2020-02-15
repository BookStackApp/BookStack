<style>
    @if (!app()->environment('testing'))
        {!! file_get_contents(public_path('/dist/export-styles.css')) !!}
    @endif
</style>

@if ($format === 'pdf')
    <style>
        /* Patches for CSS variable colors */
        a {
            color: {{ setting('app-color') }};
        }

        blockquote {
            border-left-color: {{ setting('app-color') }};
        }

        /* Patches for content layout */
        .page-content .float {
            float: none !important;
        }

        .page-content img.align-left, .page-content img.align-right  {
            float: none !important;
            clear: both;
            display: block;
        }
    </style>
@endif