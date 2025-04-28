<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'البيانات المعطاة لا توافق سجلاتنا.',
    'throttle' => 'تجاوزت الحد الأقصى من المحاولات. الرجاء المحاولة مرة أخرى بعد :seconds ثانية/ثواني.',

    // Login & Register
    'sign_up' => 'إنشاء حساب',
    'log_in' => 'تسجيل الدخول',
    'log_in_with' => 'تسجيل الدخول باستخدام :socialDriver',
    'sign_up_with' => 'إنشاء حساب باستخدام :socialDriver',
    'logout' => 'الخروج',

    'name' => 'الاسم',
    'username' => 'اسم المستخدم',
    'email' => 'البريد الإلكتروني',
    'password' => 'كلمة السر',
    'password_confirm' => 'تأكيد كلمة السر',
    'password_hint' => 'يجب أن تحتوي كلمة السر على 8 خانات على الأقل',
    'forgot_password' => 'نسيت كلمة السر؟',
    'remember_me' => 'تذكرني',
    'ldap_email_hint' => 'الرجاء إدخال عنوان بريد إلكتروني لاستخدامه مع الحساب.',
    'create_account' => 'إنشاء حساب',
    'already_have_account' => 'لديك حساب مسبقاً؟',
    'dont_have_account' => 'ليس لديك حساب؟',
    'social_login' => 'تسجيل الدخول باستخدام حسابات التواصل الاجتماعي',
    'social_registration' => 'إنشاء حساب باستخدام حسابات التواصل الاجتماعي',
    'social_registration_text' => 'إنشاء حساب والدخول باستخدام خدمة أخرى.',

    'register_thanks' => 'شكراً لتسجيل حسابك!',
    'register_confirm' => 'الرجاء مراجعة البريد الإلكتروني والضغط على زر التأكيد لاستخدام :appName.',
    'registrations_disabled' => 'التسجيل مغلق حالياً',
    'registration_email_domain_invalid' => 'المجال الخاص بالبريد الإلكتروني لا يملك حق الوصول لهذا التطبيق',
    'register_success' => 'شكراً لإنشاء حسابكم! تم تسجيلكم ودخولكم للحساب الخاص بكم.',

    // Login auto-initiation
    'auto_init_starting' => 'محاولة تسجيل الدخول',
    'auto_init_starting_desc' => 'نحن نتصل بنظام المصادقة الخاص بك لبدء عملية تسجيل الدخول. إذا لم يحدث أي تقدم بعد 5 ثوان يمكنك محاولة النقر على الرابط أدناه.',
    'auto_init_start_link' => 'المتابعة مع المصادقة',

    // Password Reset
    'reset_password' => 'استعادة كلمة السر',
    'reset_password_send_instructions' => 'أدخل بريدك الإلكتروني بالأسفل وسيتم إرسال رسالة برابط لاستعادة كلمة السر.',
    'reset_password_send_button' => 'أرسل رابط الاستعادة',
    'reset_password_sent' => 'سيتم إرسال رابط إعادة تعيين كلمة السر إلى عنوان البريد الإلكتروني هذا إذا كان موجودًا في النظام.',
    'reset_password_success' => 'تمت استعادة كلمة السر بنجاح.',
    'email_reset_subject' => 'استعد كلمة السر الخاصة بتطبيق :appName',
    'email_reset_text' => 'تم إرسال هذه الرسالة بسبب تلقينا لطلب استعادة كلمة السر الخاصة بحسابكم.',
    'email_reset_not_requested' => 'إذا لم يتم طلب استعادة كلمة السر من قبلكم، فلا حاجة لاتخاذ أية خطوات.',

    // Email Confirmation
    'email_confirm_subject' => 'تأكيد بريدكم الإلكتروني لتطبيق :appName',
    'email_confirm_greeting' => 'شكرا لانضمامكم إلى :appName!',
    'email_confirm_text' => 'الرجاء تأكيد بريدكم الإلكتروني بالضغط على الزر أدناه:',
    'email_confirm_action' => 'تأكيد البريد الإلكتروني',
    'email_confirm_send_error' => 'تأكيد البريد الإلكتروني مطلوب ولكن النظام لم يستطع إرسال الرسالة. تواصل مع مشرف النظام للتأكد من إعدادات البريد.',
    'email_confirm_success' => 'تم تأكيد بريدك الإلكتروني! يمكنك الآن تسجيل الدخول باستخدام عنوان البريد الإلكتروني هذا.',
    'email_confirm_resent' => 'تمت إعادة إرسال رسالة التأكيد، الرجاء مراجعة صندوق الوارد.',
    'email_confirm_thanks' => 'شكرا للتأكيد!',
    'email_confirm_thanks_desc' => 'الرجاء الانتظار لحظة بينما يتم التعامل مع التأكيد الخاص بك. إذا لم يتم إعادة توجيهك بعد 3 ثوان اضغط على الرابط "المتابعة" أدناه للمتابعة.',

    'email_not_confirmed' => 'لم يتم تأكيد البريد الإلكتروني',
    'email_not_confirmed_text' => 'لم يتم بعد تأكيد عنوان البريد الإلكتروني.',
    'email_not_confirmed_click_link' => 'الرجاء الضغط على الرابط المرسل إلى بريدكم الإلكتروني بعد تسجيلكم.',
    'email_not_confirmed_resend' => 'إذا لم يتم إيجاد الرسالة، بإمكانكم إعادة إرسال رسالة التأكيد عن طريق تعبئة النموذج أدناه.',
    'email_not_confirmed_resend_button' => 'إعادة إرسال رسالة التأكيد',

    // User Invite
    'user_invite_email_subject' => 'تمت دعوتك للانضمام إلى صفحة الحالة الخاصة بـ :app_name!',
    'user_invite_email_greeting' => 'تم إنشاء حساب مستخدم لك على :appName.',
    'user_invite_email_text' => 'انقر على الزر أدناه لتعيين كلمة سر الحساب والحصول على الوصول:',
    'user_invite_email_action' => 'كلمة سر المستخدم',
    'user_invite_page_welcome' => 'مرحبا بكم في :appName!',
    'user_invite_page_text' => 'لإكمال حسابك والحصول على حق الوصول تحتاج إلى تعيين كلمة السر سيتم استخدامها لتسجيل الدخول إلى :appName في الزيارات المستقبلية.',
    'user_invite_page_confirm_button' => 'تأكيد كلمة السر',
    'user_invite_success_login' => 'تم تأكيد كلمة السر. يمكنك الآن تسجيل الدخول باستخدام كلمة السر المحددة للوصول إلى :appName !',

    // Multi-factor Authentication
    'mfa_setup' => 'إعداد المصادقة متعددة العوامل',
    'mfa_setup_desc' => 'إعداد المصادقة متعددة العوامل كطبقة إضافية من الأمان لحساب المستخدم الخاص بك.',
    'mfa_setup_configured' => 'تم إعداده مسبقاً',
    'mfa_setup_reconfigure' => 'إعادة التكوين',
    'mfa_setup_remove_confirmation' => 'متأكد من أنك تريد إزالة طريقة المصادقة متعددة العوامل هذه؟',
    'mfa_setup_action' => 'إعداد',
    'mfa_backup_codes_usage_limit_warning' => 'لديك أقل من 5 رموز احتياطية متبقية، الرجاء إنشاء وتخزين مجموعة جديدة قبل نفاد الرموز لتجنب إغلاق حسابك.',
    'mfa_option_totp_title' => 'تطبيق الجوال',
    'mfa_option_totp_desc' => 'لاستخدام المصادقة المتعددة العوامل، ستحتاج إلى تطبيق جوال يدعم كلمة السر المؤقته -TOTP- مثل جوجل أوثنتيكاتور -Google Authenticator- أو أوثي -Authy- أو مايكروسوفت أوثنتيكاتور -Microsoft Authenticator-.',
    'mfa_option_backup_codes_title' => 'رموز النسخ الاحتياطي',
    'mfa_option_backup_codes_desc' => 'إنشاء مجموعة من رموز النسخ الاحتياطية للاستخدام مرة واحدة و التي سَتُدِخلها عند تسجيل الدخول للتحقق من هويتك. احرص أن تخزينها في مكان آمن.',
    'mfa_gen_confirm_and_enable' => 'تأكيد وتمكين',
    'mfa_gen_backup_codes_title' => 'إعداد رموز النسخ الاحتياطي',
    'mfa_gen_backup_codes_desc' => 'خَزِن قائمة الرموز أدناه في مكان آمن. عند الوصول إلى النظام، ستتمكن من استخدام أحد الرموز كآلية مصادقة ثانية.',
    'mfa_gen_backup_codes_download' => 'تنزيل الرموز',
    'mfa_gen_backup_codes_usage_warning' => 'يمكن استخدام كل رمز مرة واحدة فقط',
    'mfa_gen_totp_title' => 'إعداد تطبيق الجوال',
    'mfa_gen_totp_desc' => 'لاستخدام المصادقة المتعددة ، ستحتاج إلى تطبيق جوال كلمة السر المؤقته -TOTP- مثل جوجل أوثنتيكاتور -Google Authenticator- أو أوثي -Authy- أو مايكروسوفت أوثنتيكاتور -Microsoft Authenticator-.',
    'mfa_gen_totp_scan' => 'امسح رمز الاستجابة السريعة -QR- أدناه باستخدام تطبيق المصادقة المفضل لديك للبدء.',
    'mfa_gen_totp_verify_setup' => 'التحقق من الإعداد',
    'mfa_gen_totp_verify_setup_desc' => 'تحقق أن كل شيء يعمل عن طريق إدخال رمز تم إنشاؤه داخل تطبيق المصادقة الخاص بك في مربع الإدخال أدناه:',
    'mfa_gen_totp_provide_code_here' => 'أدخل الرمز الذي تم إنشاؤه للتطبيق الخاص بك هنا',
    'mfa_verify_access' => 'التحقق من الوصول',
    'mfa_verify_access_desc' => 'يتطلب حساب المستخدم الخاص بك تأكيد هويتك عن طريق مستوى إضافي من التحقق قبل منحك حق الوصول. تحقق استخدام إحدى الطرق التي إعدادها للمتابعة.',
    'mfa_verify_no_methods' => 'لا توجد طرق معدة',
    'mfa_verify_no_methods_desc' => 'لم يتم العثور على طرق مصادقة متعددة العوامل لحسابك. ستحتاج إلى إعداد طريقة واحدة على الأقل قبل أن تتمكن من الوصول.',
    'mfa_verify_use_totp' => 'التحقق باستخدام تطبيق الجوال',
    'mfa_verify_use_backup_codes' => 'التحقق باستخدام رمز النسخ الاحتياطي',
    'mfa_verify_backup_code' => 'الرموز الاحتياطية',
    'mfa_verify_backup_code_desc' => 'أدخل أحد الرموز الاحتياطية المتبقية أدناه:',
    'mfa_verify_backup_code_enter_here' => 'أدخل الرمز الاحتياطي هنا',
    'mfa_verify_totp_desc' => 'أدخل الرمز الذي تم إنشاؤه باستخدام تطبيق الجوال الخاص بك، أدناه:',
    'mfa_setup_login_notification' => 'تم إعداد طريقة الدخول متعددة العوامل، يرجى الآن تسجيل الدخول مرة أخرى باستخدام الطريقة التي تم إعدادها.',
];
