<?php

namespace BookStack\References\ModelResolvers;

use BookStack\Uploads\Image;

class ImageModelResolver implements CrossLinkModelResolver
{
    public function resolve(string $link): ?Image
    {
        $pattern = '/^' . preg_quote(url('/uploads/images'), '/') . '\/(.+)/';
        $matches = [];
        $match = preg_match($pattern, $link, $matches);
        if (!$match) {
            return null;
        }

        $path = $matches[1];

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
}
