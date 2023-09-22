<?php

namespace BookStack\Access\Oidc;

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
class OidcProviderSettings
{
    public string $issuer;
    public string $clientId;
    public string $clientSecret;
    public ?string $redirectUri;
    public ?string $authorizationEndpoint;
    public ?string $tokenEndpoint;

    /**
     * @var string[]|array[]
     */
    public ?array $keys = [];

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
     *
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

        if (!str_starts_with($this->issuer, 'https://')) {
            throw new InvalidArgumentException('Issuer value must start with https://');
        }
    }

    /**
     * Perform a full validation on these settings.
     *
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
     *
     * @throws OidcIssuerDiscoveryException
     */
    public function discoverFromIssuer(ClientInterface $httpClient, Repository $cache, int $cacheMinutes)
    {
        try {
            $cacheKey = 'oidc-discovery::' . $this->issuer;
            $discoveredSettings = $cache->remember($cacheKey, $cacheMinutes * 60, function () use ($httpClient) {
                return $this->loadSettingsFromIssuerDiscovery($httpClient);
            });
            $this->applySettingsFromArray($discoveredSettings);
        } catch (ClientExceptionInterface $exception) {
            throw new OidcIssuerDiscoveryException("HTTP request failed during discovery with error: {$exception->getMessage()}");
        }
    }

    /**
     * @throws OidcIssuerDiscoveryException
     * @throws ClientExceptionInterface
     */
    protected function loadSettingsFromIssuerDiscovery(ClientInterface $httpClient): array
    {
        $issuerUrl = rtrim($this->issuer, '/') . '/.well-known/openid-configuration';
        $request = new Request('GET', $issuerUrl);
        $response = $httpClient->sendRequest($request);
        $result = json_decode($response->getBody()->getContents(), true);

        if (empty($result) || !is_array($result)) {
            throw new OidcIssuerDiscoveryException("Error discovering provider settings from issuer at URL {$issuerUrl}");
        }

        if ($result['issuer'] !== $this->issuer) {
            throw new OidcIssuerDiscoveryException('Unexpected issuer value found on discovery response');
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
            $discoveredSettings['keys'] = $this->filterKeys($keys);
        }

        return $discoveredSettings;
    }

    /**
     * Filter the given JWK keys down to just those we support.
     */
    protected function filterKeys(array $keys): array
    {
        return array_filter($keys, function (array $key) {
            $alg = $key['alg'] ?? 'RS256';
            $use = $key['use'] ?? 'sig';

            return $key['kty'] === 'RSA' && $use === 'sig' && $alg === 'RS256';
        });
    }

    /**
     * Return an array of jwks as PHP key=>value arrays.
     *
     * @throws ClientExceptionInterface
     * @throws OidcIssuerDiscoveryException
     */
    protected function loadKeysFromUri(string $uri, ClientInterface $httpClient): array
    {
        $request = new Request('GET', $uri);
        $response = $httpClient->sendRequest($request);
        $result = json_decode($response->getBody()->getContents(), true);

        if (empty($result) || !is_array($result) || !isset($result['keys'])) {
            throw new OidcIssuerDiscoveryException('Error reading keys from issuer jwks_uri');
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
