<style>
    :root {
        --color-primary: {{ setting('app-color') }};
        --color-primary-light: {{ setting('app-color-light') }};
        --color-link: {{ setting('link-color') }};
        --color-bookshelf: {{ setting('bookshelf-color') }};
        --color-book: {{ setting('book-color') }};
        --color-chapter: {{ setting('chapter-color') }};
        --color-page: {{ setting('page-color') }};
        --color-page-draft: {{ setting('page-draft-color') }};
    }
    :root.dark-mode {
        --color-primary: {{ setting('app-color-dark') }};
        --color-primary-light: {{ setting('app-color-light-dark') }};
        --color-link: {{ setting('link-color-dark') }};
        --color-bookshelf: {{ setting('bookshelf-color-dark') }};
        --color-book: {{ setting('book-color-dark') }};
        --color-chapter: {{ setting('chapter-color-dark') }};
        --color-page: {{ setting('page-color-dark') }};
        --color-page-draft: {{ setting('page-draft-color-dark') }};
    }
</style>
