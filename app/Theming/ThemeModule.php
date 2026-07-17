<?php

namespace BookStack\Theming;

readonly class ThemeModule
{
    public function __construct(
        public string $name,
        public string $description,
        public string $version,
        public string $folderName,
    ) {
    }

    /**
     * Create a ThemeModule instance from JSON data.
     *
     * @throws ThemeModuleException
     */
    public static function fromJson(array $data, string $folderName): self
    {
        if (empty($data['name']) || !is_string($data['name'])) {
            throw new ThemeModuleException("Module in folder \"{$folderName}\" is missing a valid 'name' property");
        }

        if (!isset($data['description']) || !is_string($data['description'])) {
            throw new ThemeModuleException("Module in folder \"{$folderName}\" is missing a valid 'description' property");
        }

        if (!isset($data['version']) || !is_string($data['version'])) {
            throw new ThemeModuleException("Module in folder \"{$folderName}\" is missing a valid 'version' property");
        }

        if (!preg_match('/^v?\d+\.\d+\.\d+(-.*)?$/', $data['version'])) {
            throw new ThemeModuleException("Module in folder \"{$folderName}\" has an invalid 'version' format. Expected semantic version format like '1.0.0' or 'v1.0.0'");
        }

        return new self(
            name: $data['name'],
            description: $data['description'],
            version: $data['version'],
            folderName: $folderName,
        );
    }

    /**
     * Get a path for a file within this module.
     */
    public function path($path = ''): string
    {
        $component = trim($path, '/');
        return theme_path("modules/{$this->folderName}/{$component}");
    }

    public function getVersion(): string
    {
        return str_starts_with($this->version, 'v') ? $this->version : 'v' . $this->version;
    }
}
