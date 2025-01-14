<?php

namespace BookStack\Theming;

use BookStack\Facades\Theme;
use BookStack\Http\Controller;
use BookStack\Util\FilePathNormalizer;

class ThemeController extends Controller
{
    /**
     * Serve a public file from the configured theme.
     */
    public function publicFile(string $theme, string $path)
    {
        $cleanPath = FilePathNormalizer::normalize($path);
        if ($theme !== Theme::getTheme() || !$cleanPath) {
            abort(404);
        }

        $filePath = theme_path("public/{$cleanPath}");
        if (!file_exists($filePath)) {
            abort(404);
        }

        $response = $this->download()->streamedFileInline($filePath);
        $response->setMaxAge(86400);

        return $response;
    }
}
