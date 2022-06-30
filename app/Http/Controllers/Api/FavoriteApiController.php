<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\TopFavourites;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\PermissionsException;
use Closure;
use Exception;
use Illuminate\Http\Request;

class FavoriteApiController extends ApiController
{
    protected PageRepo $pageRepo;

    protected $rules = [
        'create' => [
            'book_id'    => ['required_without:chapter_id', 'integer'],
            'chapter_id' => ['required_without:book_id', 'integer'],
            'name'       => ['required', 'string', 'max:255'],
            'html'       => ['required_without:markdown', 'string'],
            'markdown'   => ['required_without:html', 'string'],
            'tags'       => ['array'],
        ],
        'update' => [
            'book_id'    => ['integer'],
            'chapter_id' => ['integer'],
            'name'       => ['string', 'min:1', 'max:255'],
            'html'       => ['string'],
            'markdown'   => ['string'],
            'tags'       => ['array'],
        ],
        'updateFavourite' => [
            'is_favourite' => ['required', 'boolean'],
        ],
    ];

    public function __construct(PageRepo $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    /**
     * Get a listing of pages visible to the user.
     */
    public function list()
    {
        return (new TopFavourites(['page']))->run(100, 0);
    }

    /**
     * Update favourite state of a single page for the current user.
     */
    public function updateFavourite(Request $request, string $id)
    {
        if (!signedInUser()) {
            $this->showPermissionError();
        }
        $requestData = $this->validate($request, $this->rules['updateFavourite']);

        $page = $this->pageRepo->getById($id, ['favourites']);

        if ($requestData['is_favourite'] == true) {
            $page->favourites()->firstOrCreate([
                'user_id' => user()->id,
            ]);
        } else {
            $page->favourites()->where([
                'user_id' => user()->id,
            ])->delete();
        }

        return response()->json($page->forJsonDisplay());
    }

    /**
     * Format the given user model for a listing multi-result display.
     */
    protected function listFormatter(Page $page)
    {
        $page->setAttribute('is_favourite', $page->isFavourite());
    }
}
