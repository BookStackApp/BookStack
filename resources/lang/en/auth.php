<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    /**
     * Login & Register
     */
    'sign_up' => 'Sign up',
    'log_in' => 'Log in',
    'logout' => 'Logout',

    'name' => 'Name',
    'username' => 'Username',
    'email' => 'Email',
    'password' => 'Password',
    'password_confirm' => 'Confirm Password',
    'password_hint' => 'Must be over 5 characters',
    'forgot_password' => 'Forgot Password?',
    'remember_me' => 'Remember Me',
    'ldap_email_hint' => 'Please enter an email to use for this account.',
    'create_account' => 'Create Account',
    'social_login' => 'Social Login',
    'social_registration' => 'Social Registration',
    'social_registration_text' => 'Register and sign in using another service.',

    'register_thanks' => 'Thanks for registering!',
    'register_confirm' => 'Please check your email and click the confirmation button to access :appName.',

    /**
     * Password Reset
     */
    'reset_password' => 'Reset Password',
    'reset_password_send_instructions' => 'Enter your email below and you will be sent an email with a password reset link.',
    'reset_password_send_button' => 'Send Reset Link',

    'email_reset_subject' => 'Reset your :appName password',
    'email_reset_text' => 'You are receiving this email because we received a password reset request for your account.',
    'email_reset_not_requested' => 'If you did not request a password reset, no further action is required.',

    /**
     * Email Confirmation
     */
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
];