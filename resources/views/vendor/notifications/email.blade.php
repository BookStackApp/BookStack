<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{{ config('app.lang') }}">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <style type="text/css" rel="stylesheet" media="all">
        /* Media Queries */
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 600px) {
            .button {
                width: 100% !important;
            }
            .mobile {
                max-width: 100%;
                display: block;
                width: 100%;
            }
        }
    </style>
</head>

<?php

$style = [
    /* Layout ------------------------------ */

    'body' => 'margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;',
    'email-wrapper' => 'width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;',

    /* Masthead ----------------------- */

    'email-masthead' => 'padding: 18px 0; text-align: left;',
    'email-masthead_name' => 'font-size: 24px; font-weight: 400; color: #FFFFFF; text-decoration: none;',

    'email-body' => 'width: 100%; margin: 0; padding: 0; border-bottom: 1px solid #EDEFF2; background-color: #FFF;',
    'email-body_inner' => 'width: auto; max-width: 100%; margin: 0 auto; padding: 0;',
    'email-body_cell' => 'padding: 35px;',

    'email-footer' => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;',
    'email-footer_cell' => 'color: #AEAEAE; padding: 35px; text-align: center;',

    /* Body ------------------------------ */

    'body_action' => 'width: 100%; margin: 30px auto; padding: 0; text-align: center;',
    'body_sub' => 'margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;',

    /* Type ------------------------------ */

    'anchor' => 'color: '.setting('app-color').';overflow-wrap: break-word;word-wrap: break-word;word-break: break-all;word-break:break-word;',
    'header-1' => 'margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;',
    'paragraph' => 'margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;',
    'paragraph-sub' => 'margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;',
    'paragraph-center' => 'text-align: center;',

    /* Buttons ------------------------------ */

    'button' => 'display: block; display: inline-block; width: 200px; min-height: 20px; padding: 10px;
                 background-color: #3869D4; border-radius: 3px; color: #ffffff; font-size: 15px; line-height: 25px;
                 text-align: center; text-decoration: none; -webkit-text-size-adjust: none;',

    'button--green' => 'background-color: #22BC66;',
    'button--red' => 'background-color: #dc4d2f;',
    'button--blue' => 'background-color: '.setting('app-color').';',
];
?>

<?php $fontFamily = 'font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;'; ?>

<body style="{{ $style['body'] }}">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" class="mobile">
                <table width="600" style="max-width: 100%; padding: 12px;text-align: left;" cellpadding="0" cellspacing="0" class="mobile">
                    <tr>
                        <td style="{{ $style['email-wrapper'] }}" align="center">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <!-- Logo -->
                                <tr>
                                    <td bgcolor="{{ setting('app-color') }}">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="35">&nbsp;</td>
                                                <td>
                                                    <table width="100%" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            @if(setting('app-logo', '') !== 'none')
                                                                <td valign="middle" style="padding-right: 12px; padding-top: 4px; padding-bottom: 4px;">
                                                                    <a style="border: none; vertical-align: middle; display: inline-block;" href="{{ url('/') }}" target="_blank">
                                                                        <img class="logo-image" height="43"
                                                                             src="{{ setting('app-logo', '') === '' ? url('/logo.png') : url(setting('app-logo', '')) }}"
                                                                             alt="Logo">
                                                                    </a>
                                                                </td>
                                                            @endif
                                                            <td width="90%" valign="middle" style="{{ $style['email-masthead'] }}">
                                                                <a style="{{ $fontFamily }} {{ $style['email-masthead_name'] }}" href="{{ url('/') }}" target="_blank">
                                                                    {{ setting('app-name') }}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="35">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Email Body -->
                                <tr>
                                    <td style="{{ $style['email-body'] }}" width="100%">
                                        <table style="{{ $style['email-body_inner'] }}" align="center" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="{{ $fontFamily }} {{ $style['email-body_cell'] }}">

                                                    <!-- Greeting -->
                                                    @if (!empty($greeting) || $level == 'error')
                                                    <h1 style="{{ $style['header-1'] }}">
                                                        @if (! empty($greeting))
                                                            {{ $greeting }}
                                                        @else
                                                            @if ($level == 'error')
                                                                Whoops!
                                                            @endif
                                                        @endif
                                                    </h1>
                                                    @endif

                                                    <!-- Intro -->
                                                    @foreach ($introLines as $line)
                                                        <p style="{{ $style['paragraph'] }}">
                                                            {{ $line }}
                                                        </p>
                                                    @endforeach

                                                    <!-- Action Button -->
                                                    @if (isset($actionText))
                                                        <table style="{{ $style['body_action'] }}" align="center" width="100%" cellpadding="0" cellspacing="0">
                                                            <tr>
                                                                <td align="center">
                                                                    <?php
                                                                    switch ($level) {
                                                                        case 'success':
                                                                            $actionColor = 'button--green';
                                                                            break;
                                                                        case 'error':
                                                                            $actionColor = 'button--red';
                                                                            break;
                                                                        default:
                                                                            $actionColor = 'button--blue';
                                                                    }
                                                                    ?>

                                                                    <a href="{{ $actionUrl }}"
                                                                       style="{{ $fontFamily }} {{ $style['button'] }} {{ $style[$actionColor] }}"
                                                                       class="button"
                                                                       target="_blank">
                                                                        {{ $actionText }}
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    @endif

                                                    <!-- Outro -->
                                                    @foreach ($outroLines as $line)
                                                        <p style="{{ $style['paragraph'] }}">
                                                            {{ $line }}
                                                        </p>
                                                    @endforeach


                                                    <!-- Sub Copy -->
                                                    @if (isset($actionText))
                                                        <table style="{{ $style['body_sub'] }}">
                                                            <tr>
                                                                <td style="{{ $fontFamily }}">
                                                                    <p style="{{ $style['paragraph-sub'] }}">
                                                                        {{ trans('common.email_action_help', ['actionText' => $actionText]) }}
                                                                    </p>

                                                                    <p style="{{ $style['paragraph-sub'] }}">
                                                                        <a style="{{ $style['anchor'] }}" href="{{ $actionUrl }}" target="_blank">
                                                                            {{ $actionUrl }}
                                                                        </a>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    @endif

                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Footer -->
                                <tr>
                                    <td>
                                        <table style="{{ $style['email-footer'] }}" align="center" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="{{ $fontFamily }} {{ $style['email-footer_cell'] }}">
                                                    <p style="{{ $style['paragraph-sub'] }}">
                                                        &copy; {{ date('Y') }}
                                                        <a style="{{ $style['anchor'] }}" href="{{ url('/') }}" target="_blank">{{ setting('app-name') }}</a>.
                                                        {{ trans('common.email_rights') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
