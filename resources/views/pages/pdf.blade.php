@extends('pages/export')

@section('head')
    <style>
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

        .page-content .float {
            float: none !important;
        }

        .page-content img.align-left, .page-content img.align-right  {
            float: none !important;
            clear: both;
            display: block;
        }

        .tag-display {
            min-width: 0;
            max-width: none;
            display: none;
        }

    </style>
@stop