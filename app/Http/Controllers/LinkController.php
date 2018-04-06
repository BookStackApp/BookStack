<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Repos\EntityRepo;
use BookStack\Repos\UserRepo;
use BookStack\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Views;

class LinkController extends Controller
{
    protected $userRepo;
    protected $entityRepo;
    protected $exportService;

    public function __construct(EntityRepo $entityRepo, UserRepo $userRepo, ExportService $exportService)
    {
        $this->entityRepo = $entityRepo;
        $this->userRepo = $userRepo;
        $this->exportService = $exportService;
        parent::__construct();
    }

    public function create($bookSlug, $chapterSlug = null)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $chapter = $chapterSlug ? $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug) : null;
        $parent = $chapter ? $chapter : $book;
        $this->checkOwnablePermission('page-create', $parent);

        $this->setPageTitle(trans('entities.link_create'));

        // Redirect to draft edit screen if signed in
        if ($this->signedIn) {
            return view('links/create', ['chapter' => $chapter, 'book' => $book, 'parent' => $parent]);
        }
    }

    public function store(Request $request, $bookSlug, $chapterSlug = false)
    {
        
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'link_to' => 'required|string',            
        ]);

        $input = $request->all();
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        if ($chapterSlug) {
            $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
            $parent = $chapter;
            $input['book_id'] = $book->id;
        } else {
            $parent = $book;
        }
        $this->checkOwnablePermission('page-create', $book);
        $input['priority'] = $this->entityRepo->getNewBookPriority($book);
        $link = $this->entityRepo->createFromInput('link', $input, $parent);
        
        return redirect($link->getUrl());
    }

    public function show($bookSlug, $linkSlug)
    {
        try {
            $link = $this->entityRepo->getBySlug('link', $linkSlug, $bookSlug);
        } catch (NotFoundException $e) {
            $link = $this->entityRepo->getPageByOldSlug($linkSlug, $bookSlug);
            if ($link === null) {
                throw $e;
            }
            return redirect($link->getUrl());
        }

        $sidebarTree = $this->entityRepo->getBookChildren($link->book);
        Views::add($link);
        $this->setPageTitle($link->getShortName());
        return view('links/show', [
            'link' => $link,
            'book' => $link->book,
            'current' => $link,
            'sidebarTree' => $sidebarTree
        ]);
    }

    public function showDelete($bookSlug, $linkSlug)
    {
        $link = $this->entityRepo->getBySlug('link', $linkSlug, $bookSlug);
        $this->checkOwnablePermission('page-delete', $link);
        $this->setPageTitle(trans('entities.links_delete_named', ['linkName'=>$link->getShortName()]));
        return view('links/delete', ['book' => $link->book, 'link' => $link, 'current' => $link]);
    }

    public function edit($bookSlug, $linkSlug)
    {
        $link = $this->entityRepo->getBySlug('link', $linkSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $link);
        $this->setPageTitle(trans('entities.links_editing_named', ['linkName'=>$link->getShortName()]));

        return view('links/edit', [
            'link' => $link,
            'book' => $link->book,
            'current' => $link
        ]);
    }

    public function update(Request $request, $bookSlug, $linkSlug)
    {
        $link = $this->entityRepo->getBySlug('link', $linkSlug, $bookSlug);
        $this->checkOwnablePermission('page-update', $link);

        $this->entityRepo->updateFromInput('link', $link, $request->all());
        return redirect($link->getUrl());
    }

    public function destroy($bookSlug, $linkSlug)
    {
        $link = $this->entityRepo->getBySlug('link', $linkSlug, $bookSlug);
        $book = $link->book;
        $this->checkOwnablePermission('page-delete', $link);
        $this->entityRepo->destroyLink($link);
        return redirect($book->getUrl());
    }

}

?>