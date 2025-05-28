<?php

namespace BookStack\Plugins;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MermaidProvider
{
    const MERMAID_REPOSITORY = 'https://api.github.com/repos/mermaid-js/mermaid/tags';
    const MERMAID_CDN = 'https://cdn.jsdelivr.net/npm/mermaid@%s/dist/mermaid.min.js';

    /**
     * Retrieve the version from the Github of Mermaid
     */
    public function getMermaidVersions()
    {
        $cacheKey = 'git_mermaid_versions';
        $cachedVersions = Cache::get($cacheKey);

        if (!is_null($cachedVersions)) {
            return array_merge(['disabled', 'latest'], $cachedVersions);
        }

        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github+json',
            'User-Agent' => 'BookStack',
        ])->get(MermaidProvider::MERMAID_REPOSITORY);

        if ($response->successful()) {
            $versions = collect($response->json())
                ->pluck('name')
                ->all();

            Cache::put($cacheKey, $versions, now()->addHours(12));

            return array_merge(['disabled', 'latest'], $versions);
        }

        return ['disabled', 'latest'];
    }

    /**
     * Get the MermaidJS CDN URI to use.
     */
    public function getMermaidJsCdnUri(): string
    {
        $mermaidJsVersion = setting('enable-mermaid');

        if ($mermaidJsVersion === 'disabled') {
            return '';
        }

        $localPath = public_path("mermaid/mermaid.min.js");

        if (file_exists($localPath)) {
            return asset("mermaid/mermaid.min.js");
        }

        return '';
    }

}
