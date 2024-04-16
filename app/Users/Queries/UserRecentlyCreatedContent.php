<?php

namespace BookStack\Users\Queries;

use BookStack\Entities\Queries\EntityQueries;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Get the recently created content for the provided user.
 */
class UserRecentlyCreatedContent
{
    public function __construct(
        protected EntityQueries $queries,
    ) {
    }

    /**
     * @return array{pages: Collection, chapters: Collection, books: Collection, shelves: Collection}
     */
    public function run(User $user, int $count): array
    {
        $query = function (Builder $query) use ($user, $count) {
            return $query->orderBy('created_at', 'desc')
                ->where('created_by', '=', $user->id)
                ->take($count)
                ->get();
        };

        return [
            'pages'    => $query($this->queries->pages->visibleForList()->where('draft', '=', false)),
            'chapters' => $query($this->queries->chapters->visibleForList()),
            'books'    => $query($this->queries->books->visibleForList()),
            'shelves'  => $query($this->queries->shelves->visibleForList()),
        ];
    }
}
