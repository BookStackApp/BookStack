<?php

namespace BookStack\Theming;

use BookStack\Exceptions\ThemeException;
use Illuminate\View\FileViewFinder;

class ThemeViews
{
    /**
     * @var array<string, array<string, int>>
     */
    protected array $beforeViews = [];

    /**
     * @var array<string, array<string, int>>
     */
    protected array $afterViews = [];

    public function __construct(
        protected FileViewFinder $finder
    ) {
    }

    /**
     * Register any extra paths for where we may expect views to be located
     * with the FileViewFinder, to make custom views available for use.
     * @param ThemeModule[] $modules
     */
    public function registerViewPathsForTheme(array $modules): void
    {
        foreach ($modules as $module) {
            $moduleViewsPath = $module->path('views');
            if (file_exists($moduleViewsPath) && is_dir($moduleViewsPath)) {
                $this->finder->prependLocation($moduleViewsPath);
            }
        }

        $this->finder->prependLocation(theme_path());
    }

    /**
     * Provide the response for a blade template view include.
     */
    public function handleViewInclude(string $viewPath, array $data = [], array $mergeData = []): string
    {
        if (!$this->hasRegisteredViews()) {
            return view()->make($viewPath, $data, $mergeData)->render();
        }

        if (str_contains('book-tree', $viewPath)) {
            dd($viewPath, $data);
        }

        $viewsContent = [
            ...$this->renderViewSets($this->beforeViews[$viewPath] ?? [], $data, $mergeData),
            view()->make($viewPath, $data, $mergeData)->render(),
            ...$this->renderViewSets($this->afterViews[$viewPath] ?? [], $data, $mergeData),
        ];

        return implode("\n", $viewsContent);
    }

    /**
     * Register a custom view to be rendered before the given target view is included in the template system.
     */
    public function renderBefore(string $targetView, string $localView, int $priority = 50): void
    {
        $this->registerAdjacentView($this->beforeViews, $targetView, $localView, $priority);
    }

    /**
     * Register a custom view to be rendered after the given target view is included in the template system.
     */
    public function renderAfter(string $targetView, string $localView, int $priority = 50): void
    {
        $this->registerAdjacentView($this->afterViews, $targetView, $localView, $priority);
    }

    public function hasRegisteredViews(): bool
    {
        return !empty($this->beforeViews) || !empty($this->afterViews);
    }

    protected function registerAdjacentView(array &$location, string $targetView, string $localView, int $priority = 50): void
    {
        try {
            $viewPath = $this->finder->find($localView);
        } catch (\InvalidArgumentException $exception) {
            throw new ThemeException("Expected registered view file with name \"{$localView}\" could not be found.");
        }

        if (!isset($location[$targetView])) {
            $location[$targetView] = [];
        }

        $location[$targetView][$viewPath] = $priority;
    }

    /**
     * @param array<string, int> $viewSet
     * @return string[]
     */
    protected function renderViewSets(array $viewSet, array $data, array $mergeData): array
    {
        $paths = array_keys($viewSet);
        usort($paths, function (string $a, string $b) use ($viewSet) {
            return $viewSet[$a] <=> $viewSet[$b];
        });

        return array_map(function (string $viewPath) use ($data, $mergeData) {
            return view()->file($viewPath, $data, $mergeData)->render();
        }, $paths);
    }
}
