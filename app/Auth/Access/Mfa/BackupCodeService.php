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
        for ($i = 0; $i < 16; $i++) {
            $code = Str::random(5) . '-' . Str::random(5);
            $codes[] = strtolower($code);
        }
        return $codes;
    }
}