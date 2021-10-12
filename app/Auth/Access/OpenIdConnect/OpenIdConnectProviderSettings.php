<?php

namespace BookStack\Auth\Access\OpenIdConnect;

use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Cache\Repository;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

/**
 * OpenIdConnectProviderSettings
 * Acts as a DTO for settings used within the oidc request and token handling.
 * Performs auto-discovery upon request.
 */
class OpenIdConnectProviderSettings
{
    /**
     * @var string
     */
    public $issuer;

    /**
     * @var string
     */
    public $clientId;

    /**
     * @var string
     */
    public $clientSecret;

    /**
     * @var string
     */
    public $redirectUri;

    /**
     * @var string
     */
    public $authorizationEndpoint;

    /**
     * @var string
     */
    public $tokenEndpoint;

    /**
     * @var string[]|array[]
     */
    public $keys = [];

    public function __construct(array $settings)
    {
        $this->applySettingsFromArray($settings);
        $this->validateInitial();
    }

    /**
     * Apply an array of settings to populate setting properties within this class.
     */
    protected function applySettingsFromArray(array $settingsArray)
    {
        foreach ($settingsArray as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Validate any core, required properties have been set.
     * @throws InvalidArgumentException
     */
    protected function validateInitial()
    {
        $required = ['clientId', 'clientSecret', 'redirectUri', 'issuer'];
        foreach ($required as $prop) {
            if (empty($this->$prop)) {
                throw new InvalidArgumentException("Missing required configuration \"{$prop}\" value");
            }
        }

        if (strpos($this->issuer, 'https://') !== 0) {
            throw new InvalidArgumentException("Issuer value must start with https://");
        }
    }

    /**
     * Perform a full validation on these settings.
     * @throws InvalidArgumentException
     */
    public function validate(): void
    {
        $this->validateInitial();
        $required = ['keys', 'tokenEndpoint', 'authorizationEndpoint'];
        foreach ($required as $prop) {
            if (empty($this->$prop)) {
                throw new InvalidArgumentException("Missing required configuration \"{$prop}\" value");
            }
        }
    }

    /**
     * Discover and autoload settings from the configured issuer.
     * @throws IssuerDiscoveryException
     */
    public function discoverFromIssuer(ClientInterface $httpClient, Repository $cache, int $cacheMinutes)
    {
        try {
            $cacheKey = 'oidc-discovery::' . $this->issuer;
            $discoveredSettings = $cache->remember($cacheKey, $cacheMinutes * 60, function() use ($httpClient) {
                return $this->loadSettingsFromIssuerDiscovery($httpClient);
            });
            $this->applySettingsFromArray($discoveredSettings);
        } catch (ClientExceptionInterface $exception) {
            throw new IssuerDiscoveryException("HTTP request failed during discovery with error: {$exception->getMessage()}");
        }
    }

    /**
     * @throws IssuerDiscoveryException
     * @throws ClientExceptionInterface
     */
    protected function loadSettingsFromIssuerDiscovery(ClientInterface $httpClient): array
    {
        $issuerUrl = rtrim($this->issuer, '/') . '/.well-known/openid-configuration';
        $request = new Request('GET', $issuerUrl);
        $response = $httpClient->sendRequest($request);
        $result = json_decode($response->getBody()->getContents(), true);

        if (empty($result) || !is_array($result)) {
            throw new IssuerDiscoveryException("Error discovering provider settings from issuer at URL {$issuerUrl}");
        }

        if ($result['issuer'] !== $this->issuer) {
            throw new IssuerDiscoveryException("Unexpected issuer value found on discovery response");
        }

        $discoveredSettings = [];

        if (!empty($result['authorization_endpoint'])) {
            $discoveredSettings['authorizationEndpoint'] = $result['authorization_endpoint'];
        }

        if (!empty($result['token_endpoint'])) {
            $discoveredSettings['tokenEndpoint'] = $result['token_endpoint'];
        }

        if (!empty($result['jwks_uri'])) {
            $keys = $this->loadKeysFromUri($result['jwks_uri'], $httpClient);
            $discoveredSettings['keys'] = array_filter($keys);
        }

        return $discoveredSettings;
    }

    /**
     * Filter the given JWK keys down to just those we support.
     */
    protected function filterKeys(array $keys): array
    {
        return array_filter($keys, function(array $key) {
            return $key['key'] === 'RSA' && $key['use'] === 'sig' && $key['alg'] === 'RS256';
        });
    }

    /**
     * Return an array of jwks as PHP key=>value arrays.
     * @throws ClientExceptionInterface
     * @throws IssuerDiscoveryException
     */
    protected function loadKeysFromUri(string $uri, ClientInterface $httpClient): array
    {
        $request = new Request('GET', $uri);
        $response = $httpClient->sendRequest($request);
        $result = json_decode($response->getBody()->getContents(), true);

        if (empty($result) || !is_array($result) || !isset($result['keys'])) {
            throw new IssuerDiscoveryException("Error reading keys from issuer jwks_uri");
        }

        return $result['keys'];
    }

    /**
     * Get the settings needed by an OAuth provider, as a key=>value array.
     */
    public function arrayForProvider(): array
    {
        $settingKeys = ['clientId', 'clientSecret', 'redirectUri', 'authorizationEndpoint', 'tokenEndpoint'];
        $settings = [];
        foreach ($settingKeys as $setting) {
            $settings[$setting] = $this->$setting;
        }
        return $settings;
    }
}