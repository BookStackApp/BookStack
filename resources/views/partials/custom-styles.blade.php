<style id="custom-styles" data-color="{{ setting('app-color') }}" data-color-light="{{ setting('app-color-light') }}">
    :root {
        --color-primary: {{ setting('app-color') }};
        --color-primary-light: {{ setting('app-color-light') }};
        --color-bookshelf: {{ setting('bookshelf-color')}};
        --color-book: {{ setting('book-color')}};
        --color-chapter: {{ setting('chapter-color')}};
        --color-page: {{ setting('page-color')}};
        --color-page-draft: {{ setting('page-draft-color')}};
    }
</style>
