<?php

namespace BookStack\Users\Queries;

use BookStack\Entities\Queries\EntityQueries;
use BookStack\Users\Models\User;

/**
 * Get asset created counts for the given user.
 */
class UserContentCounts
{
    public function __construct(
        protected EntityQueries $queries,
    ) {
    }


    /**
     * @return array{pages: int, chapters: int, books: int, shelves: int}
     */
    public function run(User $user): array
    {
        $createdBy = ['created_by' => $user->id];

        return [
            'pages'    => $this->queries->pages->visibleForList()->where($createdBy)->count(),
            'chapters' => $this->queries->chapters->visibleForList()->where($createdBy)->count(),
            'books'    => $this->queries->books->visibleForList()->where($createdBy)->count(),
            'shelves'  => $this->queries->shelves->visibleForList()->where($createdBy)->count(),
        ];
    }
}
