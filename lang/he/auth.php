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
    'password_hint' => '‏אורך הסיסמה חייב להיות לפחות 8 תווים',
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

    // Login auto-initiation
    'auto_init_starting' => 'ניסיון התחברות',
    'auto_init_starting_desc' => 'אנחנו יוצרים קשר עם מערכת האימות שלך להתחלת תהליך ההתחברות. במידה ולאחר 5 שניות לא בוצעה התחברות יש ללחוץ על הקישור מטה.',
    'auto_init_start_link' => 'המשך עם האימות',

    // Password Reset
    'reset_password' => 'איפוס סיסמא',
    'reset_password_send_instructions' => 'יש להזין את כתובת המייל למטה ואנו נשלח אלייך הוראות לאיפוס הסיסמא',
    'reset_password_send_button' => 'שלח קישור לאיפוס סיסמא',
    'reset_password_sent' => 'קישור לשחזור סיסמה יישלח ל:email אם כתובת המייל קיימת במערכת.',
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
    'email_confirm_success' => 'כתובת המייל שלך אומתה! כעת תוכל/י להתחבר באמצעות כתובת מייל זו.',
    'email_confirm_resent' => 'אימות נשלח לאי-מייל שלך, יש לבדוק בתיבת הדואר הנכנס',
    'email_confirm_thanks' => 'תודה על האישור!',
    'email_confirm_thanks_desc' => 'בבקשה המתן בזמן שהאישוך שלך מטופל. במידה ולא הופנתה לאחר 3 שניות לחץ על "המשך" מטה בכדי להמשיך.',

    'email_not_confirmed' => 'כתובת המייל לא אומתה',
    'email_not_confirmed_text' => 'כתובת המייל שלך טרם אומתה',
    'email_not_confirmed_click_link' => 'יש ללחוץ על הקישור אשר נשלח אליך לאחר ההרשמה',
    'email_not_confirmed_resend' => 'אם אינך מוצא את המייל, ניתן לשלוח בשנית את האימות על ידי לחיצה על הכפתור למטה',
    'email_not_confirmed_resend_button' => 'שלח שוב מייל אימות',

    // User Invite
    'user_invite_email_subject' => 'הוזמנת להצטרף ל:appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'לחץ על הכפתור מטה בכדי להגדיר סיסמת משתמש ולקבל גישה:',
    'user_invite_email_action' => 'הגדר סיסמה לחשבון',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'אימות סיסמא',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'הגדר אימות רב-שלבי',
    'mfa_setup_desc' => 'הגדר אימות רב-שלבי כשכבת אבטחה נוספת עבור החשבון שלך.',
    'mfa_setup_configured' => 'כבר הוגדר',
    'mfa_setup_reconfigure' => 'הגדר מחדש',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Setup',
    'mfa_backup_codes_usage_limit_warning' => 'נשאר לך פחות מ 5 קודי גיבוי, בבקשה חולל ואחסן סט חדש לפני שיגמרו לך הקודים בכדי למנוע נעילה מחוץ לחשבון שלך.',
    'mfa_option_totp_title' => 'אפליקציה לנייד',
    'mfa_option_totp_desc' => 'בכדי להשתמש באימות רב-שלבי תצטרך אפליקציית מובייל תומכת TOTP כמו Google Authenticator, Authy או Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'קודי גיבוי',
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
