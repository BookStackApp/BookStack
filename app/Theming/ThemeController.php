<?php

namespace BookStack\Theming;

use BookStack\Facades\Theme;
use BookStack\Http\Controller;
use BookStack\Util\FilePathNormalizer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ThemeController extends Controller
{
    /**
     * Serve a public file from the configured theme.
     */
    public function publicFile(string $theme, string $path): StreamedResponse
    {
        $cleanPath = FilePathNormalizer::normalize($path);
        if ($theme !== Theme::getTheme() || !$cleanPath) {
            abort(404);
        }

        $filePath = Theme::findFirstFile("public/{$cleanPath}");
        if (!$filePath) {
            abort(404);
        }

        $response = $this->download()->streamedFileInline($filePath);
        $response->setMaxAge(86400);

        return $response;
    }
}
