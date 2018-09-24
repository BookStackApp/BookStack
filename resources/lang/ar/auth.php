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
    'failed' => 'البيانات المعطاة لا توافق سجلاتنا.',
    'throttle' => 'تجاوزت الحد الأقصى من المحاولات. الرجاء المحاولة مرة أخرى بعد :seconds seconds.',

    /**
     * Login & Register
     */
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
    'password_hint' => 'يجب أن تكون أكثر من 5 حروف',
    'forgot_password' => 'نسيت كلمة المرور؟',
    'remember_me' => 'تذكرني',
    'ldap_email_hint' => 'الرجاء إدخال عنوان بريد إلكتروني لاستخدامه مع الحساب.',
    'create_account' => 'إنشاء حساب',
    'social_login' => 'تسجيل الدخول باستخدام حسابات التواصل الاجتماعي',
    'social_registration' => 'إنشاء حساب باستخدام حسابات التواصل الاجتماعي',
    'social_registration_text' => 'إنشاء حساب والدخول باستخدام خدمة أخرى.',

    'register_thanks' => 'شكراً لتسجيل حسابك!',
    'register_confirm' => 'الرجاء مراجعة البريد الإلكتروني والضغط على زر التأكيد لاستخدام :appName.',
    'registrations_disabled' => 'التسجيل مغلق حالياً',
    'registration_email_domain_invalid' => 'المجال الخاص بالبريد الإلكتروني لا يملك حق الوصول لهذا التطبيق',
    'register_success' => 'شكراً لإنشاء حسابكم! تم تسجيلكم ودخولكم للحساب الخاص بكم.',


    /**
     * Password Reset
     */
    'reset_password' => 'استعادة كلمة المرور',
    'reset_password_send_instructions' => 'أدخل بريدك الإلكتروني بالأسفل وسيتم إرسال رسالة برابط لاستعادة كلمة المرور.',
    'reset_password_send_button' => 'أرسل رابط الاستعادة',
    'reset_password_sent_success' => 'تم إرسال رابط استعادة كلمة المرور إلى :email.',
    'reset_password_success' => 'تمت استعادة كلمة المرور بنجاح.',

    'email_reset_subject' => 'استعد كلمة المرور الخاصة بتطبيق :appName',
    'email_reset_text' => 'تم إرسال هذه الرسالة بسبب تلقينا لطلب استعادة كلمة المرور الخاصة بحسابكم.',
    'email_reset_not_requested' => 'إذا لم يتم طلب استعادة كلمة المرور من قبلكم, فلا حاجة لاتخاذ أية خطوات.',


    /**
     * Email Confirmation
     */
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
    'email_not_confirmed_resend' => 'إذا لم يتم إيجاد الرسالة, بإمكانكم إعادة إرسال رسالة التأكيد عن طريق تعبئة النموذج أدناه.',
    'email_not_confirmed_resend_button' => 'إعادة إرسال رسالة التأكيد',
];