<?php

namespace Tests\Helpers;

use phpseclib3\Crypt\RSA;

/**
 * A collection of functions to help with OIDC JWT testing.
 * By default, unless overridden, content is provided in a correct working state.
 */
class OidcJwtHelper
{
    public static function defaultIssuer(): string
    {
        return 'https://auth.example.com';
    }

    public static function defaultClientId(): string
    {
        return 'xxyyzz.aaa.bbccdd.123';
    }

    public static function defaultPayload(): array
    {
        return [
            'sub'                => 'abc1234def',
            'name'               => 'Barry Scott',
            'email'              => 'bscott@example.com',
            'ver'                => 1,
            'iss'                => static::defaultIssuer(),
            'aud'                => static::defaultClientId(),
            'iat'                => time(),
            'exp'                => time() + 720,
            'jti'                => 'ID.AaaBBBbbCCCcccDDddddddEEEeeeeee',
            'amr'                => ['pwd'],
            'idp'                => 'fghfghgfh546456dfgdfg',
            'preferred_username' => 'xXBazzaXx',
            'auth_time'          => time(),
            'at_hash'            => 'sT4jbsdSGy9w12pq3iNYDA',
        ];
    }

    public static function idToken($payloadOverrides = [], $headerOverrides = []): string
    {
        $payload = array_merge(static::defaultPayload(), $payloadOverrides);
        $header = array_merge([
            'kid' => 'xyz456',
            'alg' => 'RS256',
        ], $headerOverrides);

        $top = implode('.', [
            static::base64UrlEncode(json_encode($header)),
            static::base64UrlEncode(json_encode($payload)),
        ]);

        $privateKey = static::privateKeyInstance();
        $signature = $privateKey->sign($top);

        return $top . '.' . static::base64UrlEncode($signature);
    }

    public static function privateKeyInstance()
    {
        static $key;
        if (is_null($key)) {
            $key = RSA::loadPrivateKey(static::privatePemKey())->withPadding(RSA::SIGNATURE_PKCS1);
        }

        return $key;
    }

    public static function base64UrlEncode(string $decoded): string
    {
        return strtr(base64_encode($decoded), '+/', '-_');
    }

    public static function publicPemKey(): string
    {
        return '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqo1OmfNKec5S2zQC4SP9
DrHuUR0VgCi6oqcGERz7zqO36hqk3A3R3aCgJkEjfnbnMuszRRKs45NbXoOp9pvm
zXL16c93Obn7G8x8A3ao6yN5qKO5S5+CETqOZfKN/g75Xlz7VsC3igOhgsXnPx6i
iM6sbYbk0U/XpFaT84LXKI8VTIPUo7gTeZN1pTET//i9FlzAOzX+xfWBKdOqlEzl
+zihMHCZUUvQu99P+o0MDR0lMUT+vPJ6SJeRfnoHexwt6bZFiNnsZIEL03bX4QNk
WvsLta1+jNUee+8IPVhzCO8bvM86NzLaKUJ4k6NZ5IVrmdCFpFsjCWByOrDG8wdw
3wIDAQAB
-----END PUBLIC KEY-----';
    }

    public static function privatePemKey(): string
    {
        return '-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCqjU6Z80p5zlLb
NALhI/0Ose5RHRWAKLqipwYRHPvOo7fqGqTcDdHdoKAmQSN+ducy6zNFEqzjk1te
g6n2m+bNcvXpz3c5ufsbzHwDdqjrI3moo7lLn4IROo5l8o3+DvleXPtWwLeKA6GC
xec/HqKIzqxthuTRT9ekVpPzgtcojxVMg9SjuBN5k3WlMRP/+L0WXMA7Nf7F9YEp
06qUTOX7OKEwcJlRS9C730/6jQwNHSUxRP688npIl5F+egd7HC3ptkWI2exkgQvT
dtfhA2Ra+wu1rX6M1R577wg9WHMI7xu8zzo3MtopQniTo1nkhWuZ0IWkWyMJYHI6
sMbzB3DfAgMBAAECggEADm7K2ghWoxwsstQh8j+DaLzx9/dIHIJV2PHdd5FGVeRQ
6gS7MswQmHrBUrtsb4VMZ2iz/AJqkw+jScpGldH3pCc4XELsSfxNHbseO4TNIqjr
4LOKOLYU4bRc3I+8KGXIAI5JzrucTJemEVUCDrte8cjbmqExt+zTyNpyxsapworF
v+vnSdv40d62f+cS1xvwB+ymLK/B/wZ/DemDCi8jsi7ou/M7l5xNCzjH4iMSLtOW
fgEhejIBG9miMJWPiVpTXE3tMdNuN3OsWc4XXm2t4VRovlZdu30Fax1xWB+Locsv
HlHKLOFc8g+jZh0TL2KCNjPffMcC7kHhW3afshpIsQKBgQDhyWUnkqd6FzbwIX70
SnaMgKoUv5W/K5T+Sv/PA2CyN8Gu8ih/OsoNZSnI0uqe3XQIvvgN/Fq3wO1ttLzf
z5B6ZC7REfTgcR0190gihk6f5rtcj7d6Fy/oG2CE8sDSXgPnpEaBjvJVgN5v/U2s
HpVaidmHTyGLCfEszoeoy8jyrQKBgQDBX8caGylmzQLc6XNntZChlt3e18Nj8MPA
DxWLcoqgdDoofLDQAmLl+vPKyDmhQjos5eas1jgmVVEM4ge+MysaVezvuLBsSnOh
ihc0i63USU6i7YDE83DrCewCthpFHi/wW1S5FoCAzpVy8y99vwcqO4kOXcmf4O6Y
uW6sMsjvOwKBgQDbFtqB+MtsLCSSBF61W6AHHD5tna4H75lG2623yXZF2NanFLF5
K6muL9DI3ujtOMQETJJUt9+rWJjLEEsJ/dYa/SV0l7D/LKOEnyuu3JZkkLaTzZzi
6qcA2bfhqdCzEKlHV99WjkfV8hNlpex9rLuOPB8JLh7FVONicBGxF/UojQKBgDXs
IlYaSuI6utilVKQP0kPtEPOKERc2VS+iRSy8hQGXR3xwwNFQSQm+f+sFCGT6VcSd
W0TI+6Fc2xwPj38vP465dTentbKM1E+wdSYW6SMwSfhO6ECDbfJsst5Sr2Kkt1N7
9FUkfDLu6GfEfnK/KR1SurZB2u51R7NYyg7EnplvAoGAT0aTtOcck0oYN30g5mdf
efqXPwg2wAPYeiec49EbfnteQQKAkqNfJ9K69yE2naf6bw3/5mCBsq/cXeuaBMII
ylysUIRBqt2J0kWm2yCpFWR7H+Ilhdx9A7ZLCqYVt8e+vjO/BOI3cQDe2VPOLPSl
q/1PY4iJviGKddtmfClH3v4=
-----END PRIVATE KEY-----';
    }

    public static function publicJwkKeyArray(): array
    {
        return [
            'kty' => 'RSA',
            'alg' => 'RS256',
            'kid' => '066e52af-8884-4926-801d-032a276f9f2a',
            'use' => 'sig',
            'e'   => 'AQAB',
            'n'   => 'qo1OmfNKec5S2zQC4SP9DrHuUR0VgCi6oqcGERz7zqO36hqk3A3R3aCgJkEjfnbnMuszRRKs45NbXoOp9pvmzXL16c93Obn7G8x8A3ao6yN5qKO5S5-CETqOZfKN_g75Xlz7VsC3igOhgsXnPx6iiM6sbYbk0U_XpFaT84LXKI8VTIPUo7gTeZN1pTET__i9FlzAOzX-xfWBKdOqlEzl-zihMHCZUUvQu99P-o0MDR0lMUT-vPJ6SJeRfnoHexwt6bZFiNnsZIEL03bX4QNkWvsLta1-jNUee-8IPVhzCO8bvM86NzLaKUJ4k6NZ5IVrmdCFpFsjCWByOrDG8wdw3w',
        ];
    }
}
