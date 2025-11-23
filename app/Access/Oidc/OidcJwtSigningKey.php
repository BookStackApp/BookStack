<?php

namespace BookStack\Access\Oidc;

use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
use phpseclib3\Math\BigInteger;

class OidcJwtSigningKey
{
    /**
     * @var PublicKey
     */
    protected $key;

    /**
     * Can be created either from a JWK parameter array or local file path to load a certificate from.
     * Examples:
     * 'file:///var/www/cert.pem'
     * ['kty' => 'RSA', 'alg' => 'RS256', 'n' => 'abc123...'].
     *
     * @param array|string $jwkOrKeyPath
     *
     * @throws OidcInvalidKeyException
     */
    public function __construct($jwkOrKeyPath)
    {
        if (is_array($jwkOrKeyPath)) {
            $this->loadFromJwkArray($jwkOrKeyPath);
        } elseif (is_string($jwkOrKeyPath) && strpos($jwkOrKeyPath, 'file://') === 0) {
            $this->loadFromPath($jwkOrKeyPath);
        } else {
            throw new OidcInvalidKeyException('Unexpected type of key value provided');
        }
    }

    /**
     * @throws OidcInvalidKeyException
     */
    protected function loadFromPath(string $path)
    {
        try {
            $key = PublicKeyLoader::load(
                file_get_contents($path)
            );
        } catch (\Exception $exception) {
            throw new OidcInvalidKeyException("Failed to load key from file path with error: {$exception->getMessage()}");
        }

        if (!$key instanceof RSA) {
            throw new OidcInvalidKeyException('Key loaded from file path is not an RSA key as expected');
        }

        $this->key = $key->withPadding(RSA::SIGNATURE_PKCS1);
    }

    /**
     * @throws OidcInvalidKeyException
     */
    protected function loadFromJwkArray(array $jwk)
    {
        // 'alg' is optional for a JWK, but we will still attempt to validate if
        // it exists otherwise presume it will be compatible.
        $alg = $jwk['alg'] ?? null;
        $algorithm = OidcJwtSigningKeyAlgorithm::tryFrom($alg ?? OidcJwtSigningKeyAlgorithm::RS256->value);
        if ($jwk['kty'] !== 'RSA' || $algorithm === null) {
            throw new OidcInvalidKeyException("Only " . OidcJwtSigningKeyAlgorithm::getSupportedAlgorithms() . " keys are currently supported. Found key using {$alg}");
        }

        // 'use' is optional for a JWK but we assume 'sig' where no value exists since that's what
        // the OIDC discovery spec infers since 'sig' MUST be set if encryption keys come into play.
        $use = $jwk['use'] ?? 'sig';
        if ($use !== 'sig') {
            throw new OidcInvalidKeyException("Only signature keys are currently supported. Found key for use {$jwk['use']}");
        }

        if (empty($jwk['e'])) {
            throw new OidcInvalidKeyException('An "e" parameter on the provided key is expected');
        }

        if (empty($jwk['n'])) {
            throw new OidcInvalidKeyException('A "n" parameter on the provided key is expected');
        }

        $n = strtr($jwk['n'] ?? '', '-_', '+/');

        try {
            $key = PublicKeyLoader::load([
                'e' => new BigInteger(base64_decode($jwk['e']), 256),
                'n' => new BigInteger(base64_decode($n), 256),
            ]);
        } catch (\Exception $exception) {
            throw new OidcInvalidKeyException("Failed to load key from JWK parameters with error: {$exception->getMessage()}");
        }

        if (!$key instanceof RSA) {
            throw new OidcInvalidKeyException('Key loaded from file path is not an RSA key as expected');
        }

        // apply key-algorithm depending hash
        $key = match ($algorithm) {
            OidcJwtSigningKeyAlgorithm::RS256 => $key->withHash('sha256'),
            OidcJwtSigningKeyAlgorithm::RS512 => $key->withHash('sha512'),
        };
        // apply key-algorithm depending padding
        $this->key = match ($algorithm) {
            OidcJwtSigningKeyAlgorithm::RS256,
            OidcJwtSigningKeyAlgorithm::RS512 => $key->withPadding(RSA::SIGNATURE_PKCS1),
        };
    }

    /**
     * Use this key to sign the given content and return the signature.
     */
    public function verify(string $content, string $signature): bool
    {
        return $this->key->verify($content, $signature);
    }

    /**
     * Convert the key to a PEM encoded key string.
     */
    public function toPem(): string
    {
        return $this->key->toString('PKCS8');
    }
}
