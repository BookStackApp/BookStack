<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Api\ApiDocsGenerator;

class ApiDocsController extends ApiController
{
    /**
     * Load the docs page for the API.
     */
    public function display()
    {
        $docs = ApiDocsGenerator::generateConsideringCache();
        $this->setPageTitle(trans('settings.users_api_tokens_docs'));

        return view('api-docs.index', [
            'docs' => $docs,
        ]);
    }

    /**
     * Show a JSON view of the API docs data.
     */
    public function json()
    {
        $docs = ApiDocsGenerator::generateConsideringCache();

        return response()->json($docs);
    }
}
