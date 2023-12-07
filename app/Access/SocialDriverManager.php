<?php

namespace BookStack\Access;

use BookStack\Exceptions\SocialDriverNotConfigured;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use SocialiteProviders\Manager\SocialiteWasCalled;

class SocialDriverManager
{
    /**
     * The default built-in social drivers we support.
     *
     * @var string[]
     */
    protected array $validDrivers = [
        'google',
        'github',
        'facebook',
        'slack',
        'twitter',
        'azure',
        'okta',
        'gitlab',
        'twitch',
        'discord',
    ];

    /**
     * Callbacks to run when configuring a social driver
     * for an initial redirect action.
     * Array is keyed by social driver name.
     * Callbacks are passed an instance of the driver.
     *
     * @var array<string, callable>
     */
    protected array $configureForRedirectCallbacks = [];

    /**
     * Check if the current config for the given driver allows auto-registration.
     */
    public function isAutoRegisterEnabled(string $driver): bool
    {
        return $this->getDriverConfigProperty($driver, 'auto_register') === true;
    }

    /**
     * Check if the current config for the given driver allow email address auto-confirmation.
     */
    public function isAutoConfirmEmailEnabled(string $driver): bool
    {
        return $this->getDriverConfigProperty($driver, 'auto_confirm') === true;
    }

    /**
     * Gets the names of the active social drivers, keyed by driver id.
     * @returns array<string, string>
     */
    public function getActive(): array
    {
        $activeDrivers = [];

        foreach ($this->validDrivers as $driverKey) {
            if ($this->checkDriverConfigured($driverKey)) {
                $activeDrivers[$driverKey] = $this->getName($driverKey);
            }
        }

        return $activeDrivers;
    }

    /**
     * Get the configure-for-redirect callback for the given driver.
     * This is a callable that allows modification of the driver at redirect time.
     * Commonly used to perform custom dynamic configuration where required.
     * The callback is passed a \Laravel\Socialite\Contracts\Provider instance.
     */
    public function getConfigureForRedirectCallback(string $driver): callable
    {
        return $this->configureForRedirectCallbacks[$driver] ?? (fn() => true);
    }

    /**
     * Add a custom socialite driver to be used.
     * Driver name should be lower_snake_case.
     * Config array should mirror the structure of a service
     * within the `Config/services.php` file.
     * Handler should be a Class@method handler to the SocialiteWasCalled event.
     */
    public function addSocialDriver(
        string $driverName,
        array $config,
        string $socialiteHandler,
        callable $configureForRedirect = null
    ) {
        $this->validDrivers[] = $driverName;
        config()->set('services.' . $driverName, $config);
        config()->set('services.' . $driverName . '.redirect', url('/login/service/' . $driverName . '/callback'));
        config()->set('services.' . $driverName . '.name', $config['name'] ?? $driverName);
        Event::listen(SocialiteWasCalled::class, $socialiteHandler);
        if (!is_null($configureForRedirect)) {
            $this->configureForRedirectCallbacks[$driverName] = $configureForRedirect;
        }
    }

    /**
     * Get the presentational name for a driver.
     */
    protected function getName(string $driver): string
    {
        return $this->getDriverConfigProperty($driver, 'name') ?? '';
    }

    protected function getDriverConfigProperty(string $driver, string $property): mixed
    {
        return config("services.{$driver}.{$property}");
    }

    /**
     * Ensure the social driver is correct and supported.
     *
     * @throws SocialDriverNotConfigured
     */
    public function ensureDriverActive(string $driverName): void
    {
        if (!in_array($driverName, $this->validDrivers)) {
            abort(404, trans('errors.social_driver_not_found'));
        }

        if (!$this->checkDriverConfigured($driverName)) {
            throw new SocialDriverNotConfigured(trans('errors.social_driver_not_configured', ['socialAccount' => Str::title($driverName)]));
        }
    }

    /**
     * Check a social driver has been configured correctly.
     */
    protected function checkDriverConfigured(string $driver): bool
    {
        $lowerName = strtolower($driver);
        $configPrefix = 'services.' . $lowerName . '.';
        $config = [config($configPrefix . 'client_id'), config($configPrefix . 'client_secret'), config('services.callback_url')];

        return !in_array(false, $config) && !in_array(null, $config);
    }
}
