<?php

namespace Laravel\Socialite\Two;

use Illuminate\Support\Arr;
use GuzzleHttp\ClientInterface;
use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

class OktaProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Scopes defintions.
     *
     * @see http://developer.okta.com/docs/api/resources/oidc.html#scopes
     */
    const SCOPE_OPENID = 'openid';
    const SCOPE_PROFILE = 'profile';
    const SCOPE_EMAIL = 'email';
    const SCOPE_ADDRESS = 'address';
    const SCOPE_PHONE = 'phone';
    const SCOPE_OFFLINE_ACCESS = 'offline_access';

    /**
     * Okta organization url.
     *
     * @var string
     */
    protected $oktaUrl;

    /**
     * {@inheritdoc}
     */
    protected $scopes = [
        'openid',
        'profile',
        'email',
    ];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * Set the okta base organization url.
     *
     * @param string $oktaUrl
     */
    public function setOktaUrl($oktaUrl)
    {
        $this->oktaUrl = $oktaUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->oktaUrl.'/oauth2/v1/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->oktaUrl.'/oauth2/v1/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see http://developer.okta.com/docs/api/resources/oidc.html#get-user-information
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->oktaUrl.'/oauth2/v1/userinfo', [
            'headers' => [
                //'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     *
     * @see http://developer.okta.com/docs/api/resources/oidc.html#response-example-success
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => Arr::get($user, 'sub'),
            'email' => Arr::get($user, 'email'),
            'email_verified' => Arr::get($user, 'email_verified', false),
            'nickname' => Arr::get($user, 'nickname'),
            'name' => Arr::get($user, 'name'),
            'first_name' => Arr::get($user, 'given_name'),
            'last_name' => Arr::get($user, 'family_name'),
            'profileUrl' => Arr::get($user, 'profile'),
            'address' => Arr::get($user, 'address'),
            'phone' => Arr::get($user, 'phone'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenResponse($code)
    {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';

        $options = [
            'headers' => [
                'Authorization' => 'Basic '.base64_encode($this->clientId.':'.$this->clientSecret),
            ],
            $postKey => $this->getTokenFields($code),
        ];

        $response = $this->getHttpClient()->post($this->getTokenUrl(), $options);

        return json_decode($response->getBody(), true);
    }
}