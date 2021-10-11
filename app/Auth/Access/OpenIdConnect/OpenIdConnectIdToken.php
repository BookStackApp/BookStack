<?php

namespace BookStack\Auth\Access\OpenIdConnect;

class OpenIdConnectIdToken
{
    /**
     * @var array
     */
    protected $header;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var string
     */
    protected $signature;

    /**
     * @var array[]|string[]
     */
    protected $keys;

    /**
     * @var string
     */
    protected $issuer;

    /**
     * @var array
     */
    protected $tokenParts = [];

    public function __construct(string $token, string $issuer, array $keys)
    {
        $this->keys = $keys;
        $this->issuer = $issuer;
        $this->parse($token);
    }

    /**
     * Parse the token content into its components.
     */
    protected function parse(string $token): void
    {
        $this->tokenParts = explode('.', $token);
        $this->header = $this->parseEncodedTokenPart($this->tokenParts[0]);
        $this->payload = $this->parseEncodedTokenPart($this->tokenParts[1] ?? '');
        $this->signature = $this->base64UrlDecode($this->tokenParts[2] ?? '') ?: '';
    }

    /**
     * Parse a Base64-JSON encoded token part.
     * Returns the data as a key-value array or empty array upon error.
     */
    protected function parseEncodedTokenPart(string $part): array
    {
        $json = $this->base64UrlDecode($part) ?: '{}';
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Base64URL decode. Needs some character conversions to be compatible
     * with PHP's default base64 handling.
     */
    protected function base64UrlDecode(string $encoded): string
    {
        return base64_decode(strtr($encoded, '-_', '+/'));
    }

    /**
     * Validate all possible parts of the id token.
     * @throws InvalidTokenException
     */
    public function validate()
    {
        $this->validateTokenStructure();
        $this->validateTokenSignature();
        $this->validateTokenClaims();
    }

    /**
     * Validate the structure of the given token and ensure we have the required pieces.
     * As per https://datatracker.ietf.org/doc/html/rfc7519#section-7.2
     * @throws InvalidTokenException
     */
    protected function validateTokenStructure(): void
    {
        foreach (['header', 'payload'] as $prop) {
            if (empty($this->$prop) || !is_array($this->$prop)) {
                throw new InvalidTokenException("Could not parse out a valid {$prop} within the provided token");
            }
        }

        if (empty($this->signature) || !is_string($this->signature)) {
            throw new InvalidTokenException("Could not parse out a valid signature within the provided token");
        }
    }

    /**
     * Validate the signature of the given token and ensure it validates against the provided key.
     * @throws InvalidTokenException
     */
    protected function validateTokenSignature(): void
    {
        if ($this->header['alg'] !== 'RS256') {
            throw new InvalidTokenException("Only RS256 signature validation is supported. Token reports using {$this->header['alg']}");
        }

        $parsedKeys = array_map(function($key) {
            try {
                return new JwtSigningKey($key);
            } catch (InvalidKeyException $e) {
                return null;
            }
        }, $this->keys);

        $parsedKeys = array_filter($parsedKeys);

        $contentToSign = $this->tokenParts[0] . '.' . $this->tokenParts[1];
        foreach ($parsedKeys as $parsedKey) {
            if ($parsedKey->verify($contentToSign, $this->signature)) {
                return;
            }
        }

        throw new InvalidTokenException('Token signature could not be validated using the provided keys.');
    }

    /**
     * Validate the claims of the token.
     * As per https://openid.net/specs/openid-connect-basic-1_0.html#IDTokenValidation
     */
    protected function validateTokenClaims(): void
    {
        // TODO
    }

}