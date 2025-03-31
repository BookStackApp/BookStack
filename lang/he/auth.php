<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'פרטי ההתחברות אינם תואמים את הנתונים שלנו.',
    'throttle' => 'נסיונות התחברות מהירים מדי, יש להמתין :seconds שניות ולנסות שנית.',

    // Login & Register
    'sign_up' => 'הרשמה למערכת',
    'log_in' => 'התחבר למערכת',
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
    'user_invite_email_greeting' => 'חשבון נוצר עבורך ב :appName.',
    'user_invite_email_text' => 'לחץ על הכפתור מטה בכדי להגדיר סיסמת משתמש ולקבל גישה:',
    'user_invite_email_action' => 'הגדר סיסמה לחשבון',
    'user_invite_page_welcome' => 'ברוכים הבאים ל :appName!',
    'user_invite_page_text' => 'על מנת לסיים את ההרשמה ולקבל גישה עלייך להגדיר סיסמה אשר תהיה בשימוש בהתחברות ל :appName בביקורים עתידיים.',
    'user_invite_page_confirm_button' => 'אימות סיסמא',
    'user_invite_success_login' => 'הסיסמה הוגדרה בהצלחה, כעת תוכלו לקבל גישה ל :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'הגדר אימות רב-שלבי',
    'mfa_setup_desc' => 'הגדר אימות רב-שלבי כשכבת אבטחה נוספת עבור החשבון שלך.',
    'mfa_setup_configured' => 'כבר הוגדר',
    'mfa_setup_reconfigure' => 'הגדר מחדש',
    'mfa_setup_remove_confirmation' => 'האם להסיר את אפשרות האימות הדו-שלבי הזאת?',
    'mfa_setup_action' => 'הגדרה',
    'mfa_backup_codes_usage_limit_warning' => 'נשאר לך פחות מ 5 קודי גיבוי, בבקשה חולל ואחסן סט חדש לפני שיגמרו לך הקודים בכדי למנוע נעילה מחוץ לחשבון שלך.',
    'mfa_option_totp_title' => 'אפליקציה לנייד',
    'mfa_option_totp_desc' => 'בכדי להשתמש באימות רב-שלבי תצטרך אפליקציית מובייל תומכת TOTP כמו Google Authenticator, Authy או Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'קודי גיבוי',
    'mfa_option_backup_codes_desc' => 'יצירת מערך סיסמאות חד-פעמיות כגיבוי אשר תתקבשו להזין על מנת לאמת את הזהות שלכם, אנא וודאו כי הקודים האלו שמורים במקום בטוח.',
    'mfa_gen_confirm_and_enable' => 'אישור והפעלה',
    'mfa_gen_backup_codes_title' => 'הגדרת קודי גיבוי',
    'mfa_gen_backup_codes_desc' => 'אנא שמור את רשימת הקודים הרשומים מטה במקום בטוח. בגישה למערכת תהיה אפשרות להשתמש באחד הקודים הללו כאמצעי זיהוי נוסף.',
    'mfa_gen_backup_codes_download' => 'הורדת קודים',
    'mfa_gen_backup_codes_usage_warning' => 'ניתן להשתמש בכל קוד פעם אחת בלבד',
    'mfa_gen_totp_title' => 'הגדרת אפליקציה לזיהוי',
    'mfa_gen_totp_desc' => 'על מנת להגדיר זיהוי דו-שלבי במכשיר נייד עלייך להשתמש באפליקציה התומכת ב TOTP כגון Google Authenticator, Authy או Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'סרוק את קוד ה QR באמצעות האפליקציה שבה מתבצע הזיהוי על מנת להתחיל.',
    'mfa_gen_totp_verify_setup' => 'אשר את ההגדרה',
    'mfa_gen_totp_verify_setup_desc' => 'על מנת לוודא כי הזיהוי הדו-שלבי עובד יש להכניס את הקוד, הוא מופיע לך על מסך האפליקציה, בשדה מטה:',
    'mfa_gen_totp_provide_code_here' => 'אנא הכנס את הקוד כאן',
    'mfa_verify_access' => 'אשר גישה',
    'mfa_verify_access_desc' => 'חשבון המשתמש שלך דורש ממך לאת את הזהות שלך בשכבת הגנה נוספת על מנת לאפשר לך גישה. יש לאשר גישה דרך אחד האמצעים הקיימים על מנת להמשיך.',
    'mfa_verify_no_methods' => 'אין אפשרויות אימות דו-שלבי מוגדרות',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'אמת באמצעות אפליקציה',
    'mfa_verify_use_backup_codes' => 'אמת באמצעות קוד גיבוי',
    'mfa_verify_backup_code' => 'קוד גיבוי',
    'mfa_verify_backup_code_desc' => 'הזן מטה אחד מקודי הגיבוי הנותרים לך:',
    'mfa_verify_backup_code_enter_here' => 'הזן קוד גיבוי כאן',
    'mfa_verify_totp_desc' => 'הזן את הקוד, שהונפק דרך האפליקציה שלך, מטה:',
    'mfa_setup_login_notification' => 'Multi-factor method configured, Please now login again using the configured method.',
];
