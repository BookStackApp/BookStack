<?php

if (! function_exists('versioned_asset')) {
    /**
     * Get the path to a versioned file.
     *
     * @param  string  $file
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    function versioned_asset($file)
    {
        static $manifest = null;

        if (is_null($manifest)) {
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        }

        if (isset($manifest[$file])) {
            return '/' . $manifest[$file];
        }

        if (file_exists(public_path($file))) {
            return '/' . $file;
        }

        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}