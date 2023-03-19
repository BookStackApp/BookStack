<?php

namespace BookStack\References;

use BookStack\Model;
use BookStack\References\ModelResolvers\BookLinkModelResolver;
use BookStack\References\ModelResolvers\BookshelfLinkModelResolver;
use BookStack\References\ModelResolvers\ChapterLinkModelResolver;
use BookStack\References\ModelResolvers\CrossLinkModelResolver;
use BookStack\References\ModelResolvers\PageLinkModelResolver;
use BookStack\References\ModelResolvers\PagePermalinkModelResolver;
use DOMDocument;
use DOMXPath;

class CrossLinkParser
{
    /**
     * @var CrossLinkModelResolver[]
     */
    protected array $modelResolvers;

    public function __construct(array $modelResolvers)
    {
        $this->modelResolvers = $modelResolvers;
    }

    /**
     * Extract any found models within the given HTML content.
     *
     * @return Model[]
     */
    public function extractLinkedModels(string $html): array
    {
        $models = [];

        $links = $this->getLinksFromContent($html);

        foreach ($links as $link) {
            $model = $this->linkToModel($link);
            if (!is_null($model)) {
                $models[get_class($model) . ':' . $model->id] = $model;
            }
        }

        return array_values($models);
    }

    /**
     * Get a list of href values from the given document.
     *
     * @returns string[]
     */
    protected function getLinksFromContent(string $html): array
    {
        $links = [];

        $html = '<?xml encoding="utf-8" ?><body>' . $html . '</body>';
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        $xPath = new DOMXPath($doc);
        $anchors = $xPath->query('//a[@href]');

        /** @var \DOMElement $anchor */
        foreach ($anchors as $anchor) {
            $links[] = $anchor->getAttribute('href');
        }

        return $links;
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

    /**
     * Create a new instance with a pre-defined set of model resolvers, specifically for the
     * default set of entities within BookStack.
     */
    public static function createWithEntityResolvers(): self
    {
        return new self([
            new PagePermalinkModelResolver(),
            new PageLinkModelResolver(),
            new ChapterLinkModelResolver(),
            new BookLinkModelResolver(),
            new BookshelfLinkModelResolver(),
        ]);
    }
}
