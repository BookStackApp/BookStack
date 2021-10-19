<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\View;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\NextPreviousContentLocator;
use BookStack\Entities\Tools\PermissionsUpdater;
use BookStack\Exceptions\MoveOperationException;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
