<?php

namespace Tests\Unit;

use BookStack\Auth\Access\OpenIdConnect\InvalidTokenException;
use BookStack\Auth\Access\OpenIdConnect\OpenIdConnectIdToken;
use phpseclib3\Crypt\RSA;
use Tests\TestCase;

class OpenIdConnectIdTokenTest extends TestCase
{
    public function test_valid_token_passes_validation()
    {
        $token = new OpenIdConnectIdToken($this->idToken(), 'https://auth.example.com', [
            $this->jwkKeyArray()
        ]);

        $this->assertTrue($token->validate('xxyyzz.aaa.bbccdd.123'));
    }

    public function test_get_claim_returns_value_if_existing()
    {
        $token = new OpenIdConnectIdToken($this->idToken(), 'https://auth.example.com', []);
        $this->assertEquals('bscott@example.com', $token->getClaim('email'));
    }

    public function test_get_claim_returns_null_if_not_existing()
    {
        $token = new OpenIdConnectIdToken($this->idToken(), 'https://auth.example.com', []);
        $this->assertEquals(null, $token->getClaim('emails'));
    }

    public function test_get_all_claims_returns_all_payload_claims()
    {
        $defaultPayload = $this->getDefaultPayload();
        $token = new OpenIdConnectIdToken($this->idToken($defaultPayload), 'https://auth.example.com', []);
        $this->assertEquals($defaultPayload, $token->getAllClaims());
    }

    public function test_token_structure_error_cases()
    {
        $idToken = $this->idToken();
        $idTokenExploded = explode('.', $idToken);

        $messagesAndTokenValues = [
            ['Could not parse out a valid header within the provided token', ''],
            ['Could not parse out a valid header within the provided token', 'cat'],
            ['Could not parse out a valid payload within the provided token', $idTokenExploded[0]],
            ['Could not parse out a valid payload within the provided token', $idTokenExploded[0] . '.' . 'dog'],
            ['Could not parse out a valid signature within the provided token', $idTokenExploded[0] . '.' . $idTokenExploded[1]],
            ['Could not parse out a valid signature within the provided token', $idTokenExploded[0] . '.' . $idTokenExploded[1] . '.' . '@$%'],
        ];

        foreach ($messagesAndTokenValues as [$message, $tokenValue]) {
            $token = new OpenIdConnectIdToken($tokenValue, 'https://auth.example.com', []);
            $err = null;
            try {
                $token->validate('abc');
            } catch (\Exception $exception) {
                $err = $exception;
            }

            $this->assertInstanceOf(InvalidTokenException::class, $err, $message);
            $this->assertEquals($message, $err->getMessage());
        }
    }

    public function test_error_thrown_if_token_signature_not_validated_from_no_keys()
    {
        $token = new OpenIdConnectIdToken($this->idToken(), 'https://auth.example.com', []);
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('Token signature could not be validated using the provided keys');
        $token->validate('abc');
    }

    public function test_error_thrown_if_token_signature_not_validated_from_non_matching_key()
    {
        $token = new OpenIdConnectIdToken($this->idToken(), 'https://auth.example.com', [
            array_merge($this->jwkKeyArray(), [
                'n' => 'iqK-1QkICMf_cusNLpeNnN-bhT0-9WLBvzgwKLALRbrevhdi5ttrLHIQshaSL0DklzfyG2HWRmAnJ9Q7sweEjuRiiqRcSUZbYu8cIv2hLWYu7K_NH67D2WUjl0EnoHEuiVLsZhQe1CmdyLdx087j5nWkd64K49kXRSdxFQUlj8W3NeK3CjMEUdRQ3H4RZzJ4b7uuMiFA29S2ZhMNG20NPbkUVsFL-jiwTd10KSsPT8yBYipI9O7mWsUWt_8KZs1y_vpM_k3SyYihnWpssdzDm1uOZ8U3mzFr1xsLAO718GNUSXk6npSDzLl59HEqa6zs4O9awO2qnSHvcmyELNk31w'
            ])
        ]);
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('Token signature could not be validated using the provided keys');
        $token->validate('abc');
    }

    public function test_error_thrown_if_token_signature_not_validated_from_invalid_key()
    {
        $token = new OpenIdConnectIdToken($this->idToken(), 'https://auth.example.com', ['url://example.com']);
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('Token signature could not be validated using the provided keys');
        $token->validate('abc');
    }

    public function test_error_thrown_if_token_algorithm_is_not_rs256()
    {
        $token = new OpenIdConnectIdToken($this->idToken([], ['alg' => 'HS256']), 'https://auth.example.com', []);
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage("Only RS256 signature validation is supported. Token reports using HS256");
        $token->validate('abc');
    }

    public function test_token_claim_error_cases()
    {
        /** @var array<array{0: string: 1: array}> $claimOverridesByErrorMessage */
        $claimOverridesByErrorMessage = [
            // 1. iss claim present
            ['Missing or non-matching token issuer value', ['iss' => null]],
            // 1. iss claim matches provided issuer
            ['Missing or non-matching token issuer value', ['iss' => 'https://auth.example.co.uk']],
            // 2. aud claim present
            ['Missing token audience value', ['aud' => null]],
            // 2. aud claim validates all values against those expected (Only expect single)
            ['Token audience value has 2 values, Expected 1', ['aud' => ['abc', 'def']]],
            // 2. aud claim matches client id
            ['Token audience value did not match the expected client_id', ['aud' => 'xxyyzz.aaa.bbccdd.456']],
            // 4. azp claim matches client id if present
            ['Token authorized party exists but does not match the expected client_id', ['azp' => 'xxyyzz.aaa.bbccdd.456']],
            // 5. exp claim present
            ['Missing token expiration time value', ['exp' => null]],
            // 5. exp claim not expired
            ['Token has expired', ['exp' => time() - 360]],
            // 6. iat claim present
            ['Missing token issued at time value', ['iat' => null]],
            // 6. iat claim too far in the future
            ['Token issue at time is not recent or is invalid', ['iat' => time() + 600]],
            // 6. iat claim too far in the past
            ['Token issue at time is not recent or is invalid', ['iat' => time() - 172800]],

            // Custom: sub is present
            ['Missing token subject value', ['sub' => null]],
        ];

        foreach ($claimOverridesByErrorMessage as [$message, $overrides]) {
            $token = new OpenIdConnectIdToken($this->idToken($overrides), 'https://auth.example.com', [
                $this->jwkKeyArray()
            ]);

            $err = null;
            try {
                $token->validate('xxyyzz.aaa.bbccdd.123');
            } catch (\Exception $exception) {
                $err = $exception;
            }

            $this->assertInstanceOf(InvalidTokenException::class, $err, $message);
            $this->assertEquals($message, $err->getMessage());
        }
    }

    public function test_keys_can_be_a_local_file_reference_to_pem_key()
    {
        $file = tmpfile();
        $testFilePath = 'file://' . stream_get_meta_data($file)['uri'];
        file_put_contents($testFilePath, $this->pemKey());
        $token = new OpenIdConnectIdToken($this->idToken(), 'https://auth.example.com', [
            $testFilePath
        ]);

        $this->assertTrue($token->validate('xxyyzz.aaa.bbccdd.123'));
        unlink($testFilePath);
    }

    protected function getDefaultPayload(): array
    {
        return [
            "sub" => "abc1234def",
            "name" => "Barry Scott",
            "email" => "bscott@example.com",
            "ver" => 1,
            "iss" => "https://auth.example.com",
            "aud" => "xxyyzz.aaa.bbccdd.123",
            "iat" => time(),
            "exp" => time() + 720,
            "jti" => "ID.AaaBBBbbCCCcccDDddddddEEEeeeeee",
            "amr" => ["pwd"],
            "idp" => "fghfghgfh546456dfgdfg",
            "preferred_username" => "xXBazzaXx",
            "auth_time" => time(),
            "at_hash" => "sT4jbsdSGy9w12pq3iNYDA",
        ];
    }

    protected function idToken($payloadOverrides = [], $headerOverrides = []): string
    {
        $payload = array_merge($this->getDefaultPayload(), $payloadOverrides);
        $header = array_merge([
            'kid' => 'xyz456',
            'alg' => 'RS256',
        ], $headerOverrides);

        $top = implode('.', [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload)),
        ]);

        $privateKey = $this->getPrivateKey();
        $signature = $privateKey->sign($top);
        return $top . '.' . $this->base64UrlEncode($signature);
    }

    protected function getPrivateKey()
    {
        static $key;
        if (is_null($key)) {
            $key = RSA::loadPrivateKey($this->privatePemKey())->withPadding(RSA::SIGNATURE_PKCS1);
        }

        return $key;
    }

    protected function base64UrlEncode(string $decoded): string
    {
        return strtr(base64_encode($decoded), '+/', '-_');
    }

    protected function pemKey(): string
    {
        return "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqo1OmfNKec5S2zQC4SP9
DrHuUR0VgCi6oqcGERz7zqO36hqk3A3R3aCgJkEjfnbnMuszRRKs45NbXoOp9pvm
zXL16c93Obn7G8x8A3ao6yN5qKO5S5+CETqOZfKN/g75Xlz7VsC3igOhgsXnPx6i
iM6sbYbk0U/XpFaT84LXKI8VTIPUo7gTeZN1pTET//i9FlzAOzX+xfWBKdOqlEzl
+zihMHCZUUvQu99P+o0MDR0lMUT+vPJ6SJeRfnoHexwt6bZFiNnsZIEL03bX4QNk
WvsLta1+jNUee+8IPVhzCO8bvM86NzLaKUJ4k6NZ5IVrmdCFpFsjCWByOrDG8wdw
3wIDAQAB
-----END PUBLIC KEY-----";
    }

    protected function privatePemKey(): string
    {
        return "-----BEGIN PRIVATE KEY-----
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
-----END PRIVATE KEY-----";
    }

    protected function jwkKeyArray(): array
    {
        return [
            'kty' => 'RSA',
            'alg' => 'RS256',
            'kid' => '066e52af-8884-4926-801d-032a276f9f2a',
            'use' => 'sig',
            'e' => 'AQAB',
            'n' => 'qo1OmfNKec5S2zQC4SP9DrHuUR0VgCi6oqcGERz7zqO36hqk3A3R3aCgJkEjfnbnMuszRRKs45NbXoOp9pvmzXL16c93Obn7G8x8A3ao6yN5qKO5S5-CETqOZfKN_g75Xlz7VsC3igOhgsXnPx6iiM6sbYbk0U_XpFaT84LXKI8VTIPUo7gTeZN1pTET__i9FlzAOzX-xfWBKdOqlEzl-zihMHCZUUvQu99P-o0MDR0lMUT-vPJ6SJeRfnoHexwt6bZFiNnsZIEL03bX4QNkWvsLta1-jNUee-8IPVhzCO8bvM86NzLaKUJ4k6NZ5IVrmdCFpFsjCWByOrDG8wdw3w',
        ];
    }
}