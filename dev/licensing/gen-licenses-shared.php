<?php

declare(strict_types=1);

$warnings = [];

function findLicenseFile(string $packageName, string $packagePath): string
{
    $licenseNameOptions = [
        'license', 'LICENSE', 'License',
        'license.*', 'LICENSE.*', 'License.*',
        'license-*.*', 'LICENSE-*.*', 'License-*.*',
    ];
    $packageDir = dirname($packagePath);

    $foundLicenses = [];
    foreach ($licenseNameOptions as $option) {
        $search = glob("{$packageDir}/$option");
        array_push($foundLicenses, ...$search);
    }

    if (count($foundLicenses) > 1) {
        warn("Package {$packageName}: more than one license file found");
    }

    if (count($foundLicenses) > 0) {
        $fileName = basename($foundLicenses[0]);
        return "{$packageDir}/{$fileName}";
    }

    warn("Package {$packageName}: no license files found");
    return '';
}

function findCopyright(string $licenseFile): string
{
    $fileContents = file_get_contents($licenseFile);
    $pattern = '/^.*?copyright (\(c\)|\d{4})[\s\S]*?(\n\n|\.\n)/mi';
    $matches = [];
    preg_match($pattern, $fileContents, $matches);
    $copyright = trim($matches[0] ?? '');

    if (str_contains($copyright, 'i.e.')) {
        return '';
    }

    $emailPattern = '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i';
    return preg_replace_callback($emailPattern, obfuscateEmail(...), $copyright);
}

function obfuscateEmail(array $matches): string
{
    return preg_replace('/[^@.]/', '*', $matches[1]);
}

function warn(string $text): void
{
    global $warnings;
    $warnings[] = "WARN:" . $text;
}

function getWarnings(): array
{
    global $warnings;
    return $warnings;
}
