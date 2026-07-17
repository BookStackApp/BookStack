<?php

namespace BookStack\Util\HtmlPurifier;

use BookStack\App\AppVersion;
use BookStack\Util\HtmlPurifier\Filters\UriLimitFileProtocolToAnchors;
use BookStack\Util\UrlFilter;
use HTMLPurifier;
use HTMLPurifier_Config;
use HTMLPurifier_DefinitionCache_Serializer;
use HTMLPurifier_HTML5Config;
use HTMLPurifier_HTMLDefinition;
use HTMLPurifier_URIDefinition;

/**
 * Provides a configured HTML Purifier instance.
 * https://github.com/ezyang/htmlpurifier
 * Also uses this to extend support to HTML5 elements:
 * https://github.com/xemlock/htmlpurifier-html5
 */
class ConfiguredHtmlPurifier
{
    protected HTMLPurifier $purifier;
    protected static bool $cachedChecked = false;

    public function __construct()
    {
        // This is done by the web-server at run-time, with the existing
        // storage/framework/cache folder to ensure we're using a server-writable folder.
        $cachePath = storage_path('framework/cache/purifier');
        $this->createCacheFolderIfNeeded($cachePath);

        $config = HTMLPurifier_HTML5Config::createDefault();
        $this->setConfig($config, $cachePath);
        $this->resetCacheIfNeeded($config);

        $htmlDef = $config->getDefinition('HTML', true, true);
        if ($htmlDef instanceof HTMLPurifier_HTMLDefinition) {
            $this->configureHtmlDefinition($htmlDef);
        }

        $uriDef = $config->getDefinition('URI', true, true);
        if ($uriDef instanceof HTMLPurifier_URIDefinition) {
            $this->configureUriDefinition($uriDef);
        }

        $this->purifier = new HTMLPurifier($config);
    }

    protected function createCacheFolderIfNeeded(string $cachePath): void
    {
        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0777, true);
        }
    }

    protected function resetCacheIfNeeded(HTMLPurifier_Config $config): void
    {
        if (self::$cachedChecked) {
            return;
        }

        $cachedForVersion = cache('htmlpurifier::cache-version');
        $appVersion = AppVersion::get();
        if ($cachedForVersion !== $appVersion) {
            foreach (['HTML', 'CSS', 'URI'] as $name) {
                $cache = new HTMLPurifier_DefinitionCache_Serializer($name);
                $cache->flush($config);
            }
            cache()->set('htmlpurifier::cache-version', $appVersion);
        }

        self::$cachedChecked = true;
    }

    protected function setConfig(HTMLPurifier_Config $config, string $cachePath): void
    {
        $config->set('Cache.SerializerPath', $cachePath);
        $config->set('Core.AllowHostnameUnderscore', true);
        $config->set('CSS.AllowTricky', true);
        $config->set('HTML.SafeIframe', true);
        $config->set('HTML.TargetNoopener', false);
        $config->set('HTML.TargetNoreferrer', false);
        $config->set('Attr.EnableID', true);
        $config->set('Attr.ID.HTML5', true);
        $config->set('Output.FixInnerHTML', false);
        $config->set('URI.SafeIframeRegexp', '%^(http://|https://|//)%');

        $allowedSchemes = UrlFilter::getAllowedSchemes();
        $allowedSchemesSetting = [];
        foreach ($allowedSchemes as $scheme) {
            $allowedSchemesSetting[$scheme] = true;
        }
        $config->set('URI.AllowedSchemes', $allowedSchemesSetting);

         // $config->set('Cache.DefinitionImpl', null); // Disable cache during testing
    }

    protected function configureHtmlDefinition(HTMLPurifier_HTMLDefinition $definition): void
    {
        // Allow the object element
        $definition->addElement(
            'object',
            'Inline',
            'Flow',
            'Common',
            [
                'data'   => 'URI',
                'type'   => 'Text',
                'width'  => 'Length',
                'height' => 'Length',
            ]
        );

        // Allow the embed element
        $definition->addElement(
            'embed',
            'Inline',
            'Empty',
            'Common',
            [
                'src'   => 'URI',
                'type'   => 'Text',
                'width'  => 'Length',
                'height' => 'Length',
            ]
        );

        // Allow checkbox inputs
        $definition->addElement(
            'input',
            'Formctrl',
            'Empty',
            'Common',
            [
                'checked' => 'Bool#checked',
                'disabled' => 'Bool#disabled',
                'name' => 'Text',
                'readonly' => 'Bool#readonly',
                'type' => 'Enum#checkbox',
                'value' => 'Text',
            ]
        );

        // Allow the drawio-diagram attribute on div elements
        $definition->addAttribute(
            'div',
            'drawio-diagram',
            'Number',
        );

        // Allow target="_blank" on links
        $definition->addAttribute('a', 'target', 'Enum#_blank');

        // Allow mention-ids on links
        $definition->addAttribute('a', 'data-mention-user-id', 'Number');

        // Set up custom handler for srcset to limit accepted types
        $definition->addAttribute('img', 'srcset', new SrcsetAttrDef());
        $definition->addAttribute('source', 'srcset', new SrcsetAttrDef());
    }

    protected function configureUriDefinition(HTMLPurifier_URIDefinition $definition): void
    {
        $definition->registerFilter(new UriLimitFileProtocolToAnchors());
    }

    public function purify(string $html): string
    {
        return $this->purifier->purify($html);
    }
}
