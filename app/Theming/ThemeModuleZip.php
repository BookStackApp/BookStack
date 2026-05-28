<?php

namespace BookStack\Theming;

use BookStack\Util\FilePathNormalizer;
use ZipArchive;

readonly class ThemeModuleZip
{
    public function __construct(
        protected string $path
    ) {
    }

    public function extractTo(string $destinationPath): void
    {
        $zip = new ZipArchive();
        $zip->open($this->path);
        $prefix = $this->getZipContentPrefix($zip);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $entryIsDir = str_ends_with($name, "/");
            if ($entryIsDir) {
                continue;
            }

            $stream = $zip->getStreamIndex($i);

            if ($prefix) {
                if (!str_starts_with($name, $prefix) || $name === $prefix) {
                    continue;
                }
                $name = str_replace($prefix, '', $name);
            }

            try {
                $targetPath = $destinationPath . DIRECTORY_SEPARATOR . FilePathNormalizer::normalize($name);
            } catch (\Exception $exception) {
                throw new ThemeModuleException("Bad file path found in module ZIP file: {$name}");
            }

            $targetPathDir = dirname($targetPath);
            if (!is_dir($targetPathDir)) {
                $dirCreated = mkdir($targetPathDir, 0777, true);
                if (!$dirCreated) {
                    throw new ThemeModuleException("Failed to create directory {$targetPathDir} when extracting module files");
                }
            }

            $targetFile = fopen($targetPath, 'w');
            $written = stream_copy_to_stream($stream, $targetFile);
            if (!$written) {
                throw new ThemeModuleException("Failed to write to {$targetPath} when extracting module files");
            }
            fclose($targetFile);
        }

        $zip->close();
    }

    /**
     * Read the module's JSON metadata to read it into a ThemeModule instance.
     * @throws ThemeModuleException
     */
    public function getModuleInstance(): ThemeModule
    {
        $zip = new ZipArchive();
        $open = $zip->open($this->path);
        if ($open !== true) {
            throw new ThemeModuleException("Unable to open zip file at {$this->path}");
        }

        $prefix = $this->getZipContentPrefix($zip);
        $moduleJsonText = $zip->getFromName("{$prefix}bookstack-module.json");
        $zip->close();

        if ($moduleJsonText === false) {
            throw new ThemeModuleException("bookstack-module.json not found within module ZIP at {$this->path}");
        }

        $moduleJson = json_decode($moduleJsonText, true);
        if ($moduleJson === null) {
            throw new ThemeModuleException("Could not read JSON from bookstack-module.json within module ZIP at {$this->path}");
        }

        return ThemeModule::fromJson($moduleJson, '_temp');
    }

    /**
     * Get the path to the zip file.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Check if the zip file exists and that it appears to be a valid zip file.
     */
    public function exists(): bool
    {
        if (!file_exists($this->path)) {
            return false;
        }

        $zip = new ZipArchive();
        $open = $zip->open($this->path, ZipArchive::RDONLY);
        if ($open === true) {
            $zip->close();
            return true;
        }
        return false;
    }

    /**
     * Get the total size of the zip file contents when uncompressed.
     */
    public function getContentsSize(): int
    {
        $zip = new ZipArchive();

        if ($zip->open($this->path) !== true) {
            return 0;
        }

        $totalSize = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat !== false) {
                $totalSize += $stat['size'];
            }
        }

        $zip->close();

        return $totalSize;
    }

    protected function getZipContentPrefix(ZipArchive $zip): string
    {
        $index = $zip->locateName('bookstack-module.json', ZipArchive::FL_NODIR);
        if ($index === false) {
            return '';
        }

        $location = $zip->getNameIndex($index);
        $pathParts = explode('/', $location);
        if (count($pathParts) !== 2) {
            return '';
        }

        return $pathParts[0] . '/';
    }
}
