<?php

namespace Laravel\Socialite;

use InvalidArgumentException;
use Illuminate\Support\Manager;
use Laravel\Socialite\One\TwitterProvider;
use League\OAuth1\Client\Server\Twitter as TwitterServer;

class SocialiteManager extends Manager implements Contracts\Factory {
	/**
	 * Get a driver instance.
	 *
	 * @param string $driver        	
	 * @return mixed
	 */
	public function with($driver) {
		return $this->driver ( $driver );
	}
	
	/**
	 * Create an instance of the specified driver.
	 *
	 * @return \Laravel\Socialite\Two\AbstractProvider
	 */
	protected function createGithubDriver() {
		$config = $this->app ['config'] ['services.github'];
		
		return $this->buildProvider ( 'Laravel\Socialite\Two\GithubProvider', $config );
	}
	
	/**
	 * Create an instance of the specified driver.
	 *
	 * @return \Laravel\Socialite\Two\AbstractProvider
	 */
	protected function createFacebookDriver() {
		$config = $this->app ['config'] ['services.facebook'];
		
		return $this->buildProvider ( 'Laravel\Socialite\Two\FacebookProvider', $config );
	}
	
	/**
	 * Creates Okta provider and bind it to Laravel Socialite.
	 *
	 * @return \Laravel\Socialite\Two\AbstractProvider
	 */
	public function createOktaDriver() {
		$config = $this->app ['config'] ['services.okta'];
		
		$provider = $this->buildProvider ( 'Laravel\Socialite\Two\OktaProvider', $config );
		
		$provider->setOktaUrl ( $config ['url'] );
		
		return $provider;
	}
	
	/**
	 * Create an instance of the specified driver.
	 *
	 * @return \Laravel\Socialite\Two\AbstractProvider
	 */
	protected function createGoogleDriver() {
		$config = $this->app ['config'] ['services.google'];
		
		return $this->buildProvider ( 'Laravel\Socialite\Two\GoogleProvider', $config );
	}
	
	/**
	 * Create an instance of the specified driver.
	 *
	 * @return \Laravel\Socialite\Two\AbstractProvider
	 */
	protected function createLinkedinDriver() {
		$config = $this->app ['config'] ['services.linkedin'];
		
		return $this->buildProvider ( 'Laravel\Socialite\Two\LinkedInProvider', $config );
	}
	
	/**
	 * Create an instance of the specified driver.
	 *
	 * @return \Laravel\Socialite\Two\AbstractProvider
	 */
	protected function createBitbucketDriver() {
		$config = $this->app ['config'] ['services.bitbucket'];
		
		return $this->buildProvider ( 'Laravel\Socialite\Two\BitbucketProvider', $config );
	}
	
	/**
	 * Build an OAuth 2 provider instance.
	 *
	 * @param string $provider        	
	 * @param array $config        	
	 * @return \Laravel\Socialite\Two\AbstractProvider
	 */
	public function buildProvider($provider, $config) {
		return new $provider ( $this->app ['request'], $config ['client_id'], $config ['client_secret'], $config ['redirect'] );
	}
	
	/**
	 * Create an instance of the specified driver.
	 *
	 * @return \Laravel\Socialite\One\AbstractProvider
	 */
	protected function createTwitterDriver() {
		$config = $this->app ['config'] ['services.twitter'];
		
		return new TwitterProvider ( $this->app ['request'], new TwitterServer ( $this->formatConfig ( $config ) ) );
	}
	
	/**
	 * Format the server configuration.
	 *
	 * @param array $config        	
	 * @return array
	 */
	public function formatConfig(array $config) {
		return array_merge ( [ 
				'identifier' => $config ['client_id'],
				'secret' => $config ['client_secret'],
				'callback_uri' => $config ['redirect'] 
		], $config );
	}
	
	/**
	 * Get the default driver name.
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return string
	 */
	public function getDefaultDriver() {
		throw new InvalidArgumentException ( 'No Socialite driver was specified.' );
	}
}
