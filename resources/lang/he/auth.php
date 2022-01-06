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
    'password_hint' => 'Must be at least 8 characters',
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
    'registration_email_domain_invalid' => 'לא ניתן להרשם באמצעות המייל שסופק',
    'register_success' => 'תודה על הרשמתך! ניתן כעת להתחבר',

    // Password Reset
    'reset_password' => 'איפוס סיסמא',
    'reset_password_send_instructions' => 'יש להזין את כתובת המייל למטה ואנו נשלח אלייך הוראות לאיפוס הסיסמא',
    'reset_password_send_button' => 'שלח קישור לאיפוס סיסמא',
    'reset_password_sent' => 'A password reset link will be sent to :email if that email address is found in the system.',
    'reset_password_success' => 'סיסמתך עודכנה בהצלחה',
    'email_reset_subject' => 'איפוס סיסמא ב :appName',
    'email_reset_text' => 'קישור זה נשלח עקב בקשה לאיפוס סיסמא בחשבון שלך',
    'email_reset_not_requested' => 'אם לא ביקשת לאפס את סיסמתך, אפשר להתעלם ממייל זה',

    // Email Confirmation
    'email_confirm_subject' => 'אמת אי-מייל ב :appName',
    'email_confirm_greeting' => 'תודה שהצטרפת אל :appName!',
    'email_confirm_text' => 'יש לאמת את כתובת המייל של על ידי לחיצה על הכפור למטה:',
    'email_confirm_action' => 'אמת כתובת אי-מייל',
    'email_confirm_send_error' => 'נדרש אימות אי-מייל אך שליחת האי-מייל אליך נכשלה. יש ליצור קשר עם מנהל המערכת כדי לוודא שאכן ניתן לשלוח מיילים.',
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'אימות נשלח לאי-מייל שלך, יש לבדוק בתיבת הדואר הנכנס',

    'email_not_confirmed' => 'כתובת המייל לא אומתה',
    'email_not_confirmed_text' => 'כתובת המייל שלך טרם אומתה',
    'email_not_confirmed_click_link' => 'יש ללחוץ על הקישור אשר נשלח אליך לאחר ההרשמה',
    'email_not_confirmed_resend' => 'אם אינך מוצא את המייל, ניתן לשלוח בשנית את האימות על ידי לחיצה על הכפתור למטה',
    'email_not_confirmed_resend_button' => 'שלח שוב מייל אימות',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Reconfigure',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Setup',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Mobile App',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Codes',
    'mfa_option_backup_codes_desc' => 'Securely store a set of one-time-use backup codes which you can enter to verify your identity.',
    'mfa_gen_confirm_and_enable' => 'Confirm and Enable',
    'mfa_gen_backup_codes_title' => 'Backup Codes Setup',
    'mfa_gen_backup_codes_desc' => 'Store the below list of codes in a safe place. When accessing the system you\'ll be able to use one of the codes as a second authentication mechanism.',
    'mfa_gen_backup_codes_download' => 'Download Codes',
    'mfa_gen_backup_codes_usage_warning' => 'Each code can only be used once',
    'mfa_gen_totp_title' => 'Mobile App Setup',
    'mfa_gen_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan the QR code below using your preferred authentication app to get started.',
    'mfa_gen_totp_verify_setup' => 'Verify Setup',
    'mfa_gen_totp_verify_setup_desc' => 'Verify that all is working by entering a code, generated within your authentication app, in the input box below:',
    'mfa_gen_totp_provide_code_here' => 'Provide your app generated code here',
    'mfa_verify_access' => 'Verify Access',
    'mfa_verify_access_desc' => 'Your user account requires you to confirm your identity via an additional level of verification before you\'re granted access. Verify using one of your configured methods to continue.',
    'mfa_verify_no_methods' => 'No Methods Configured',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'Verify using a mobile app',
    'mfa_verify_use_backup_codes' => 'Verify using a backup code',
    'mfa_verify_backup_code' => 'Backup Code',
    'mfa_verify_backup_code_desc' => 'Enter one of your remaining backup codes below:',
    'mfa_verify_backup_code_enter_here' => 'Enter backup code here',
    'mfa_verify_totp_desc' => 'Enter the code, generated using your mobile app, below:',
    'mfa_setup_login_notification' => 'Multi-factor method configured, Please now login again using the configured method.',
];
