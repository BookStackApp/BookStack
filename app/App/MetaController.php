<?php

namespace BookStack\App;

use BookStack\Http\Controller;
use BookStack\Uploads\FaviconHandler;

class MetaController extends Controller
{
    /**
     * Show the view for /robots.txt.
     */
    public function robots()
    {
        $sitePublic = setting('app-public', false);
        $allowRobots = config('app.allow_robots');

        if ($allowRobots === null) {
            $allowRobots = $sitePublic;
        }

        return response()
            ->view('misc.robots', ['allowRobots' => $allowRobots])
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Show the route for 404 responses.
     */
    public function notFound()
    {
        return response()->view('errors.404', [], 404);
    }

    /**
     * Serve the application favicon.
     * Ensures a 'favicon.ico' file exists at the web root location (if writable) to be served
     * directly by the webserver in the future.
     */
    public function favicon(FaviconHandler $favicons)
    {
        $exists = $favicons->restoreOriginalIfNotExists();
        return response()->file($exists ? $favicons->getPath() : $favicons->getOriginalPath());
    }

    /**
     * Serve a PWA application manifest.
     */
    public function pwaManifest(PwaManifestBuilder $manifestBuilder)
    {
        return response()->json($manifestBuilder->build());
    }

    /**
     * Show license information for the application.
     */
    public function licenses()
    {
        $this->setPageTitle(trans('settings.licenses'));

        return view('help.licenses', [
            'license' => file_get_contents(base_path('LICENSE')),
            'phpLibData' => file_get_contents(base_path('dev/licensing/php-library-licenses.txt')),
            'jsLibData' => file_get_contents(base_path('dev/licensing/js-library-licenses.txt')),
        ]);
    }

    /**
     * Show the view for /opensearch.xml.
     */
    public function opensearch()
    {
        return response()
            ->view('misc.opensearch')
            ->header('Content-Type', 'application/opensearchdescription+xml');
    }
}
