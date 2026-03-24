<?php

$issuer = envValue('OIDC_ISSUER', 'http://fake-oidc:9000');
$publicBase = envValue('OIDC_PUBLIC_BASE', 'http://localhost:9091');
$clientId = envValue('OIDC_CLIENT_ID', 'fake-bookstack-client');
$email = envValue('FAKE_OIDC_EMAIL', 'fake.user@example.com');
$name = envValue('FAKE_OIDC_NAME', 'Fake OIDC User');
$subject = envValue('FAKE_OIDC_SUB', 'fake-oidc-user-001');
$username = envValue('FAKE_OIDC_USERNAME', 'fake.user');
$groups = array_values(array_filter(array_map('trim', explode(',', envValue('FAKE_OIDC_GROUPS', 'bookstack-users')))));

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($path) {
    case '/':
        respondHtml('<h1>Fake OIDC Provider</h1><p>Use <code>/authorize</code> through BookStack.</p>');
        break;

    case '/.well-known/openid-configuration':
        respondJson([
            'issuer' => $issuer,
            'authorization_endpoint' => "{$publicBase}/authorize",
            'token_endpoint' => "{$issuer}/token",
            'userinfo_endpoint' => "{$issuer}/userinfo",
            'jwks_uri' => "{$issuer}/keys",
            'end_session_endpoint' => "{$publicBase}/logout",
            'id_token_signing_alg_values_supported' => ['RS256'],
            'subject_types_supported' => ['public'],
            'response_types_supported' => ['code'],
        ]);
        break;

    case '/authorize':
        $redirectUri = $_GET['redirect_uri'] ?? null;
        $state = $_GET['state'] ?? '';

        if (!$redirectUri) {
            respondJson(['error' => 'missing_redirect_uri'], 400);
        }

        $joiner = str_contains($redirectUri, '?') ? '&' : '?';
        header('Location: ' . $redirectUri . $joiner . http_build_query([
            'code' => 'fake-auth-code',
            'state' => $state,
        ]));
        exit;

    case '/token':
        if ($method !== 'POST') {
            respondJson(['error' => 'method_not_allowed'], 405);
        }

        $code = $_POST['code'] ?? '';
        if ($code !== 'fake-auth-code') {
            respondJson(['error' => 'invalid_grant'], 400);
        }

        respondJson([
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'access_token' => 'fake-access-token',
            'id_token' => createIdToken([
                'iss' => $issuer,
                'aud' => $clientId,
                'sub' => $subject,
                'email' => $email,
                'name' => $name,
                'preferred_username' => $username,
                'groups' => $groups,
                'iat' => time(),
                'exp' => time() + 3600,
            ]),
        ]);
        break;

    case '/userinfo':
        respondJson([
            'sub' => $subject,
            'email' => $email,
            'name' => $name,
            'preferred_username' => $username,
            'groups' => $groups,
        ]);
        break;

    case '/keys':
        respondJson([
            'keys' => [[
                'kty' => 'RSA',
                'alg' => 'RS256',
                'kid' => '066e52af-8884-4926-801d-032a276f9f2a',
                'use' => 'sig',
                'e'   => 'AQAB',
                'n'   => 'qo1OmfNKec5S2zQC4SP9DrHuUR0VgCi6oqcGERz7zqO36hqk3A3R3aCgJkEjfnbnMuszRRKs45NbXoOp9pvmzXL16c93Obn7G8x8A3ao6yN5qKO5S5-CETqOZfKN_g75Xlz7VsC3igOhgsXnPx6iiM6sbYbk0U_XpFaT84LXKI8VTIPUo7gTeZN1pTET__i9FlzAOzX-xfWBKdOqlEzl-zihMHCZUUvQu99P-o0MDR0lMUT-vPJ6SJeRfnoHexwt6bZFiNnsZIEL03bX4QNkWvsLta1-jNUee-8IPVhzCO8bvM86NzLaKUJ4k6NZ5IVrmdCFpFsjCWByOrDG8wdw3w',
            ]],
        ]);
        break;

    case '/logout':
        $redirectUri = $_GET['post_logout_redirect_uri'] ?? 'http://localhost:8080';
        header('Location: ' . $redirectUri);
        exit;

    default:
        respondJson(['error' => 'not_found', 'path' => $path], 404);
}

function envValue(string $key, string $default): string
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    return is_string($value) && $value !== '' ? $value : $default;
}

function createIdToken(array $payload): string
{
    $header = [
        'typ' => 'JWT',
        'alg' => 'RS256',
        'kid' => '066e52af-8884-4926-801d-032a276f9f2a',
    ];

    $segments = [
        base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
        base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES)),
    ];

    $signingInput = implode('.', $segments);
    $privateKey = openssl_pkey_get_private(file_get_contents(__DIR__ . '/private.pem'));
    openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

    $segments[] = base64UrlEncode($signature);

    return implode('.', $segments);
}

function base64UrlEncode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

function respondJson(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

function respondHtml(string $html, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: text/html; charset=utf-8');
    echo $html;
    exit;
}
