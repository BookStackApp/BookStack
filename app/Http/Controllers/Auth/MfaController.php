<?php

namespace BookStack\Http\Controllers\Auth;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use BookStack\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class MfaController extends Controller
{
    protected const TOTP_SETUP_SECRET_SESSION_KEY = 'mfa-setup-totp-secret';

    /**
     * Show the view to setup MFA for the current user.
     */
    public function setup()
    {
        // TODO - Redirect back to profile/edit if already setup?
        // Show MFA setup route
        return view('mfa.setup');
    }

    /**
     * Show a view that generates and displays a TOTP QR code.
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function totpGenerate()
    {
        // TODO - Ensure a QR code doesn't already exist? Or overwrite?
        $google2fa = new Google2FA();
        if (session()->has(static::TOTP_SETUP_SECRET_SESSION_KEY)) {
            $totpSecret = decrypt(session()->get(static::TOTP_SETUP_SECRET_SESSION_KEY));
        } else {
            $totpSecret = $google2fa->generateSecretKey();
            session()->put(static::TOTP_SETUP_SECRET_SESSION_KEY, encrypt($totpSecret));
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            setting('app-name'),
            user()->email,
            $totpSecret
        );

        $color = Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(32, 110, 167));
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, $color),
                new SvgImageBackEnd
            )
        ))->writeString($qrCodeUrl);

        // Get user to verify setup via responding once.
        // If correct response, Save key against user
        return view('mfa.totp-generate', [
            'secret' => $totpSecret,
            'svg' => $svg,
        ]);
    }

    /**
     * Confirm the setup of TOTP and save the auth method secret
     * against the current user.
     * @throws ValidationException
     */
    public function totpConfirm(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|max:12|min:4'
        ]);

        // TODO - Confirm code
        dd($request->input('code'));
    }
}
