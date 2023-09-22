<?php

namespace BookStack\App;

use BookStack\Http\Controller;

class PwaManifestBuilder extends Controller
{
    private function GenerateManifest()
    {
        dump(setting()->getForCurrentUser('dark-mode-enabled'));
        dump(setting('app-color-dark'));
        dump(setting('app-color'));

        return [
            "name" => setting('app-name'),
            "short_name" => setting('app-name'),
            "start_url" => "./",
            "scope" => "/",
            "display" => "standalone",
            "background_color" => (setting()->getForCurrentUser('dark-mode-enabled') ? setting('app-color-dark') : setting('app-color')),
            "description" => setting('app-name'),
            "theme_color" => (setting()->getForCurrentUser('dark-mode-enabled') ? setting('app-color-dark') : setting('app-color')),
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
                    "src" => public_path('icon.ico'),
                    "sizes" => "48x48",
                    "type" => "image/vnd.microsoft.icon"
                ],
                [
                    "src" => public_path('favicon.ico'),
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
