<?php

namespace BookStack\Access\Oidc;

use League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;

/**
 * Option provider that sends credentials via HTTP Basic Auth header
 * and also includes client_id in the request body.
 */
class OidcHttpBasicWithClientIdOptionProvider extends HttpBasicAuthOptionProvider
{
    public function getAccessTokenOptions($method, array $params)
    {
        $clientId = $params['client_id'] ?? null;

        $options = parent::getAccessTokenOptions($method, $params);

        if ($clientId) {
            parse_str($options['body'] ?? '', $body);
            $body['client_id'] = $clientId;
            $options['body'] = http_build_query($body);
        }

        return $options;
    }
}
