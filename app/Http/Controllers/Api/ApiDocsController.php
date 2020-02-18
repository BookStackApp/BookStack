<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Api\ApiDocsGenerator;
use Cache;
use Illuminate\Support\Collection;

class ApiDocsController extends ApiController
{

    /**
     * Load the docs page for the API.
     */
    public function display()
    {
        $docs = $this->getDocs();
        return view('api-docs.index', [
            'docs' => $docs,
        ]);
    }

    /**
     * Show a JSON view of the API docs data.
     */
    public function json() {
        $docs = $this->getDocs();
        return response()->json($docs);
    }

    /**
     * Get the base docs data.
     * Checks and uses the system cache for quick re-fetching.
     */
    protected function getDocs(): Collection
    {
        $appVersion = trim(file_get_contents(base_path('version')));
        $cacheKey = 'api-docs::' . $appVersion;
        if (Cache::has($cacheKey) && config('app.env') === 'production') {
            $docs = Cache::get($cacheKey);
        } else {
            $docs = (new ApiDocsGenerator())->generate();
            Cache::put($cacheKey, $docs, 60*24);
        }

        return $docs;
    }

}
