<?php

namespace BookStack\Access\Oidc;

class OidcJwtWithClaims implements ProvidesClaims
{
    protected array $header;
    protected array $payload;
    protected string $signature;
    protected string $issuer;
    protected array $tokenParts = [];

    /**
     * @var array[]|string[]
     */
    protected array $keys;

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
     * Validate common parts of OIDC JWT tokens.
     *
     * @throws OidcInvalidTokenException
     */
    public function validateCommonTokenDetails(string $clientId): bool
    {
        $this->validateTokenStructure();
        $this->validateTokenSignature();
        $this->validateCommonClaims($clientId);

        return true;
    }

    /**
     * Fetch a specific claim from this token.
     * Returns null if it is null or does not exist.
     */
    public function getClaim(string $claim): mixed
    {
        return $this->payload[$claim] ?? null;
    }

    /**
     * Get all returned claims within the token.
     */
    public function getAllClaims(): array
    {
        return $this->payload;
    }

    /**
     * Replace the existing claim data of this token with that provided.
     */
    public function replaceClaims(array $claims): void
    {
        $this->payload = $claims;
    }

    /**
     * Validate the structure of the given token and ensure we have the required pieces.
     * As per https://datatracker.ietf.org/doc/html/rfc7519#section-7.2.
     *
     * @throws OidcInvalidTokenException
     */
    protected function validateTokenStructure(): void
    {
        foreach (['header', 'payload'] as $prop) {
            if (empty($this->$prop) || !is_array($this->$prop)) {
                throw new OidcInvalidTokenException("Could not parse out a valid {$prop} within the provided token");
            }
        }

        if (empty($this->signature) || !is_string($this->signature)) {
            throw new OidcInvalidTokenException('Could not parse out a valid signature within the provided token');
        }
    }

    /**
     * Validate the signature of the given token and ensure it validates against the provided key.
     *
     * @throws OidcInvalidTokenException
     */
    protected function validateTokenSignature(): void
    {
        if ($this->header['alg'] !== 'RS256') {
            throw new OidcInvalidTokenException("Only RS256 signature validation is supported. Token reports using {$this->header['alg']}");
        }

        $parsedKeys = array_map(function ($key) {
            try {
                return new OidcJwtSigningKey($key);
            } catch (OidcInvalidKeyException $e) {
                throw new OidcInvalidTokenException('Failed to read signing key with error: ' . $e->getMessage());
            }
        }, $this->keys);

        $parsedKeys = array_filter($parsedKeys);

        $contentToSign = $this->tokenParts[0] . '.' . $this->tokenParts[1];
        /** @var OidcJwtSigningKey $parsedKey */
        foreach ($parsedKeys as $parsedKey) {
            if ($parsedKey->verify($contentToSign, $this->signature)) {
                return;
            }
        }

        throw new OidcInvalidTokenException('Token signature could not be validated using the provided keys');
    }

    /**
     * Validate common claims for OIDC JWT tokens.
     * As per https://openid.net/specs/openid-connect-basic-1_0.html#IDTokenValidation
     * and https://openid.net/specs/openid-connect-core-1_0.html#UserInfoResponse
     *
     * @throws OidcInvalidTokenException
     */
    protected function validateCommonClaims(string $clientId): void
    {
        // 1. The Issuer Identifier for the OpenID Provider (which is typically obtained during Discovery)
        // MUST exactly match the value of the iss (issuer) Claim.
        if (empty($this->payload['iss']) || $this->issuer !== $this->payload['iss']) {
            throw new OidcInvalidTokenException('Missing or non-matching token issuer value');
        }

        // 2. The Client MUST validate that the aud (audience) Claim contains its client_id value registered
        // at the Issuer identified by the iss (issuer) Claim as an audience. The ID Token MUST be rejected
        // if the ID Token does not list the Client as a valid audience.
        if (empty($this->payload['aud'])) {
            throw new OidcInvalidTokenException('Missing token audience value');
        }

        $aud = is_string($this->payload['aud']) ? [$this->payload['aud']] : $this->payload['aud'];
        if (!in_array($clientId, $aud, true)) {
            throw new OidcInvalidTokenException('Token audience value did not match the expected client_id');
        }
    }
}
