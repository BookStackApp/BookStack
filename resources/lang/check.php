#!/usr/bin/env php
<?php

/**
 * Compares translation files to find missing and redundant content.
 */

$args = array_slice($argv, 1);

if (count($args) === 0) {
    errorOut("Please provide a language code as the first argument (./check.php fr)");
}


// Get content from files
$lang = formatLang($args[0]);
$enContent = loadLang('en');
$langContent = loadLang($lang);

if (count($langContent) === 0) {
    errorOut("No language content found for '{$lang}'");
}

info("Checking '{$lang}' translation content against 'en'");

// Track missing lang strings
$missingLangStrings = [];
foreach ($enContent as $enKey => $enStr) {
    if (strpos($enKey, 'settings.language_select.') === 0) {
        unset($langContent[$enKey]);
        continue;
    }
    if (!isset($langContent[$enKey])) {
        $missingLangStrings[$enKey] = $enStr;
        continue;
    }
    unset($langContent[$enKey]);
}

if (count($missingLangStrings) > 0) {
    info("\n========================");
    info("Missing language content");
    info("========================");
    outputFlatArray($missingLangStrings, $lang);
}

if (count($langContent) > 0) {
    info("\n==========================");
    info("Redundant language content");
    info("==========================");
    outputFlatArray($langContent, $lang);
}

function outputFlatArray($arr, $lang) {
    $grouped = [];
    foreach ($arr as $key => $val) {
        $explodedKey = explode('.', $key);
        $group = $explodedKey[0];
        $path = implode('.', array_slice($explodedKey, 1));
        if (!isset($grouped[$group])) $grouped[$group] = [];
        $grouped[$group][$path] = $val;
    }
    foreach ($grouped as $filename => $arr) {
        echo "\e[36m" . $lang . '/' . $filename . ".php\e[0m\n";
        echo json_encode($arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n";
    }
}

function formatLang($lang) {
    $langParts = explode('_', strtoupper($lang));
    $langParts[0] = strtolower($langParts[0]);
    return implode('_', $langParts);
}

function loadLang(string $lang) {
    $dir = __DIR__ . "/{$lang}";
    if (!file_exists($dir)) {
       errorOut("Expected directory '{$dir}' does not exist");
    }
    $files = scandir($dir);
    $data = [];
    foreach ($files as $file) {
        if (substr($file, -4) !== '.php') continue;
        $fileData = include ($dir . '/' . $file);
        $name = substr($file, 0, -4);
        $data[$name] = $fileData;
    }
    return flattenArray($data);
}

function flattenArray(array $arr) {
    $data = [];
    foreach ($arr as $key => $arrItem) {
        if (!is_array($arrItem)) {
            $data[$key] = $arrItem;
            continue;
        }

        $toUse = flattenArray($arrItem);
        foreach ($toUse as $innerKey => $item) {
            $data[$key . '.' . $innerKey] = $item;
        }
    }
    return $data;
}

function info($text) {
    echo "\e[34m" . $text . "\e[0m\n";
}

function errorOut($text) {
    echo "\e[31m" . $text . "\e[0m\n";
    exit(1);
}