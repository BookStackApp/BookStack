<?php

namespace BookStack\Util;

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

        $iconPath = resource_path('icons/' . $this->name . '.svg');
        $themeIconPath = theme_path('icons/' . $this->name . '.svg');

        if ($themeIconPath && file_exists($themeIconPath)) {
            $iconPath = $themeIconPath;
        } elseif (!file_exists($iconPath)) {
            return '';
        }

        $fileContents = file_get_contents($iconPath);

        return str_replace('<svg', '<svg' . $attrString, $fileContents);
    }
}
