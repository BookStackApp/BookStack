<?php

namespace BookStack\Auth\Queries;

use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;

/**
 * Get asset created counts for the given user.
 */
class UserContentCounts
{
    /**
     * @return array{pages: int, chapters: int, books: int, shelves: int}
     */
    public function run(User $user): array
    {
        $createdBy = ['created_by' => $user->id];

        return [
            'pages'    => Page::visible()->where($createdBy)->count(),
            'chapters' => Chapter::visible()->where($createdBy)->count(),
            'books'    => Book::visible()->where($createdBy)->count(),
            'shelves'  => Bookshelf::visible()->where($createdBy)->count(),
        ];
    }
}