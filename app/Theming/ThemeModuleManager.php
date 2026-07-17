<?php

namespace BookStack\Theming;

use Illuminate\Support\Str;

class ThemeModuleManager
{
    /** @var array<string, ThemeModule>|null */
    protected array|null $loadedModules = null;

    public function __construct(
        protected string $modulesFolderPath
    ) {
    }

    /**
     * @return array<string, ThemeModule>
     */
    public function getByName(string $name): array
    {
        return array_filter($this->load(), fn(ThemeModule $module) => $module->name === $name);
    }

    public function deleteModuleFolder(string $moduleFolderName): void
    {
        $modules = $this->load();
        $module = $modules[$moduleFolderName] ?? null;
        if (!$module) {
            return;
        }

        $moduleFolderPath = $module->path('');
        if (!file_exists($moduleFolderPath)) {
            return;
        }

        $this->deleteDirectoryRecursively($moduleFolderPath);
        unset($this->loadedModules[$moduleFolderName]);
    }

    /**
     * @throws ThemeModuleException
     */
    public function addFromZip(string $name, ThemeModuleZip $zip): ThemeModule
    {
        $baseFolderName = Str::limit(Str::slug($name), 40, '');
        $folderName = $baseFolderName;
        while (!$baseFolderName || file_exists($this->modulesFolderPath . DIRECTORY_SEPARATOR . $folderName)) {
            $folderName = ($baseFolderName ?: 'mod') . '-' . Str::random(4);
        }

        $folderPath = $this->modulesFolderPath . DIRECTORY_SEPARATOR . $folderName;
        try {
            $zip->extractTo($folderPath);
        } catch (ThemeModuleException $exception) {
            if (is_dir($folderPath)) {
                $this->deleteDirectoryRecursively($folderPath);
            }
            throw new ThemeModuleException("Failed to load extract files from module ZIP with error: {$exception->getMessage()}");
        }

        $module = $this->loadFromFolder($folderName);
        if (!$module) {
            throw new ThemeModuleException("Failed to load module from zip file after extraction");
        }

        return $module;
    }

    protected function deleteDirectoryRecursively(string $path): void
    {
        $items = array_diff(scandir($path), ['.', '..']);
        foreach ($items as $item) {
            $itemPath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($itemPath)) {
                $this->deleteDirectoryRecursively($itemPath);
            } else {
                $deleted = unlink($itemPath);
                if (!$deleted) {
                    throw new ThemeModuleException("Failed to delete file at \"{$itemPath}\"");
                }
            }
        }
        rmdir($path);
    }

    public function load(): array
    {
        if ($this->loadedModules !== null) {
            return $this->loadedModules;
        }

        if (!is_dir($this->modulesFolderPath)) {
            return [];
        }

        $subFolders = array_filter(scandir($this->modulesFolderPath), function ($item) {
            return $item !== '.' && $item !== '..' && is_dir($this->modulesFolderPath . DIRECTORY_SEPARATOR . $item);
        });

        $modules = [];

        foreach ($subFolders as $folderName) {
            $module = $this->loadFromFolder($folderName);
            if ($module) {
                $modules[$folderName] = $module;
            }
        }

        $this->loadedModules = $modules;

        return $modules;
    }

    protected function loadFromFolder(string $folderName): ThemeModule|null
    {
        $moduleJsonFile = $this->modulesFolderPath . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . 'bookstack-module.json';
        if (!file_exists($moduleJsonFile)) {
            return null;
        }

        try {
            $jsonContent = file_get_contents($moduleJsonFile);
            $jsonData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ThemeModuleException("Invalid JSON in module file at \"{$moduleJsonFile}\": " . json_last_error_msg());
            }

            $module = ThemeModule::fromJson($jsonData, $folderName);
        } catch (ThemeModuleException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new ThemeModuleException("Failed loading module from \"{$moduleJsonFile}\" with error: {$exception->getMessage()}");
        }

        return $module;
    }
}
