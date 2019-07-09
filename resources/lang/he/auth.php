<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'פרטי ההתחברות אינם תואמים את הנתונים שלנו',
    'throttle' => 'נסיונות התחברות רבים מדי, יש להמתין :seconds שניות ולנסות שנית',

    // Login & Register
    'sign_up' => 'הרשמה',
    'log_in' => 'התחבר',
    'log_in_with' => 'התחבר באמצעות :socialDriver',
    'sign_up_with' => 'הרשם באמצעות :socialDriver',
    'logout' => 'התנתק',

    'name' => 'שם',
    'username' => 'שם משתמש',
    'email' => 'אי-מייל',
    'password' => 'סיסמא',
    'password_confirm' => 'אימות סיסמא',
    'password_hint' => 'חייבת להיות יותר מ-5 תווים',
    'forgot_password' => 'שכחת סיסמא?',
    'remember_me' => 'זכור אותי',
    'ldap_email_hint' => 'אנא ציין כתובת אי-מייל לשימוש בחשבון זה',
    'create_account' => 'צור חשבון',
    'already_have_account' => 'יש לך כבר חשבון?',
    'dont_have_account' => 'אין לך חשבון?',
    'social_login' => 'התחברות באמצעות אתר חברתי',
    'social_registration' => 'הרשמה באמצעות אתר חברתי',
    'social_registration_text' => 'הרשם והתחבר באמצעות שירות אחר',

    'register_thanks' => 'תודה על הרשמתך!',
    'register_confirm' => 'יש לבדוק את תיבת המייל שלך ולאשר את ההרשמה על מנת להשתמש ב:appName',
    'registrations_disabled' => 'הרשמה כרגע מבוטלת',
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
    'email_not_confirmed_resend_button' => 'שלח שוב מייל אימות',
];