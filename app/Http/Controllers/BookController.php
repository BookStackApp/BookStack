<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityQueries;
use BookStack\Actions\ActivityType;
use BookStack\Actions\View;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Ratings_model;
use BookStack\Entities\Models\Counties_model;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\Cloner;
use BookStack\Entities\Tools\HierarchyTransformer;
use BookStack\Entities\Tools\PermissionsUpdater;
use BookStack\Entities\Tools\ShelfContext;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Facades\Activity;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class BookController extends Controller
{
    protected $bookRepo;
    protected $entityContextManager;

    public function __construct(ShelfContext $entityContextManager, BookRepo $bookRepo)
    {
        $this->bookRepo = $bookRepo;
        $this->entityContextManager = $entityContextManager;
    }

    /**
     * Display a listing of the book.
     */
    public function index()
    {
        $view = setting()->getForCurrentUser('books_view_type');
        $sort = setting()->getForCurrentUser('books_sort', 'name');
        $order = setting()->getForCurrentUser('books_sort_order', 'asc');

        $books = $this->bookRepo->getAllPaginated(18, $sort, $order);
        $recents = $this->isSignedIn() ? $this->bookRepo->getRecentlyViewed(4) : false;
        $popular = $this->bookRepo->getPopular(4);
        $new = $this->bookRepo->getRecentlyCreated(4);
        $this->entityContextManager->clearShelfContext();
        $this->setPageTitle(trans('entities.books'));
        return view('books.index1',[
            'books'   => $books,
            'recents' => $recents,
            'popular' => $popular,
            'new'     => $new,
            'view'    => $view,
            'sort'    => $sort,
            'order'   => $order,
        ]);
    }

    /**
     * Show the form for creating a new book.
     */
    public function create(string $shelfSlug = null)
    {
        $this->checkPermission('book-create-all');

        $bookshelf = null;
        if ($shelfSlug !== null) {
            $bookshelf = Bookshelf::visible()->where('slug', '=', $shelfSlug)->firstOrFail();
            $this->checkOwnablePermission('bookshelf-update', $bookshelf);
        }

        $this->setPageTitle(trans('entities.books_create'));

        return view('books.create', [
            'bookshelf' => $bookshelf,
        ]);
    }

    /**
     * Store a newly created book in storage.
     *
     * @throws ImageUploadException
     * @throws ValidationException
     */
    public function store(Request $request, string $shelfSlug = null)
    {
        $this->checkPermission('book-create-all');
        $validated = $this->validate($request, [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
            'tags'        => ['array'],
        ]);

        $bookshelf = null;
        if ($shelfSlug !== null) {
            $bookshelf = Bookshelf::visible()->where('slug', '=', $shelfSlug)->firstOrFail();
            $this->checkOwnablePermission('bookshelf-update', $bookshelf);
        }

        $book = $this->bookRepo->create($validated);

        if ($bookshelf) {
            $bookshelf->appendBook($book);
            Activity::add(ActivityType::BOOKSHELF_UPDATE, $bookshelf);
        }

        return redirect($book->getUrl());
    }

    /**
     * Display the specified book.
     */
    public function show(Request $request, ActivityQueries $activities, string $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $bookChildren = (new BookContents($book))->getTree(true);
        $bookParentShelves = $book->shelves()->scopes('visible')->get();
        // foreach ($books as $book) {
            
            $books = (new BookContents($book))->getTree(true);
        // }
        View::incrementFor($book);
        if ($request->has('shelf')) {
            $this->entityContextManager->setShelfContext(intval($request->get('shelf')));
        }

        $this->setPageTitle($book->getShortName());
//dd($books);
        return view('books.show1', [
            'bookd'              => $books,
            'book'              => $book,
            'current'           => $books,
            'bookChildren'      => $bookChildren,
            'bookParentShelves' => $bookParentShelves,
            'activity'          => $activities->entityActivity($book, 20, 1),
        ]);
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(string $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-update', $book);
        $this->setPageTitle(trans('entities.books_edit_named', ['bookName'=>$book->getShortName()]));

        return view('books.edit', ['book' => $book, 'current' => $book]);
    }

    /**
     * Update the specified book in storage.
     *
     * @throws ImageUploadException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, string $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-update', $book);

        $validated = $this->validate($request, [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
            'tags'        => ['array'],
        ]);

        if ($request->has('image_reset')) {
            $validated['image'] = null;
        } elseif (array_key_exists('image', $validated) && is_null($validated['image'])) {
            unset($validated['image']);
        }

        $book = $this->bookRepo->update($book, $validated);

        return redirect($book->getUrl());
    }

    /**
     * Shows the page to confirm deletion.
     */
    public function showDelete(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);
        $this->setPageTitle(trans('entities.books_delete_named', ['bookName' => $book->getShortName()]));

        return view('books.delete', ['book' => $book, 'current' => $book]);
    }

    /**
     * Remove the specified book from the system.
     *
     * @throws Throwable
     */
    public function destroy(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-delete', $book);

        $this->bookRepo->destroy($book);

        return redirect('/books');
    }

    /**
     * Show the permissions view.
     */
    public function showPermissions(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $book);

        return view('books.permissions', [
            'book' => $book,
        ]);
    }

    /**
     * Set the restrictions for this book.
     *
     * @throws Throwable
     */
    public function permissions(Request $request, PermissionsUpdater $permissionsUpdater, string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $book);

        $permissionsUpdater->updateFromPermissionsForm($book, $request);

        $this->showSuccessNotification(trans('entities.books_permissions_updated'));

        return redirect($book->getUrl());
    }

    /**
     * Show the view to copy a book.
     *
     * @throws NotFoundException
     */
    public function showCopy(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-view', $book);

        session()->flashInput(['name' => $book->name]);

        return view('books.copy', [
            'book' => $book,
        ]);
    }

    /**
     * Create a copy of a book within the requested target destination.
     *
     * @throws NotFoundException
     */
    public function copy(Request $request, Cloner $cloner, string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-view', $book);
        $this->checkPermission('book-create-all');

        $newName = $request->get('name') ?: $book->name;
        $bookCopy = $cloner->cloneBook($book, $newName);
        $this->showSuccessNotification(trans('entities.books_copy_success'));

        return redirect($bookCopy->getUrl());
    }

    /**
     * Convert the chapter to a book.
     */
    public function convertToShelf(HierarchyTransformer $transformer, string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);
        $this->checkOwnablePermission('book-delete', $book);
        $this->checkPermission('bookshelf-create-all');
        $this->checkPermission('book-create-all');

        $shelf = $transformer->transformBookToShelf($book);

        return redirect($shelf->getUrl());
    }
    // nci changes
    public function nci_basic_c_ceneter(){
        $view = setting()->getForCurrentUser('books_view_type');
        $sort = setting()->getForCurrentUser('books_sort', 'name');
        $order = setting()->getForCurrentUser('books_sort_order', 'asc');

        $books = $this->bookRepo->getAllPaginated(18, $sort, $order);
        $recents = $this->isSignedIn() ? $this->bookRepo->getRecentlyViewed(4) : false;
        $popular = $this->bookRepo->getPopular(4);
        $new = $this->bookRepo->getRecentlyCreated(4);

        $this->entityContextManager->clearShelfContext();

        $this->setPageTitle(trans('entities.books' ,[
            'books'   => $books,
            'recents' => $recents,
            'popular' => $popular,
            'new'     => $new,
            'view'    => $view,
            'sort'    => $sort,
            'order'   => $order,
        ]));
        return view('types_of_cancer/nci_basic_cancer_center');
    }
    public function nci_mlevel_c_ceneter(){
        return view('types_of_cancer/nci_mlevel_cancer_center');
    }
    public function nci_comprehensive_c_ceneter(){
        return view('types_of_cancer/nci_comprehensive_cancer_center');
    }
    public function nci_customer_ratings(){
        return view('types_of_cancer/nci_customer_satisfaction_ratings');
    }
    public function nci_cancer_forms(){
        return view('types_of_cancer/cancer_center_patients_form');
    }
    public function nci_operation_consideration_req(){
        return view('types_of_cancer/operational_consideration_req');
    }
    public function nci_chemotherapy(){
        return view('types_of_cancer/bcc/chemoteraphy');
    }
    public function chemoteraphy_considerations(){
        return view('types_of_cancer/bcc/chemoteraphy_considerations');
    }
    public function add_user_ratings(Request $request){
     $ratings= new Ratings_model();
     $ratings->additional_comments = $request->comment;
     $ratings->experience_rating = $request->difficult;
     $ratings->empathetic_rating = $request->empathetic;
     $ratings->doctor_attends_rating = $request->long;
     $ratings->satisfied_doctor_rating = $request->satisfied;
     $ratings->user_id =auth()->user()->id;
     $rates=$ratings->save();
     if ($rates) {
        # code...
        return redirect('/nci/customer/satisfaction/ratings')->with('message', 'Thanks for your feedback and your coment!');
     }
    }
    public function dataAjax(Request $request)
    {
    	$data = [];

        if($request->has('q')){
            $search = $request->q;
            $data =_model::select("id","name")
            		->where('name','LIKE',"%$search%")
            		->get();
        }
        return response()->json($data);
    }
}
