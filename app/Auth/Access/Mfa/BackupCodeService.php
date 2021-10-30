<?php

namespace BookStack\Auth\Access\Mfa;

use Illuminate\Support\Str;

class BackupCodeService
{
    /**
     * Generate a new set of 16 backup codes.
     */
    public function generateNewSet(): array
    {
        $codes = [];
        while (count($codes) < 16) {
            $code = Str::random(5) . '-' . Str::random(5);
            if (!in_array($code, $codes)) {
                $codes[] = strtolower($code);
            }
        }

        return $codes;
    }

    /**
     * Check if the given code matches one of the available options.
     */
    public function inputCodeExistsInSet(string $code, string $codeSet): bool
    {
        $cleanCode = $this->cleanInputCode($code);
        $codes = json_decode($codeSet);

        return in_array($cleanCode, $codes);
    }

    /**
     * Remove the given input code from the given available options.
     * Will return a JSON string containing the codes.
     */
    public function removeInputCodeFromSet(string $code, string $codeSet): string
    {
        $cleanCode = $this->cleanInputCode($code);
        $codes = json_decode($codeSet);
        $pos = array_search($cleanCode, $codes, true);
        array_splice($codes, $pos, 1);

        return json_encode($codes);
    }

    /**
     * Count the number of codes in the given set.
     */
    public function countCodesInSet(string $codeSet): int
    {
        return count(json_decode($codeSet));
    }

    protected function cleanInputCode(string $code): string
    {
        return strtolower(str_replace(' ', '-', trim($code)));
    }
}
