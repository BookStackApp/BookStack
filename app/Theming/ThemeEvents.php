<?php

namespace BookStack\Theming;

/**
 * The ThemeEvents used within BookStack.
 *
 * This file details the events that BookStack may fire via the custom
 * theme system, including event names, parameters and expected return types.
 *
 * This system is regarded as semi-stable.
 * We'll look to fix issues with it or migrate old event types but
 * events and their signatures may change in new versions of BookStack.
 * We'd advise testing any usage of these events upon upgrade.
 */
class ThemeEvents
{
    /**
     * Application boot-up.
     * After main services are registered.
     *
     * @param \BookStack\Application $app
     */
    const APP_BOOT = 'app_boot';

    /**
     * Auth login event.
     * Runs right after a user is logged-in to the application by any authentication
     * system as a standard app user. This includes a user becoming logged in
     * after registration. This is not emitted upon API usage.
     *
     * @param string               $authSystem
     * @param \BookStack\Auth\User $user
     */
    const AUTH_LOGIN = 'auth_login';

    /**
     * Auth register event.
     * Runs right after a user is newly registered to the application by any authentication
     * system as a standard app user. This includes auto-registration systems used
     * by LDAP, SAML and social systems. It only includes self-registrations.
     *
     * @param string               $authSystem
     * @param \BookStack\Auth\User $user
     */
    const AUTH_REGISTER = 'auth_register';

    /**
     * Commonmark environment configure.
     * Provides the commonmark library environment for customization
     * before it's used to render markdown content.
     * If the listener returns a non-null value, that will be used as an environment instead.
     *
     * @param \League\CommonMark\ConfigurableEnvironmentInterface $environment
     * @returns \League\CommonMark\ConfigurableEnvironmentInterface|null
     */
    const COMMONMARK_ENVIRONMENT_CONFIGURE = 'commonmark_environment_configure';

    /**
     * Web before middleware action.
     * Runs before the request is handled but after all other middleware apart from those
     * that depend on the current session user (Localization for example).
     * Provides the original request to use.
     * Return values, if provided, will be used as a new response to use.
     *
     * @param \Illuminate\Http\Request $request
     * @returns \Illuminate\Http\Response|null
     */
    const WEB_MIDDLEWARE_BEFORE = 'web_middleware_before';

    /**
     * Web after middleware action.
     * Runs after the request is handled but before the response is sent.
     * Provides both the original request and the currently resolved response.
     * Return values, if provided, will be used as a new response to use.
     *
     * @param \Illuminate\Http\Request                                                       $request
     * @param \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse $response
     * @returns \Illuminate\Http\Response|null
     */
    const WEB_MIDDLEWARE_AFTER = 'web_middleware_after';

    /**
     * Webhook call before event.
     * Runs before a webhook endpoint is called. Allows for customization
     * of the data format & content within the webhook POST request.
     * Provides the original event name as a string (see \BookStack\Actions\ActivityType)
     * along with the webhook instance along with the event detail which may be a
     * "Loggable" model type or a string.
     * If the listener returns a non-null value, that will be used as the POST data instead
     * of the system default.
     *
     * @param string                                $event
     * @param \BookStack\Actions\Webhook            $webhook
     * @param string|\BookStack\Interfaces\Loggable $detail
     * @param \BookStack\Auth\User                  $initiator
     * @param int                                   $initiatedTime
     */
    const WEBHOOK_CALL_BEFORE = 'webhook_call_before';
}
