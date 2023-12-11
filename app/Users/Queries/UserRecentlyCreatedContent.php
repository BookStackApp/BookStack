<?php

namespace BookStack\Users\Queries;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Get the recently created content for the provided user.
 */
class UserRecentlyCreatedContent
{
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
            'pages'    => $query(Page::visible()->where('draft', '=', false)),
            'chapters' => $query(Chapter::visible()),
            'books'    => $query(Book::visible()),
            'shelves'  => $query(Bookshelf::visible()),
        ];
    }
}
