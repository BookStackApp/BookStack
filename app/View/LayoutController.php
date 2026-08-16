<?php

namespace BookStack\View;

use BookStack\Http\Controller;
use Closure;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function __construct(
        protected ViewBlockManager $viewBlocks,
        protected ViewBlockPreferences $viewBlockPreferences,
    ) {
        $this->middleware(function (Request $request, Closure $next) {
            $this->preventGuestAccess();
            return $next($request);
        });
    }

    /**
     * Start editing the layout for a specific location.
     */
    public function edit(string $location)
    {
        $namedLocations = $this->viewBlocks->getNamedLocations();
        $locationName = $namedLocations[$location] ?? $location;
        $blocks = $this->viewBlocks->getForLocationForCurrentUser($location);

        $this->setPageTitle(trans('preferences.layout_edit'));

        return view('settings.layouts.edit', [
            'location' => $location,
            'locationName' => $locationName,
            'namedLocations' => $namedLocations,
            'blocks' => $blocks,
        ]);
    }

    /**
     * Update the layout for a specific location for the current user.
     */
    public function update(string $location, Request $request)
    {
        $data = $this->validate($request, [
            'layout' => ['required', 'string', 'json'],
        ]);

        $layoutData = json_decode($data['layout'], true, 5);
        $this->viewBlocks->updatePreferencesFromIdPositionMap($location, $layoutData);

        $this->showSuccessNotification(trans('preferences.layout_update_success'));

        return redirect("/layouts/{$location}");
    }

    /**
     * Reset the layout for a specific location, for the current user,
     * back to system defaults by removing any user-specific preferences.
     */
    public function reset(string $location)
    {
        $this->viewBlockPreferences->clearForLocation($location);

        $this->showSuccessNotification(trans('preferences.layout_reset_success'));

        return redirect("/layouts/{$location}");
    }
}
