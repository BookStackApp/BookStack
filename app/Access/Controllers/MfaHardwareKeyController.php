<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\LoginService;
use BookStack\Access\Mfa\BackupCodeService;
use BookStack\Access\Mfa\MfaSession;
use BookStack\Access\Mfa\MfaValue;
use BookStack\Access\Mfa\MfaVerificationLimiter;
use BookStack\Activity\ActivityType;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controller;
use Cose\Algorithms;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialOptions;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

class MfaHardwareKeyController extends Controller
{
    use HandlesPartialLogins;

    protected const SETUP_SECRET_SESSION_KEY = 'mfa-setup-hardware-key-secret';

    public function __construct(
        protected MfaVerificationLimiter $limiter,
    ) {
    }

    /**
     * Show a view that allows configuration of a hardware key.
     */
    public function generate(BackupCodeService $codeService)
    {

        $domain = parse_url(url('/'), PHP_URL_HOST);
        $rpEntity = PublicKeyCredentialRpEntity::create(
            setting('app-name'),
            $domain,
        );

        $user = $this->currentOrLastAttemptedUser();
        // TODO - Ensure user ID is somewhat long and random
        // See: https://webauthn-doc.spomky-labs.com/prerequisites/user-entity
        // Maybe hash of (app-id from settings + user id)
        // Or maybe a UUID/random_string which we connect via DB entry?
        $userEntity = PublicKeyCredentialUserEntity::create(
            $user->slug,
            $user->id,
            $user->name,
        );

        $publicKeyCredentialParametersList = [
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_ES256K), // More interesting algorithm
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_ES256),  //      ||
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_RS256),  //      ||
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_PS256),  //      \/
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_ED256),  // Less interesting algorithm
        ];

        $credentialOptions = PublicKeyCredentialCreationOptions::create(
            $rpEntity,
            $userEntity,
            random_bytes(32),
            $publicKeyCredentialParametersList,
            hints: [
                PublicKeyCredentialOptions::HINT_SECURITY_KEY,
            ],
            timeout: 60000, // 1 minute
            authenticatorSelection: AuthenticatorSelectionCriteria::create(
                userVerification: AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_DISCOURAGED,
            )
        );

        $attestationStatementSupportManager = AttestationStatementSupportManager::create();
        $attestationStatementSupportManager->add(NoneAttestationStatementSupport::create());
        $factory = new WebauthnSerializerFactory($attestationStatementSupportManager);
        $serializer = $factory->create();
        $jsonOptions = $serializer->serialize(
            $credentialOptions,
            'json',
            [
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                JsonEncode::OPTIONS => JSON_THROW_ON_ERROR,
            ]
        );

        // TODO - Change
        $this->setPageTitle(trans('auth.mfa_gen_hardware_key_title'));

        return view('mfa.hardware-key-generate', [
            'options' => $jsonOptions,
        ]);
    }

    /**
     * Confirm the setup of a hardware key, storing the key value against the user.
     *
     * @throws Exception
     */
    public function confirm()
    {


        return redirect('/mfa/setup');
    }

    /**
     * Verify the MFA method submission on check.
     *
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function verify(Request $request, MfaSession $mfaSession, LoginService $loginService)
    {
        $user = $this->currentOrLastAttemptedUser();
        $this->limiter->incrementAttempts($user, $request);
        if ($this->limiter->hasHitLimit($user, $request)) {
            $this->clearLastAttemptedUser();
            $this->limiter->throwException();
        }
    }
}
