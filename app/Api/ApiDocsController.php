<?php

namespace BookStack\Api;

use BookStack\Entities\Tools\Markdown\HtmlToMarkdown;
use BookStack\Http\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiDocsController extends ApiController
{
    protected static array $gettingStartedSectionsById = [
        'authentication' => 'Authentication',
        'request-format' => 'Request Format',
        'listing-endpoints' => 'Listing Endpoints',
        'error-handling' => 'Error Handling',
        'rate-limits' => 'Rate Limits',
        'content-security' => 'Content Security',
    ];

    /**
     * Load the docs page for the API.
     */
    public function display()
    {
        $docs = ApiDocsGenerator::generateConsideringCache();
        $this->setPageTitle(trans('settings.users_api_tokens_docs'));

        return view('api-docs.index', [
            'docs' => $docs,
            'gettingStartedSections' => static::$gettingStartedSectionsById,
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

    /**
     * Download the API docs as a cleaner HTML file or as JSON with embedded HTML guidance.
     * Provide a ?format=json query parameter to download as JSON.
     */
    public function download(Request $request)
    {
        $format = $request->query('format') ?? 'html';
        $docs = ApiDocsGenerator::generateConsideringCache();
        $downloadName = Str::slug(strtolower(setting('app-name')) . '-api-docs');

        if ($format === 'json') {
            $docs->prepend(
                view('api-docs.parts.getting-started')->render(),
                'getting-started-guide'
            );
            return $this->createDownload()->directly(json_encode($docs), "{$downloadName}.json");
        }

        $responseData = view('api-docs.download', [
            'docs' => $docs,
            'gettingStartedSections' => static::$gettingStartedSectionsById,
        ])->render();

        return $this->createDownload()->directly($responseData, "{$downloadName}.html");
    }

    /**
     * Redirect to the API docs page.
     * Required as a controller method, instead of the Route::redirect helper,
     * to ensure the URL is generated correctly.
     */
    public function redirect()
    {
        return redirect('/api/docs');
    }
}
