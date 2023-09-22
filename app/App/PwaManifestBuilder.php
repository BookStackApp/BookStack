<?php

namespace BookStack\App;

use BookStack\Activity\ActivityQueries;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\RecentlyViewed;
use BookStack\Entities\Queries\TopFavourites;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Entities\Tools\PageContent;
use BookStack\Http\Controller;
use BookStack\Uploads\FaviconHandler;
use BookStack\Util\SimpleListOptions;
use Illuminate\Http\Request;

class PwaManifestBuilder extends Controller
{
    private function GenerateManifest()
    {
        return [
            "name" => config('app.name'),
            "short_name" => config('app.name'),
            "start_url" => "./",
            "scope" => ".",
            "display" => "standalone",
            "background_color" => (setting()->getForCurrentUser('dark-mode-enabled') ? setting('app-color-dark') : setting('app-color')),
            "description" => config('app.name'),
            "theme_color" => setting('app-color'),
            "launch_handler" => [
                "client_mode" => "focus-existing"
            ],
            "orientation" => "portrait",
            "icons" => [
                [
                    "src" => setting('app-icon-64') ?: url('/icon-64.png'),
                    "sizes" => "64x64",
                    "type" => "image/png"
                ],
                [
                    "src" => setting('app-icon-32') ?: url('/icon-32.png'),
                    "sizes" => "32x32",
                    "type" => "image/png"
                ],
                [
                    "src" => setting('app-icon-128') ?: url('/icon-128.png'),
                    "sizes" => "128x128",
                    "type" => "image/png"
                ],
                [
                    "src" => setting('app-icon-180') ?: url('/icon-180.png'),
                    "sizes" => "180x180",
                    "type" => "image/png"
                ],
                [
                    "src" => setting('app-icon') ?: url('/icon.png'),
                    "sizes" => "256x256",
                    "type" => "image/png"
                ],
                [
                    "src" => "icon.ico",
                    "sizes" => "48x48",
                    "type" => "image/vnd.microsoft.icon"
                ],
                [
                    "src" => "favicon.ico",
                    "sizes" => "48x48",
                    "type" => "image/vnd.microsoft.icon"
                ],
            ],
        ];
    }

    /**
     * Serve the application manifest.
     * Ensures a 'manifest.json'
     */
    public function manifest()
    {
        return response()->json($this->GenerateManifest());
    }
}
