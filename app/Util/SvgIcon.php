<?php

namespace BookStack\Util;

use BookStack\Facades\Theme;

class SvgIcon
{
    public function __construct(
        protected string $name,
        protected array $attrs = []
    ) {
    }

    public function toHtml(): string
    {
        $attrs = array_merge([
            'class'     => 'svg-icon',
            'data-icon' => $this->name,
            'role'      => 'presentation',
        ], $this->attrs);

        $attrString = ' ';
        foreach ($attrs as $attrName => $attr) {
            $attrString .= $attrName . '="' . $attr . '" ';
        }

        $defaultIconPath = resource_path('icons/' . $this->name . '.svg');
        $iconPath = Theme::findFirstFile("icons/{$this->name}.svg") ?? $defaultIconPath;
        if (!file_exists($iconPath)) {
            return '';
        }

        $fileContents = file_get_contents($iconPath);

        return str_replace('<svg', '<svg' . $attrString, $fileContents);
    }
}
