<?php

namespace BookStack\Users\Controllers;

use BookStack\Http\Controller;
use BookStack\Users\UserRepo;
use Illuminate\Http\Request;

class UserPreferencesController extends Controller
{
    public function __construct(
        protected UserRepo $userRepo
    ) {
    }

    /**
     * Update the preferred view format for a list view of the given type.
     */
    public function changeView(Request $request, string $type)
    {
        $valueViewTypes = ['books', 'bookshelves', 'bookshelf'];
        if (!in_array($type, $valueViewTypes)) {
            return $this->redirectToRequest($request);
        }

        $view = $request->get('view');
        if (!in_array($view, ['grid', 'list'])) {
            $view = 'list';
        }

        $key = $type . '_view_type';
        setting()->putForCurrentUser($key, $view);

        return $this->redirectToRequest($request);
    }

    /**
     * Change the stored sort type for a particular view.
     */
    public function changeSort(Request $request, string $type)
    {
        $validSortTypes = ['books', 'bookshelves', 'shelf_books', 'users', 'roles', 'webhooks', 'tags', 'page_revisions'];
        if (!in_array($type, $validSortTypes)) {
            return $this->redirectToRequest($request);
        }

        $sort = substr($request->get('sort') ?: 'name', 0, 50);
        $order = $request->get('order') === 'desc' ? 'desc' : 'asc';

        $sortKey = $type . '_sort';
        $orderKey = $type . '_sort_order';
        setting()->putForCurrentUser($sortKey, $sort);
        setting()->putForCurrentUser($orderKey, $order);

        return $this->redirectToRequest($request);
    }

    /**
     * Toggle dark mode for the current user.
     */
    public function toggleDarkMode(Request $request)
    {
        $enabled = setting()->getForCurrentUser('dark-mode-enabled');
        setting()->putForCurrentUser('dark-mode-enabled', $enabled ? 'false' : 'true');

        return $this->redirectToRequest($request);
    }

    /**
     * Update the stored section expansion preference for the given user.
     */
    public function changeExpansion(Request $request, string $type)
    {
        $typeWhitelist = ['home-details'];
        if (!in_array($type, $typeWhitelist)) {
            return response('Invalid key', 500);
        }

        $newState = $request->get('expand', 'false');
        setting()->putForCurrentUser('section_expansion#' . $type, $newState);

        return response('', 204);
    }

    /**
     * Update the favorite status for a code language.
     */
    public function updateCodeLanguageFavourite(Request $request)
    {
        $validated = $this->validate($request, [
            'language' => ['required', 'string', 'max:20'],
            'active' => ['required', 'bool'],
        ]);

        $currentFavoritesStr = setting()->getForCurrentUser('code-language-favourites', '');
        $currentFavorites = array_filter(explode(',', $currentFavoritesStr));

        $isFav = in_array($validated['language'], $currentFavorites);
        if (!$isFav && $validated['active']) {
            $currentFavorites[] = $validated['language'];
        } elseif ($isFav && !$validated['active']) {
            $index = array_search($validated['language'], $currentFavorites);
            array_splice($currentFavorites, $index, 1);
        }

        setting()->putForCurrentUser('code-language-favourites', implode(',', $currentFavorites));
        return response('', 204);
    }
}
