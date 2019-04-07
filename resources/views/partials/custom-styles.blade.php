<style id="custom-styles" data-color="{{ setting('app-color') }}" data-color-light="{{ setting('app-color-light') }}">
    .primary-background {
        background-color: {{ setting('app-color') }} !important;
    }
    .primary-background-light {
        background-color: {{ setting('app-color-light') }};
    }
    .button.primary, .button.primary:hover, .button.primary:active, .button.primary:focus {
        background-color: {{ setting('app-color') }};
        border-color: {{ setting('app-color') }};
    }
    .nav-tabs a.selected, .nav-tabs .tab-item.selected {
        border-bottom-color: {{ setting('app-color') }};
    }
    .text-primary, .text-primary-hover:hover, .text-primary:hover {
        color: {{ setting('app-color') }} !important;
        fill: {{ setting('app-color') }} !important;
    }

    a, a:hover, a:focus, .text-button, .text-button:hover, .text-button:focus {
        color: {{ setting('app-color') }};
        fill: {{ setting('app-color') }};
    }
</style>
