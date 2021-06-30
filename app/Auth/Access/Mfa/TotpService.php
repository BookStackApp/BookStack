<?php

namespace BookStack\Auth\Access\Mfa;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class TotpService
{
    protected $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    /**
     * Generate a new totp secret key.
     */
    public function generateSecret(): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Generate a TOTP URL from secret key.
     */
    public function generateUrl(string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            setting('app-name'),
            user()->email,
            $secret
        );
    }

    /**
     * Generate a QR code to display a TOTP URL.
     */
    public function generateQrCodeSvg(string $url): string
    {
        $color = Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(32, 110, 167));
        return (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, $color),
                new SvgImageBackEnd
            )
        ))->writeString($url);
    }

    /**
     * Verify that the user provided code is valid for the secret.
     * The secret must be known, not user-provided.
     */
    public function verifyCode(string $code, string $secret): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->google2fa->verifyKey($secret, $code);
    }
}