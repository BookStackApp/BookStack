<?php

namespace BookStack\Exports\ZipExports;

use BookStack\App\Model;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\References\ModelResolvers\AttachmentModelResolver;
use BookStack\References\ModelResolvers\BookLinkModelResolver;
use BookStack\References\ModelResolvers\ChapterLinkModelResolver;
use BookStack\References\ModelResolvers\CrossLinkModelResolver;
use BookStack\References\ModelResolvers\ImageModelResolver;
use BookStack\References\ModelResolvers\PageLinkModelResolver;
use BookStack\References\ModelResolvers\PagePermalinkModelResolver;
use BookStack\Uploads\ImageStorage;

class ZipReferenceParser
{
    /**
     * @var CrossLinkModelResolver[]|null
     */
    protected ?array $modelResolvers = null;

    public function __construct(
        protected EntityQueries $queries
    ) {
    }

    /**
     * Parse and replace references in the given content.
     * Calls the handler for each model link detected and replaces the link
     * with the handler return value if provided.
     * Returns the resulting content with links replaced.
     * @param callable(Model):(string|null) $handler
     */
    public function parseLinks(string $content, callable $handler): string
    {
        $linkRegex = $this->getLinkRegex();
        $matches = [];
        preg_match_all($linkRegex, $content, $matches);

        if (count($matches) < 2) {
            return $content;
        }

        foreach ($matches[1] as $link) {
            $model = $this->linkToModel($link);
            if ($model) {
                $result = $handler($model);
                if ($result !== null) {
                    $content = str_replace($link, $result, $content);
                }
            }
        }

        return $content;
    }

    /**
     * Parse and replace references in the given content.
     * Calls the handler for each reference detected and replaces the link
     * with the handler return value if provided.
     * Returns the resulting content string with references replaced.
     * @param callable(string $type, int $id):(string|null) $handler
     */
    public function parseReferences(string $content, callable $handler): string
    {
        $referenceRegex = '/\[\[bsexport:([a-z]+):(\d+)]]/';
        $matches = [];
        preg_match_all($referenceRegex, $content, $matches);

        if (count($matches) < 3) {
            return $content;
        }

        for ($i = 0; $i < count($matches[0]); $i++) {
            $referenceText = $matches[0][$i];
            $type = strtolower($matches[1][$i]);
            $id = intval($matches[2][$i]);
            $result = $handler($type, $id);
            if ($result !== null) {
                $content = str_replace($referenceText, $result, $content);
            }
        }

        return $content;
    }


    /**
     * Attempt to resolve the given link to a model using the instance model resolvers.
     */
    protected function linkToModel(string $link): ?Model
    {
        foreach ($this->getModelResolvers() as $resolver) {
            $model = $resolver->resolve($link);
            if (!is_null($model)) {
                return $model;
            }
        }

        return null;
    }

    protected function getModelResolvers(): array
    {
        if (isset($this->modelResolvers)) {
            return $this->modelResolvers;
        }

        $this->modelResolvers = [
            new PagePermalinkModelResolver($this->queries->pages),
            new PageLinkModelResolver($this->queries->pages),
            new ChapterLinkModelResolver($this->queries->chapters),
            new BookLinkModelResolver($this->queries->books),
            new ImageModelResolver(),
            new AttachmentModelResolver(),
        ];

        return $this->modelResolvers;
    }

    /**
     * Build the regex to identify links we should handle in content.
     */
    protected function getLinkRegex(): string
    {
        $urls = [rtrim(url('/'), '/')];
        $imageUrl = rtrim(ImageStorage::getPublicUrl('/'), '/');
        if ($urls[0] !== $imageUrl) {
            $urls[] = $imageUrl;
        }


        $urlBaseRegex = implode('|', array_map(function ($url) {
            return preg_quote($url, '/');
        }, $urls));

        return "/(({$urlBaseRegex}).*?)[\\t\\n\\f>\"'=?#()]/";
    }
}
