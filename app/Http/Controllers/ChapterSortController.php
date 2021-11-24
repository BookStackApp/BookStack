<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\View;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Entities\Tools\BookContents;
use Illuminate\Http\Request;
use BookStack\Actions\ActivityType;
use BookStack\Exceptions\SortOperationException;
use BookStack\Facades\Activity;
use Throwable;

class ChapterSortController extends Controller
{
    protected $ChapterRepo;

    public function __construct(ChapterRepo $chapterRepo)
    {
        $this->chapterRepo = $chapterRepo;
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     */
    public function show(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $chapterChildren = (new BookContents($chapter->book))->getTree();

        $this->setPageTitle(trans('entities.chapters_sort_named', ['chapterName'=>$chapter->getShortName()]));

        return view('chapters.sort', ['chapter' => $chapter, 'current' => $chapter, 'chapterChildren' => $chapterChildren]);
    }

    /**
     * Shows the sort box for a single book.
     * Used via AJAX when loading in extra chapters to a sort.
     */
    public function showItem(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $chapterChildren = (new BookContents($chapter->book))->getTree();

        return view('chapters.parts.sort-box', ['chapter' => $chapter, 'chapterChildren' => $chapterChildren]);
    }

    /**
     * Sorts a book using a given mapping array.
     */
    public function update(Request $request, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        // Return if no map sent
        if (!$request->filled('sort-tree')) {
            return redirect($chapter->getUrl());
        }

        $sortMap = collect(json_decode($request->get('sort-tree')));
        $chapterContents = new BookContents($chapter->book);
        $chaptersInvolved = collect();

        try {
            $chaptersInvolved = $chapterContents->sortUsingMap($sortMap, $chapterSlug);
        } catch (SortOperationException $exception) {
            $this->showPermissionError();
        }

        // Rebuild permissions and add activity for involved chapters.
        $chaptersInvolved->each(function (Chapter $chapter) {
            Activity::addForEntity($chapter, ActivityType::CHAPTER_SORT);
        });

        return redirect($chapter->getUrl());
    }
}
