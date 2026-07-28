<?php

namespace BookStack\Util\HtmlPurifier;

use HTMLPurifier_AttrDef;
use HTMLPurifier_AttrDef_URI;

/**
 * A custom attribute definition for handling Srcset attributes.
 * Has been provided upstream via:
 * https://github.com/xemlock/htmlpurifier-html5/pull/91
 * but awaiting review/merge.
 */
class SrcsetAttrDef extends HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $sources = $this->parseImageSources($string);
        if (empty($sources)) {
            return false;
        }

        $uriFilter = new HTMLPurifier_AttrDef_URI(true);

        $filtered = array();
        foreach ($sources as $source) {
            $uri = $source['uri'];
            $descriptor = $source['descriptor'];
            $validatedUri = $uriFilter->validate($uri, $config, $context);
            if (is_string($validatedUri)) {
                if ($descriptor) {
                    $filtered[] = $validatedUri . ' ' . $source['descriptor'];
                } else {
                    $filtered[] = $validatedUri;
                }
            }
        }

        if (empty($filtered)) {
            return false;
        }

        return implode(', ', $filtered);
    }

    /**
     * Parse the image source from srcset attribute text.
     * Returns false if it's found to be invalid, otherwise
     * returns an array of uri and descriptor combinations.
     *
     * This aims to follow the WHATWG parsing spec as per:
     * https://html.spec.whatwg.org/multipage/images.html#parsing-a-srcset-attribute
     *
     * @param string $string
     * @return array{uri: string, descriptor: string}[]|false
     */
    private function parseImageSources($string)
    {
        $imageSources = array();
        $asciiWhitespace = " \n\r\t\f";
        $asciiWhiteSpaceComma = $asciiWhitespace . ',';
        $input = trim($string, $asciiWhiteSpaceComma);

        if ($input === "") {
            return false;
        }

        $position = 0;
        while ($position < strlen($input)) {
            $position += strspn($input, $asciiWhitespace, $position);
            $urlEnd = $position + strcspn($input, $asciiWhitespace, $position);
            $url = substr($input, $position, $urlEnd - $position);
            $position = $urlEnd;
            $descriptors = array();

            if (strpos($url, ',') === strlen($url) - 1) {
                $url = rtrim($url, ',');
            } else {
                $position += strspn($input, $asciiWhitespace, $position);
                $currentDescriptor = '';
                $state = 'in_descriptor';
                while (true) {
                    if ($position < strlen($input)) {
                        $c = $input[$position];
                    } else {
                        $c = null;
                    }

                    if ($state === 'in_descriptor') {
                        if ($c !== null && str_contains($asciiWhitespace, $c)) {
                            if ($currentDescriptor !== '') {
                                $descriptors[] = $currentDescriptor;
                            }
                            $state = 'after_descriptor';
                        } else if ($c === ',') {
                            $position++;
                            if ($currentDescriptor !== '') {
                                $descriptors[] = $currentDescriptor;
                            }
                            break;
                        } else if ($c === '(') {
                            $currentDescriptor .= $c;
                            $state = 'in_parens';
                        } else if ($c === null) {
                            if ($currentDescriptor !== '') {
                                $descriptors[] = $currentDescriptor;
                            }
                            break;
                        } else {
                            $currentDescriptor .= $c;
                        }
                    } else if ($state === 'in_parens') {
                        if ($c === ')') {
                            $currentDescriptor .= $c;
                            $state = 'in_descriptor';
                        } else if ($c === null) {
                            $descriptors[] = $currentDescriptor;
                            break;
                        } else {
                            $currentDescriptor .= $c;
                        }
                    } else {
                        if ($c !== null && str_contains($asciiWhitespace, $c)) {
                            // Stay in this state
                        } else if ($c === null) {
                            break;
                        } else {
                            $state = 'in_descriptor';
                            $position--;
                        }
                    }

                    $position++;
                }
            }

            $descriptor = $this->formatDescriptor($descriptors);

            if ($url && $descriptor !== false) {
                $imageSources[] = array(
                    'uri' => $url,
                    'descriptor' => $descriptor,
                );
            }
        }

        return $imageSources;
    }

    /**
     * Parse and format a single descriptor from an array of potential
     * descriptor strings. Returns empty if valid but no descriptor.
     * Returns false if invalid.
     * @param string[] $descriptors
     * @return false|string
     */
    private function formatDescriptor(array $descriptors)
    {
        $error = false;
        $width = '';
        $density = '';
        $futureCompatH = '';

        foreach ($descriptors as $descriptor) {
            $descriptor = trim($descriptor);
            if ($descriptor === '') {
                continue;
            }

            $unit = $descriptor[strlen($descriptor) - 1];
            $number = trim(substr($descriptor, 0, -1));

            if ($unit === 'w' && filter_var($number, FILTER_VALIDATE_INT) && intval($number) >= 0) {
                if (!empty($width) || !empty($density) || intval($number) === 0) {
                    $error = true;
                }
                $width = $number;
            } else if ($unit === 'x' && filter_var($number, FILTER_VALIDATE_FLOAT)) {
                if (!empty($width) || !empty($density) || !empty($futureCompatH) || floatval($number) < 0) {
                    $error = true;
                }
                $density = $number;
            } else if ($unit === 'h' && filter_var($number, FILTER_VALIDATE_INT) && intval($number) >= 0) {
                if (!empty($futureCompatH) || !empty($density)) {
                    $error = true;
                }
                $futureCompatH = $number;
            } else {
                $error = true;
            }
        }

        if (!empty($futureCompatH) && empty($width)) {
            $error = true;
        }

        if ($error) {
            return false;
        }

        if ($width) {
            return $width . 'w';
        }

        if ($density) {
            return $density . 'x';
        }

        return '';
    }
}
