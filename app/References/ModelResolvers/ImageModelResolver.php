<?php

namespace BookStack\References\ModelResolvers;

use BookStack\Uploads\Image;
use BookStack\Uploads\ImageStorage;

class ImageModelResolver implements CrossLinkModelResolver
{
    protected ?string $pattern = null;

    public function resolve(string $link): ?Image
    {
        $pattern = $this->getUrlPattern();
        $matches = [];
        $match = preg_match($pattern, $link, $matches);
        if (!$match) {
            return null;
        }

        $path = $matches[2];

        // Strip thumbnail element from path if existing
        $originalPathSplit = array_filter(explode('/', $path), function (string $part) {
            $resizedDir = (str_starts_with($part, 'thumbs-') || str_starts_with($part, 'scaled-'));
            $missingExtension = !str_contains($part, '.');

            return !($resizedDir && $missingExtension);
        });

        // Build a database-format image path and search for the image entry
        $fullPath = '/uploads/images/' . ltrim(implode('/', $originalPathSplit), '/');

        return Image::query()->where('path', '=', $fullPath)->first();
    }

    /**
     * Get the regex pattern to identify image URLs.
     * Caches the pattern since it requires looking up to settings/config.
     */
    protected function getUrlPattern(): string
    {
        if ($this->pattern) {
            return $this->pattern;
        }

        $urls = [url('/uploads/images')];
        $baseImageUrl = ImageStorage::getPublicUrl('/uploads/images');
        if ($baseImageUrl !== $urls[0]) {
            $urls[] = $baseImageUrl;
        }

        $imageUrlRegex = implode('|', array_map(fn ($url) => preg_quote($url, '/'), $urls));
        $this->pattern = '/^(' . $imageUrlRegex . ')\/(.+)/';

        return $this->pattern;
    }
}
