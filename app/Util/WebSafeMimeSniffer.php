<?php

namespace BookStack\Util;

use finfo;

/**
 * Helper class to sniff out the mime-type of content resulting in
 * a mime-type that's relatively safe to serve to a browser.
 */
class WebSafeMimeSniffer
{
    /**
     * @var string[]
     */
    protected array $safeMimes = [
        'application/json',
        'application/octet-stream',
        'application/pdf',
        'audio/aac',
        'audio/midi',
        'audio/mpeg',
        'audio/ogg',
        'audio/opus',
        'audio/wav',
        'audio/webm',
        'audio/x-m4a',
        'image/apng',
        'image/bmp',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/avif',
        'image/heic',
        'text/css',
        'text/csv',
        'text/javascript',
        'text/json',
        'text/plain',
        'video/x-msvideo',
        'video/mp4',
        'video/mpeg',
        'video/ogg',
        'video/webm',
        'video/vp9',
        'video/h264',
        'video/av1',
    ];

    protected array $textTypesByExtension = [
        'css' => 'text/css',
        'js' => 'text/javascript',
        'json' => 'application/json',
        'csv' => 'text/csv',
    ];

    /**
     * Sniff the mime-type from the given file content while running the result
     * through an allow-list to ensure a web-safe result.
     * Takes the content as a reference since the value may be quite large.
     * Accepts an optional $extension which can be used for further guessing.
     */
    public function sniff(string &$content, string $extension = ''): string
    {
        $fInfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $fInfo->buffer($content) ?: 'application/octet-stream';

        if ($mime === 'text/plain' && $extension) {
            $mime = $this->textTypesByExtension[$extension] ?? 'text/plain';
        }

        if (in_array($mime, $this->safeMimes)) {
            return $mime;
        }

        [$category] = explode('/', $mime, 2);
        if ($category === 'text') {
            return 'text/plain';
        }

        return 'application/octet-stream';
    }
}
