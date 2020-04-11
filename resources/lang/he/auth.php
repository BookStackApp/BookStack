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
    'registration_email_domain_invalid' => 'לא ניתן להרשם באמצעות המייל שסופק',
    'register_success' => 'תודה על הרשמתך! ניתן כעת להתחבר',


    // Password Reset
    'reset_password' => 'איפוס סיסמא',
    'reset_password_send_instructions' => 'יש להזין את כתובת המייל למטה ואנו נשלח אלייך הוראות לאיפוס הסיסמא',
    'reset_password_send_button' => 'שלח קישור לאיפוס סיסמא',
    'reset_password_sent_success' => 'שלחנו הוראות לאיפוס הסיסמא אל :email',
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
    'email_confirm_success' => 'האי-מייל שלך אושר!',
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
    'user_invite_success' => 'Password set, you now have access to :appName!'
];