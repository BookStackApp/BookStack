<?php

namespace BookStack\Uploads;

use BookStack\Util\WebSafeMimeSniffer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use InvalidArgumentException;

class Base64UriMimeRule implements ValidationRule
{
    public function __construct(
        protected string $requiredMime
    ) {
        if (empty($requiredMime)) {
            throw new InvalidArgumentException('A required mime type must be provided');
        }
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $imageDataEncoded = explode(',', $value, 2)[1] ?? '';
        if (empty($imageDataEncoded)) {
            $fail('validation.base64_uri_mime')->translate(['mime' => $this->requiredMime]);
            return;
        }

        $imageData = base64_decode($imageDataEncoded);
        if (empty($imageData)) {
            $fail('validation.base64_uri_mime')->translate(['mime' => $this->requiredMime]);
            return;
        }

        $sniffer = new WebSafeMimeSniffer();
        $mime = $sniffer->sniff($imageData);

        if ($mime !== $this->requiredMime) {
            $fail('validation.base64_uri_mime')->translate(['mime' => $this->requiredMime]);
        }
    }
}
