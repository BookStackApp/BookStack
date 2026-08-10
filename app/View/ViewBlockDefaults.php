<?php

namespace BookStack\View;

class ViewBlockDefaults
{
    /**
     * @var array<string, array<string, class-string<ViewBlockInterface>[]>>
     */
    protected static array $defaults = [
        'home-default' => [
            'left' => [
                ViewBlocks\HomeRecentDrafts::class,
                ViewBlocks\HomeRecentlyViewedOrRecentBooks::class,
            ],
            'center' => [
                ViewBlocks\HomeTopFavourites::class,
                ViewBlocks\HomeRecentlyUpdatedPages::class,
            ],
            'right' => [
                ViewBlocks\HomeRecentActivity::class,
            ],
        ],
        'home-non-default' => [
            'left' => [
                ViewBlocks\HomeRecentDrafts::class,
                ViewBlocks\HomeTopFavourites::class,
                ViewBlocks\HomeRecentlyViewedOrRecentBooks::class,
                ViewBlocks\HomeRecentlyUpdatedPages::class,
                ViewBlocks\HomeRecentActivity::class,
            ],
            'right' => [
                ViewBlocks\HomeActions::class,
            ],
        ],
        'shelves-index' => [
            'left' => [
                ViewBlocks\ShelvesIndexRecents::class,
                ViewBlocks\ShelvesIndexPopular::class,
                ViewBlocks\ShelvesIndexNew::class,
            ],
            'right' => [
                ViewBlocks\ShelvesIndexActions::class,
            ],
        ],
        'shelves-show' => [
            'left' => [
                ViewBlocks\ShelvesShowTags::class,
                ViewBlocks\ShelvesShowDetails::class,
                ViewBlocks\ShelvesShowActivity::class,
            ],
            'right' => [
                ViewBlocks\ShelvesShowActions::class,
            ],
        ],
        'books-index' => [
            'left' => [
                ViewBlocks\BooksIndexRecents::class,
                ViewBlocks\BooksIndexPopular::class,
                ViewBlocks\BooksIndexNew::class,
            ],
            'right' => [
                ViewBlocks\BooksIndexActions::class,
            ],
        ],
        'books-show' => [
            'left' => [
                ViewBlocks\BooksShowSearchForm::class,
                ViewBlocks\BooksShowTags::class,
                ViewBlocks\BooksShowShelves::class,
                ViewBlocks\BooksShowActivity::class,
            ],
            'right' => [
                ViewBlocks\BooksShowDetails::class,
                ViewBlocks\BooksShowActions::class,
            ],
        ],
        'chapters-show' => [
            'left' => [
                ViewBlocks\ChaptersShowSearchForm::class,
                ViewBlocks\ChaptersShowTags::class,
                ViewBlocks\ChaptersShowBookTree::class,
            ],
            'right' => [
                ViewBlocks\ChaptersShowDetails::class,
                ViewBlocks\ChaptersShowActions::class,
            ],
        ],
        'pages-show' => [
            'left' => [
                ViewBlocks\PagesShowTags::class,
                ViewBlocks\PagesShowAttachments::class,
                ViewBlocks\PagesShowPageNav::class,
                ViewBlocks\PagesShowBookTree::class,
            ],
            'right' => [
                ViewBlocks\PagesShowDetails::class,
                ViewBlocks\PagesShowActions::class,
            ],
        ],
    ];

    /**
     * Get the default view blocks for the given location.
     */
    public static function getForLocation(string $location): array
    {
        return self::$defaults[$location] ?? [];
    }

    /**
     * Get the locations for all default blocks.
     */
    public static function getLocations(): array
    {
        return array_keys(self::$defaults);
    }

    public static function getLocationLabels(): array
    {
        return [
            'home-default' => trans('common.homepage'),
            'home-non-default' => trans('preferences.layouts_home_non_default'),
            'shelves-index' => trans('entities.shelves'),
            'shelves-show' => trans('entities.shelf'),
            'books-index' => trans('entities.books'),
            'books-show' => trans('entities.book'),
            'chapters-show' => trans('entities.chapter'),
            'pages-show' => trans('entities.page'),
        ];
    }
}
