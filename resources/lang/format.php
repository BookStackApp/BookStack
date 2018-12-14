#!/usr/bin/env php
<?php

/**
 * Format a language file in the same way as the EN equivalent.
 * Matches the line numbers of translated content.
 */

$args = array_slice($argv, 1);

if (count($args) < 2) {
    errorOut("Please provide a language code as the first argument and a translation file name as the second (./format.php fr activities)");
}

$lang = formatLang($args[0]);
$fileName = explode('.', $args[1])[0];

$enLines = loadLangFileLines('en', $fileName);
$langContent = loadLang($lang, $fileName);
$enContent = loadLang('en', $fileName);

// Calculate the longest top-level key length
$longestKeyLength = longestKey($enContent);

// Start formatted content
$formatted = [];
$mode = 'header';
$arrayKeys = [];

foreach($enLines as $index => $line) {
    $trimLine = trim($line);
    if ($mode === 'header') {
        $formatted[$index] = $line;
        if (str_replace(' ', '', $trimLine) === 'return[') $mode = 'body';
    }

    if ($mode === 'body') {
        $matches = [];

        // Comment
        if (strpos($trimLine, '//') === 0) {
            $formatted[$index] = "\t" . $trimLine;
            continue;
        }

        // Arrays
        $arrayStartMatch = preg_match('/^\'(.*)\'\s+?=>\s+?\[(\],)?\s*?$/', $trimLine, $matches);
        $arrayEndMatch = preg_match('/]\s*,\s*$/', $trimLine);
        $indent = count($arrayKeys) + 1;
        if ($arrayStartMatch === 1) {
            $arrayKeys[] = $matches[1];
            $formatted[$index] = str_repeat(" ", $indent * 4) . str_pad("'{$matches[1]}'", $longestKeyLength) . "=> [";
            if ($arrayEndMatch !== 1) continue;
        }
        if ($arrayEndMatch === 1) {
            unsetArrayByKeys($langContent, $arrayKeys);
            $key = array_pop($arrayKeys);
            if (isset($formatted[$index])) {
                $formatted[$index] .= '],';
            } else {
                $formatted[$index] = str_repeat(" ", ($indent-1) * 4) . "],";
            }
            continue;
        }

        // Translation
        $translationMatch = preg_match('/^\'(.*)\'\s+?=>\s+?\'(.*)?\'.+?$/', $trimLine, $matches);
        if ($translationMatch === 1) {
            $key = $matches[1];
            $keys = array_merge($arrayKeys, [$key]);
            $langVal = getTranslationByKeys($langContent, $keys);
            if (empty($langVal)) continue;

            $keyPad = $longestKeyLength;
            if (count($arrayKeys) === 0) {
                unset($langContent[$key]);
            } else {
                $keyPad = longestKey(getTranslationByKeys($enContent, $arrayKeys));
            }

            $formatted[$index] = formatTranslationLine($key, $langVal, $indent, $keyPad);
            continue;
        }
    }

}

// Fill missing lines
$arraySize = max(array_keys($formatted));
$formatted = array_replace(array_fill(0, $arraySize, ''), $formatted);

// Add remaining translations
$langContent = array_filter($langContent, function($item) {
    return !is_null($item) && !empty($item);
});
if (count($langContent) > 0) {
    $formatted[] = '';
    $formatted[] = "\t// Unmatched";
}
foreach ($langContent as $key => $value) {
    if (is_array($value)) {
        $formatted[] = formatTranslationArray($key, $value);
    } else {
        $formatted[] = formatTranslationLine($key, $value);
    }
}

// Add end line
$formatted[] = '];';
$formatted = implode("\n", $formatted);

writeLangFile($lang, $fileName, $formatted);

function formatTranslationLine(string $key, string $value, int $indent = 1, int $keyPad = 1) {
    $escapedValue = str_replace("'", "\\'", $value);
    return str_repeat(" ", $indent * 4) . str_pad("'{$key}'", $keyPad, ' ') ."=> '{$escapedValue}',";
}

function longestKey(array $array) {
    $top = 0;
    foreach ($array as $key => $value) {
        $keyLen = strlen($key);
        $top = max($top, $keyLen);
    }
    return $top + 3;
}

function formatTranslationArray(string $key, array $array) {
    $arrayPHP = var_export($array, true);
    return "    '{$key}' => {$arrayPHP},";
}

function getTranslationByKeys(array $translations, array $keys) {
    $val = $translations;
    foreach ($keys as $key) {
        $val = $val[$key] ?? '';
        if ($val === '') return '';
    }
    return $val;
}

function unsetArrayByKeys(array &$input, array $keys) {
    $val = &$input;
    $lastIndex = count($keys) - 1;
    foreach ($keys as $index => &$key) {
        if ($index === $lastIndex && is_array($val)) {
            unset($val[$key]);
        }
        if (!is_array($val)) return;
        $val = &$val[$key] ?? [];
    }
}

function writeLangFile(string $lang, string $fileName, string $content) {
    $path = __DIR__ . "/{$lang}/{$fileName}.php";
    if (!file_exists($path)) {
        errorOut("Expected translation file '{$path}' does not exist");
    }
    file_put_contents($path, $content);
}

function loadLangFileLines(string $lang, string $fileName) {
    $path = __DIR__ . "/{$lang}/{$fileName}.php";
    if (!file_exists($path)) {
        errorOut("Expected translation file '{$path}' does not exist");
    }
    $lines = explode("\n", file_get_contents($path));
    return array_map(function($line) {
        return trim($line, "\r");
    }, $lines);
}

function loadLang(string $lang, string $fileName) {
    $path = __DIR__ . "/{$lang}/{$fileName}.php";
    if (!file_exists($path)) {
        errorOut("Expected translation file '{$path}' does not exist");
    }

    $fileData = include($path);
    return $fileData;
}

function formatLang($lang) {
    $langParts = explode('_', strtoupper($lang));
    $langParts[0] = strtolower($langParts[0]);
    return implode('_', $langParts);
}

function dd($content) {
    print_r($content);
    exit(1);
}

function info($text) {
    echo "\e[34m" . $text . "\e[0m\n";
}

function errorOut($text) {
    echo "\e[31m" . $text . "\e[0m\n";
    exit(1);
}