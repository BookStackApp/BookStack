<style>
    @if (!app()->runningUnitTests())
        {!! file_get_contents(public_path('/dist/export-styles.css')) !!}
    @endif
</style>

@if ($format === 'pdf')
    <style>

        /* PDF size adjustments */
        body {
            font-size: 14px;
            line-height: 1.2;
        }

        h1, h2, h3, h4, h5, h6 {
            line-height: 1.2;
        }

        table {
            max-width: 800px !important;
            font-size: 0.8em;
            width: 100% !important;
        }

        table td {
            width: auto !important;
        }

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

        @if($engine === \BookStack\Entities\Tools\PdfGenerator::ENGINE_DOMPDF)
        {{-- Fix for full width linked image sizes on DOMPDF --}}
        .page-content a > img {
            max-width: 700px;
        }
        {{-- Undoes the above for table images to prevent visually worse scenario, Awaiting next DOMPDF release for patch --}}
        .page-content td a > img {
            max-width: 100%;
        }
        @endif
    </style>
@endif