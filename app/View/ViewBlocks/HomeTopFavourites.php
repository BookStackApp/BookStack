<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\QueryTopFavourites;
use BookStack\View\ViewBlockInterface;
use Illuminate\Http\Request;

class HomeTopFavourites implements ViewBlockInterface
{
    public function __construct(
        protected QueryTopFavourites $topFavourites
    ) {
    }

    public function getId(): string
    {
        return 'builtin_home-top-favourites';
    }

    public function getLabel(): string
    {
        return trans('entities.my_most_viewed_favourites');
    }

    public function getView(array $viewData): string
    {
        if ($viewData['homeView'] === 'default') {
            return 'home.parts.default-card-top-favourites';
        }

        return 'home.parts.configured-section-top-favourites';
    }

    public function withData(array $viewData): array
    {
        $favourites = $this->topFavourites->run(6);
        return [
            'favourites' => $favourites
        ];
    }
}
