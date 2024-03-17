<?php

namespace BookStack\Access\Mfa;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TotpValidationRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     * Takes the TOTP secret that must be system provided, not user provided.
     */
    public function __construct(
        protected string $secret,
        protected TotpService $totpService,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $passes = $this->totpService->verifyCode($value, $this->secret);
        if (!$passes) {
            $fail(trans('validation.totp'));
        }
    }
}
