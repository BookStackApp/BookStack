<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'البيانات المعطاة لا توافق سجلاتنا.',
    'throttle' => 'تجاوزت الحد الأقصى من المحاولات. الرجاء المحاولة مرة أخرى بعد :seconds seconds.',

    // Login & Register
    'sign_up' => 'إنشاء حساب',
    'log_in' => 'تسجيل الدخول',
    'log_in_with' => 'تسجيل الدخول باستخدام :socialDriver',
    'sign_up_with' => 'إنشاء حساب باستخدام :socialDriver',
    'logout' => 'تسجيل الخروج',

    'name' => 'الاسم',
    'username' => 'اسم المستخدم',
    'email' => 'البريد الإلكتروني',
    'password' => 'كلمة المرور',
    'password_confirm' => 'تأكيد كلمة المرور',
    'password_hint' => 'يجب أن تكون أكثر من 7 حروف',
    'forgot_password' => 'نسيت كلمة المرور؟',
    'remember_me' => 'تذكرني',
    'ldap_email_hint' => 'الرجاء إدخال عنوان بريد إلكتروني لاستخدامه مع الحساب.',
    'create_account' => 'إنشاء حساب',
    'already_have_account' => 'لديك حساب بالفعل؟',
    'dont_have_account' => 'ليس لديك حساب؟',
    'social_login' => 'تسجيل الدخول باستخدام حسابات التواصل الاجتماعي',
    'social_registration' => 'إنشاء حساب باستخدام حسابات التواصل الاجتماعي',
    'social_registration_text' => 'إنشاء حساب والدخول باستخدام خدمة أخرى.',

    'register_thanks' => 'شكراً لتسجيل حسابك!',
    'register_confirm' => 'الرجاء مراجعة البريد الإلكتروني والضغط على زر التأكيد لاستخدام :appName.',
    'registrations_disabled' => 'التسجيل مغلق حالياً',
    'registration_email_domain_invalid' => 'المجال الخاص بالبريد الإلكتروني لا يملك حق الوصول لهذا التطبيق',
    'register_success' => 'شكراً لإنشاء حسابكم! تم تسجيلكم ودخولكم للحساب الخاص بكم.',


    // Password Reset
    'reset_password' => 'استعادة كلمة المرور',
    'reset_password_send_instructions' => 'أدخل بريدك الإلكتروني بالأسفل وسيتم إرسال رسالة برابط لاستعادة كلمة المرور.',
    'reset_password_send_button' => 'أرسل رابط الاستعادة',
    'reset_password_sent' => 'سيتم إرسال رابط إعادة تعيين كلمة المرور إلى عنوان البريد الإلكتروني هذا إذا كان موجودًا في النظام.',
    'reset_password_success' => 'تمت استعادة كلمة المرور بنجاح.',
    'email_reset_subject' => 'استعد كلمة المرور الخاصة بتطبيق :appName',
    'email_reset_text' => 'تم إرسال هذه الرسالة بسبب تلقينا لطلب استعادة كلمة المرور الخاصة بحسابكم.',
    'email_reset_not_requested' => 'إذا لم يتم طلب استعادة كلمة المرور من قبلكم، فلا حاجة لاتخاذ أية خطوات.',


    // Email Confirmation
    'email_confirm_subject' => 'تأكيد بريدكم الإلكتروني لتطبيق :appName',
    'email_confirm_greeting' => 'شكرا لانضمامكم إلى :appName!',
    'email_confirm_text' => 'الرجاء تأكيد بريدكم الإلكتروني بالضغط على الزر أدناه:',
    'email_confirm_action' => 'تأكيد البريد الإلكتروني',
    'email_confirm_send_error' => 'تأكيد البريد الإلكتروني مطلوب ولكن النظام لم يستطع إرسال الرسالة. تواصل مع مشرف النظام للتأكد من إعدادات البريد.',
    'email_confirm_success' => 'تم تأكيد بريدكم الإلكتروني!',
    'email_confirm_resent' => 'تمت إعادة إرسال رسالة التأكيد. الرجاء مراجعة صندوق الوارد',

    'email_not_confirmed' => 'لم يتم تأكيد البريد الإلكتروني',
    'email_not_confirmed_text' => 'لم يتم بعد تأكيد عنوان البريد الإلكتروني.',
    'email_not_confirmed_click_link' => 'الرجاء الضغط على الرابط المرسل إلى بريدكم الإلكتروني بعد تسجيلكم.',
    'email_not_confirmed_resend' => 'إذا لم يتم إيجاد الرسالة، بإمكانكم إعادة إرسال رسالة التأكيد عن طريق تعبئة النموذج أدناه.',
    'email_not_confirmed_resend_button' => 'إعادة إرسال رسالة التأكيد',

    // User Invite
    'user_invite_email_subject' => 'تمت دعوتك للانضمام إلى صفحة الحالة الخاصة بـ :app_name!',
    'user_invite_email_greeting' => 'تم إنشاء حساب مستخدم لك على %site%.',
    'user_invite_email_text' => 'انقر على الزر أدناه لتعيين كلمة مرور الحساب والحصول على الوصول:',
    'user_invite_email_action' => 'كلمة سر المستخدم',
    'user_invite_page_welcome' => 'مرحبا بكم في :appName!',
    'user_invite_page_text' => 'لإكمال حسابك والحصول على حق الوصول تحتاج إلى تعيين كلمة مرور سيتم استخدامها لتسجيل الدخول إلى :appName في الزيارات المستقبلية.',
    'user_invite_page_confirm_button' => 'تأكيد كلمة المرور',
    'user_invite_success' => 'مجموعة كلمات المرور، لديك الآن حق الوصول إلى :appName!',

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