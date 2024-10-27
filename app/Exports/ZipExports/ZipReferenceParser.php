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

class ZipReferenceParser
{
    /**
     * @var CrossLinkModelResolver[]
     */
    protected array $modelResolvers;

    public function __construct(EntityQueries $queries)
    {
        $this->modelResolvers = [
            new PagePermalinkModelResolver($queries->pages),
            new PageLinkModelResolver($queries->pages),
            new ChapterLinkModelResolver($queries->chapters),
            new BookLinkModelResolver($queries->books),
            new ImageModelResolver(),
            new AttachmentModelResolver(),
        ];
    }

    /**
     * Parse and replace references in the given content.
     * @param callable(Model):(string|null) $handler
     */
    public function parse(string $content, callable $handler): string
    {
        $escapedBase = preg_quote(url('/'), '/');
        $linkRegex = "/({$escapedBase}.*?)[\\t\\n\\f>\"'=?#()]/";
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
     * Attempt to resolve the given link to a model using the instance model resolvers.
     */
    protected function linkToModel(string $link): ?Model
    {
        foreach ($this->modelResolvers as $resolver) {
            $model = $resolver->resolve($link);
            if (!is_null($model)) {
                return $model;
            }
        }

        return null;
    }
}
