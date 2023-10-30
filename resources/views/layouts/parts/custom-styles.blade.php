@php
    $settingSuffix = setting()->getForCurrentUser('dark-mode-enabled') ? '-dark' : '';
@endphp
<style>
    :root {
        --color-primary: {{ setting('app-color' . $settingSuffix) }};
        --color-primary-light: {{ setting('app-color-light' . $settingSuffix) }};
        --color-link: {{ setting('link-color' . $settingSuffix) }};
        --color-bookshelf: {{ setting('bookshelf-color' . $settingSuffix)}};
        --color-book: {{ setting('book-color' . $settingSuffix)}};
        --color-chapter: {{ setting('chapter-color' . $settingSuffix)}};
        --color-page: {{ setting('page-color' . $settingSuffix)}};
        --color-page-draft: {{ setting('page-draft-color' . $settingSuffix)}};
    }
</style>
