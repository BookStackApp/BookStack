<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Page;

enum PageEditorType: string
{
    case WysiwygTinymce = 'wysiwyg';
    case WysiwygLexical = 'wysiwyg2024';
    case Markdown = 'markdown';

    public function isHtmlBased(): bool
    {
        return match ($this) {
            self::WysiwygTinymce, self::WysiwygLexical => true,
            self::Markdown => false,
        };
    }

    public static function fromRequestValue(string $value): static|null
    {
        $editor = explode('-', $value)[0];
        return static::tryFrom($editor);
    }

    public static function forPage(Page $page): static|null
    {
        return static::tryFrom($page->editor);
    }

    public static function getSystemDefault(): static
    {
        $setting = setting('app-editor');
        return static::tryFrom($setting) ?? static::WysiwygTinymce;
    }
}
