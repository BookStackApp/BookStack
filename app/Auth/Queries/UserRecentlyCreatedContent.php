<?php

namespace BookStack\Auth\Queries;

use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
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
            'updates'  => PageRevision::query()->where('created_by', '=', $user->id)->where('revision_number', '!=', 1)->orderBy('updated_at', 'desc')->take($count)->get(), // The way this is setup seems to prevent "showPath" from working 
            'symbols'  => $query(Page::getVisiblePagesInBookshelf('symbols')->where('created_by', '=', $user->id)),
            'drafts'  => $query(Page::getVisiblePagesInBookshelf('contribute')->where('created_by', '=', $user->id)),
        ];
    }
}
