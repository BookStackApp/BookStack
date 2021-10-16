<?php

namespace Tests\Unit;

use BookStack\Auth\Access\Oidc\OidcIdToken;
use BookStack\Auth\Access\Oidc\OidcInvalidTokenException;
use Tests\Helpers\OidcJwtHelper;
use Tests\TestCase;

class OidcIdTokenTest extends TestCase
{
    public function test_valid_token_passes_validation()
    {
        $token = new OidcIdToken(OidcJwtHelper::idToken(), OidcJwtHelper::defaultIssuer(), [
            OidcJwtHelper::publicJwkKeyArray(),
        ]);

        $this->assertTrue($token->validate('xxyyzz.aaa.bbccdd.123'));
    }

    public function test_get_claim_returns_value_if_existing()
    {
        $token = new OidcIdToken(OidcJwtHelper::idToken(), OidcJwtHelper::defaultIssuer(), []);
        $this->assertEquals('bscott@example.com', $token->getClaim('email'));
    }

    public function test_get_claim_returns_null_if_not_existing()
    {
        $token = new OidcIdToken(OidcJwtHelper::idToken(), OidcJwtHelper::defaultIssuer(), []);
        $this->assertEquals(null, $token->getClaim('emails'));
    }

    public function test_get_all_claims_returns_all_payload_claims()
    {
        $defaultPayload = OidcJwtHelper::defaultPayload();
        $token = new OidcIdToken(OidcJwtHelper::idToken($defaultPayload), OidcJwtHelper::defaultIssuer(), []);
        $this->assertEquals($defaultPayload, $token->getAllClaims());
    }

    public function test_token_structure_error_cases()
    {
        $idToken = OidcJwtHelper::idToken();
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
            $token = new OidcIdToken($tokenValue, OidcJwtHelper::defaultIssuer(), []);
            $err = null;

            try {
                $token->validate('abc');
            } catch (\Exception $exception) {
                $err = $exception;
            }

            $this->assertInstanceOf(OidcInvalidTokenException::class, $err, $message);
            $this->assertEquals($message, $err->getMessage());
        }
    }

    public function test_error_thrown_if_token_signature_not_validated_from_no_keys()
    {
        $token = new OidcIdToken(OidcJwtHelper::idToken(), OidcJwtHelper::defaultIssuer(), []);
        $this->expectException(OidcInvalidTokenException::class);
        $this->expectExceptionMessage('Token signature could not be validated using the provided keys');
        $token->validate('abc');
    }

    public function test_error_thrown_if_token_signature_not_validated_from_non_matching_key()
    {
        $token = new OidcIdToken(OidcJwtHelper::idToken(), OidcJwtHelper::defaultIssuer(), [
            array_merge(OidcJwtHelper::publicJwkKeyArray(), [
                'n' => 'iqK-1QkICMf_cusNLpeNnN-bhT0-9WLBvzgwKLALRbrevhdi5ttrLHIQshaSL0DklzfyG2HWRmAnJ9Q7sweEjuRiiqRcSUZbYu8cIv2hLWYu7K_NH67D2WUjl0EnoHEuiVLsZhQe1CmdyLdx087j5nWkd64K49kXRSdxFQUlj8W3NeK3CjMEUdRQ3H4RZzJ4b7uuMiFA29S2ZhMNG20NPbkUVsFL-jiwTd10KSsPT8yBYipI9O7mWsUWt_8KZs1y_vpM_k3SyYihnWpssdzDm1uOZ8U3mzFr1xsLAO718GNUSXk6npSDzLl59HEqa6zs4O9awO2qnSHvcmyELNk31w',
            ]),
        ]);
        $this->expectException(OidcInvalidTokenException::class);
        $this->expectExceptionMessage('Token signature could not be validated using the provided keys');
        $token->validate('abc');
    }

    public function test_error_thrown_if_invalid_key_provided()
    {
        $token = new OidcIdToken(OidcJwtHelper::idToken(), OidcJwtHelper::defaultIssuer(), ['url://example.com']);
        $this->expectException(OidcInvalidTokenException::class);
        $this->expectExceptionMessage('Unexpected type of key value provided');
        $token->validate('abc');
    }

    public function test_error_thrown_if_token_algorithm_is_not_rs256()
    {
        $token = new OidcIdToken(OidcJwtHelper::idToken([], ['alg' => 'HS256']), OidcJwtHelper::defaultIssuer(), []);
        $this->expectException(OidcInvalidTokenException::class);
        $this->expectExceptionMessage('Only RS256 signature validation is supported. Token reports using HS256');
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
            $token = new OidcIdToken(OidcJwtHelper::idToken($overrides), OidcJwtHelper::defaultIssuer(), [
                OidcJwtHelper::publicJwkKeyArray(),
            ]);

            $err = null;

            try {
                $token->validate('xxyyzz.aaa.bbccdd.123');
            } catch (\Exception $exception) {
                $err = $exception;
            }

            $this->assertInstanceOf(OidcInvalidTokenException::class, $err, $message);
            $this->assertEquals($message, $err->getMessage());
        }
    }

    public function test_keys_can_be_a_local_file_reference_to_pem_key()
    {
        $file = tmpfile();
        $testFilePath = 'file://' . stream_get_meta_data($file)['uri'];
        file_put_contents($testFilePath, OidcJwtHelper::publicPemKey());
        $token = new OidcIdToken(OidcJwtHelper::idToken(), OidcJwtHelper::defaultIssuer(), [
            $testFilePath,
        ]);

        $this->assertTrue($token->validate('xxyyzz.aaa.bbccdd.123'));
        unlink($testFilePath);
    }
}
