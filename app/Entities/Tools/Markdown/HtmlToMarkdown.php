<?php

namespace BookStack\Entities\Tools\Markdown;

use League\HTMLToMarkdown\Converter\BlockquoteConverter;
use League\HTMLToMarkdown\Converter\CodeConverter;
use League\HTMLToMarkdown\Converter\CommentConverter;
use League\HTMLToMarkdown\Converter\DivConverter;
use League\HTMLToMarkdown\Converter\EmphasisConverter;
use League\HTMLToMarkdown\Converter\HardBreakConverter;
use League\HTMLToMarkdown\Converter\HeaderConverter;
use League\HTMLToMarkdown\Converter\HorizontalRuleConverter;
use League\HTMLToMarkdown\Converter\ImageConverter;
use League\HTMLToMarkdown\Converter\LinkConverter;
use League\HTMLToMarkdown\Converter\ListBlockConverter;
use League\HTMLToMarkdown\Converter\ListItemConverter;
use League\HTMLToMarkdown\Converter\PreformattedConverter;
use League\HTMLToMarkdown\Converter\TextConverter;
use League\HTMLToMarkdown\Environment;
use League\HTMLToMarkdown\HtmlConverter;

class HtmlToMarkdown
{
    protected $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    /**
     * Run the conversion.
     */
    public function convert(): string
    {
        $converter = new HtmlConverter($this->getConverterEnvironment());
        $html = $this->prepareHtml($this->html);

        return $converter->convert($html);
    }

    /**
     * Run any pre-processing to the HTML to clean it up manually before conversion.
     */
    protected function prepareHtml(string $html): string
    {
        // Carriage returns can cause whitespace issues in output
        $html = str_replace("\r\n", "\n", $html);
        // Attributes on the pre tag can cause issues with conversion
        return preg_replace('/<pre .*?>/', '<pre>', $html);
    }

    /**
     * Get the HTML to Markdown customized environment.
     * Extends the default provided environment with some BookStack specific tweaks.
     */
    protected function getConverterEnvironment(): Environment
    {
        $environment = new Environment([
            'header_style'            => 'atx', // Set to 'atx' to output H1 and H2 headers as # Header1 and ## Header2
            'suppress_errors'         => true, // Set to false to show warnings when loading malformed HTML
            'strip_tags'              => false, // Set to true to strip tags that don't have markdown equivalents. N.B. Strips tags, not their content. Useful to clean MS Word HTML output.
            'strip_placeholder_links' => false, // Set to true to remove <a> that doesn't have href.
            'bold_style'              => '**', // DEPRECATED: Set to '__' if you prefer the underlined style
            'italic_style'            => '*', // DEPRECATED: Set to '_' if you prefer the underlined style
            'remove_nodes'            => '', // space-separated list of dom nodes that should be removed. example: 'meta style script'
            'hard_break'              => false, // Set to true to turn <br> into `\n` instead of `  \n`
            'list_item_style'         => '-', // Set the default character for each <li> in a <ul>. Can be '-', '*', or '+'
            'preserve_comments'       => false, // Set to true to preserve comments, or set to an array of strings to preserve specific comments
            'use_autolinks'           => false, // Set to true to use simple link syntax if possible. Will always use []() if set to false
            'table_pipe_escape'       => '\|', // Replacement string for pipe characters inside markdown table cells
            'table_caption_side'      => 'top', // Set to 'top' or 'bottom' to show <caption> content before or after table, null to suppress
        ]);

        $environment->addConverter(new BlockquoteConverter());
        $environment->addConverter(new CodeConverter());
        $environment->addConverter(new CommentConverter());
        $environment->addConverter(new DivConverter());
        $environment->addConverter(new EmphasisConverter());
        $environment->addConverter(new HardBreakConverter());
        $environment->addConverter(new HeaderConverter());
        $environment->addConverter(new HorizontalRuleConverter());
        $environment->addConverter(new ImageConverter());
        $environment->addConverter(new LinkConverter());
        $environment->addConverter(new ListBlockConverter());
        $environment->addConverter(new ListItemConverter());
        $environment->addConverter(new CustomParagraphConverter());
        $environment->addConverter(new PreformattedConverter());
        $environment->addConverter(new TextConverter());
        $environment->addConverter(new CheckboxConverter());

        return $environment;
    }
}
