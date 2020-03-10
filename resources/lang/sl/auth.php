<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Poverilnice se ne ujemajo s podatki v naši bazi.',
    'throttle' => 'Prekoračili ste število možnih prijav. Poskusite znova čez :seconds sekund.',

    // Login & Register
    'sign_up' => 'Registracija',
    'log_in' => 'Prijavi se',
    'log_in_with' => 'Prijavi se z :socialDriver',
    'sign_up_with' => 'Registriraj se z :socialDriver',
    'logout' => 'Odjavi se',

    'name' => 'Ime',
    'username' => 'Uporabniško ime',
    'email' => 'E-pošta',
    'password' => 'Geslo',
    'password_confirm' => 'Potrdi geslo',
    'password_hint' => 'Mora vebovati vsaj 8 znakov',
    'forgot_password' => 'Pozabljeno geslo?',
    'remember_me' => 'Zapomni si me',
    'ldap_email_hint' => 'Prosimo vpišite e-poštni naslov za ta račun.',
    'create_account' => 'Ustvari račun',
    'already_have_account' => 'Že imate račun?',
    'dont_have_account' => 'Nimate računa?',
    'social_login' => 'Prijava z računi družbenih omrežij',
    'social_registration' => 'Registracija z družbenim omrežjem',
    'social_registration_text' => 'Registrirajte in prijavite se za uporabo drugih možnosti.',

    'register_thanks' => 'Hvala za registracijo!',
    'register_confirm' => 'Please check your email and click the confirmation button to access :appName.',
    'registrations_disabled' => 'Registrations are currently disabled',
    'registration_email_domain_invalid' => 'That email domain does not have access to this application',
    'register_success' => 'Thanks for signing up! You are now registered and signed in.',


    // Password Reset
    'reset_password' => 'Reset Password',
    'reset_password_send_instructions' => 'Enter your email below and you will be sent an email with a password reset link.',
    'reset_password_send_button' => 'Send Reset Link',
    'reset_password_sent_success' => 'A password reset link has been sent to :email.',
    'reset_password_success' => 'Your password has been successfully reset.',
    'email_reset_subject' => 'Reset your :appName password',
    'email_reset_text' => 'You are receiving this email because we received a password reset request for your account.',
    'email_reset_not_requested' => 'If you did not request a password reset, no further action is required.',


    // Email Confirmation
    'email_confirm_subject' => 'Confirm your email on :appName',
    'email_confirm_greeting' => 'Thanks for joining :appName!',
    'email_confirm_text' => 'Please confirm your email address by clicking the button below:',
    'email_confirm_action' => 'Confirm Email',
    'email_confirm_send_error' => 'Email confirmation required but the system could not send the email. Contact the admin to ensure email is set up correctly.',
    'email_confirm_success' => 'Your email has been confirmed!',
    'email_confirm_resent' => 'Confirmation email resent, Please check your inbox.',

    'email_not_confirmed' => 'Email Address Not Confirmed',
    'email_not_confirmed_text' => 'Your email address has not yet been confirmed.',
    'email_not_confirmed_click_link' => 'Please click the link in the email that was sent shortly after you registered.',
    'email_not_confirmed_resend' => 'If you cannot find the email you can re-send the confirmation email by submitting the form below.',
    'email_not_confirmed_resend_button' => 'Resend Confirmation Email',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success' => 'Password set, you now have access to :appName!'
];