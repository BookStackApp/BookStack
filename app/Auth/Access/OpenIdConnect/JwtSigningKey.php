<?php

namespace BookStack\Auth\Access\OpenIdConnect;

use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
use phpseclib3\Math\BigInteger;

class JwtSigningKey
{
    /**
     * @var PublicKey
     */
    protected $key;

    /**
     * Can be created either from a JWK parameter array or local file path to load a certificate from.
     * Examples:
     * 'file:///var/www/cert.pem'
     * ['kty' => 'RSA', 'alg' => 'RS256', 'n' => 'abc123...']
     * @param array|string $jwkOrKeyPath
     * @throws InvalidKeyException
     */
    public function __construct($jwkOrKeyPath)
    {
        if (is_array($jwkOrKeyPath)) {
            $this->loadFromJwkArray($jwkOrKeyPath);
        } else if (is_string($jwkOrKeyPath) && strpos($jwkOrKeyPath, 'file://') === 0) {
            $this->loadFromPath($jwkOrKeyPath);
        } else {
            throw new InvalidKeyException('Unexpected type of key value provided');
        }
    }

    /**
     * @throws InvalidKeyException
     */
    protected function loadFromPath(string $path)
    {
        try {
            $this->key = PublicKeyLoader::load(
                file_get_contents($path)
            )->withPadding(RSA::SIGNATURE_PKCS1);
        } catch (\Exception $exception) {
            throw new InvalidKeyException("Failed to load key from file path with error: {$exception->getMessage()}");
        }

        if (!($this->key instanceof RSA)) {
            throw new InvalidKeyException("Key loaded from file path is not an RSA key as expected");
        }
    }

    /**
     * @throws InvalidKeyException
     */
    protected function loadFromJwkArray(array $jwk)
    {
        if ($jwk['alg'] !== 'RS256') {
            throw new InvalidKeyException("Only RS256 keys are currently supported. Found key using {$jwk['alg']}");
        }

        if (empty($jwk['use'])) {
            throw new InvalidKeyException('A "use" parameter on the provided key is expected');
        }

        if ($jwk['use'] !== 'sig') {
            throw new InvalidKeyException("Only signature keys are currently supported. Found key for use {$jwk['use']}");
        }

        if (empty($jwk['e'])) {
            throw new InvalidKeyException('An "e" parameter on the provided key is expected');
        }

        if (empty($jwk['n'])) {
            throw new InvalidKeyException('A "n" parameter on the provided key is expected');
        }

        $n = strtr($jwk['n'] ?? '', '-_', '+/');

        try {
            /** @var RSA $key */
            $this->key = PublicKeyLoader::load([
                'e' => new BigInteger(base64_decode($jwk['e']), 256),
                'n' => new BigInteger(base64_decode($n), 256),
            ])->withPadding(RSA::SIGNATURE_PKCS1);
        } catch (\Exception $exception) {
            throw new InvalidKeyException("Failed to load key from JWK parameters with error: {$exception->getMessage()}");
        }
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