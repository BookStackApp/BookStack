<?php

namespace BookStack\Access\Mfa;

use Illuminate\Contracts\Validation\Rule;

class TotpValidationRule implements Rule
{
    protected $secret;
    protected $totpService;

    /**
     * Create a new rule instance.
     * Takes the TOTP secret that must be system provided, not user provided.
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
        $this->totpService = app()->make(TotpService::class);
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value)
    {
        return $this->totpService->verifyCode($value, $this->secret);
    }

    /**
     * Get the validation error message.
     */
    public function message()
    {
        return trans('validation.totp');
    }
}
