@if(Setting::get('app-color'))
    <style>
        header, #back-to-top, .primary-background {
            background-color: {{ Setting::get('app-color') }};
        }
        .faded-small, .primary-background-light {
            background-color: {{ Setting::get('app-color-light') }};
        }
        .button-base, .button, input[type="button"], input[type="submit"] {
            background-color: {{ Setting::get('app-color') }};
        }
        .button-base:hover, .button:hover, input[type="button"]:hover, input[type="submit"]:hover, .button:focus {
            background-color: {{ Setting::get('app-color') }};
        }
        .nav-tabs a.selected, .nav-tabs .tab-item.selected {
            border-bottom-color: {{ Setting::get('app-color') }};
        }
        p.primary:hover, p .primary:hover, span.primary:hover, .text-primary:hover, a, a:hover, a:focus, .text-button, .text-button:hover, .text-button:focus {
            color: {{ Setting::get('app-color') }};
        }
    </style>
@endif