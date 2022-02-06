<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'يجب الموافقة على :attribute.',
    'active_url'           => ':attribute ليس رابط صالح.',
    'after'                => 'يجب أن يكون التاريخ :attribute بعد :date.',
    'alpha'                => 'يجب أن يقتصر :attribute على الحروف فقط.',
    'alpha_dash'           => 'يجب أن يقتصر :attribute على حروف أو أرقام أو شرطات فقط.',
    'alpha_num'            => 'يجب أن يقتصر :attribute على الحروف والأرقام فقط.',
    'array'                => 'يجب أن تكون السمة مصفوفة.',
    'backup_codes'         => 'The provided code is not valid or has already been used.',
    'before'               => 'يجب أن يكون التاريخ :attribute قبل :date.',
    'between'              => [
        'numeric' => 'يجب أن يكون :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون :attribute بين :min و :max كيلو بايت.',
        'string'  => 'يجب أن يكون :attribute بين :min و :max حرف / حروف.',
        'array'   => 'يجب أن يكون :attribute بين :min و :max عنصر / عناصر.',
    ],
    'boolean'              => 'يجب أن يحتمل حقل السمة الصحة أو الخطأ.',
    'confirmed'            => ':attribute غير مطابق.',
    'date'                 => ':attribute ليس تاريخ صالح.',
    'date_format'          => ':attribute لا يطابق الصيغة :format.',
    'different'            => 'يجب أن يكون :attribute مختلف عن :other.',
    'digits'               => 'يجب أن يكون :attribute بعدد :digits خانات.',
    'digits_between'       => 'يجب أن يكون :attribute بعدد خانات بين :min و :max.',
    'email'                => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالح.',
    'ends_with' => 'يجب أن تنتهي السمة بأحد القيم التالية',
    'file'                 => 'The :attribute must be provided as a valid file.',
    'filled'               => 'حقل :attribute مطلوب.',
    'gt'                   => [
        'numeric' => 'يجب أن تكون السمة أكبر من: القيمة.',
        'file'    => 'يجب أن تكون السمة أكبر من: القيمة كيلوبايت.',
        'string'  => 'يجب أن تكون السمة أكبر من: أحرف القيمة.',
        'array'   => 'يجب أن تحتوي السمة على أكثر من: عناصر القيمة.',
    ],
    'gte'                  => [
        'numeric' => 'يجب أن تكون السمة أكبر من أو تساوي: القيمة.',
        'file'    => 'يجب أن تكون السمة أكبر من أو تساوي: القيمة كيلوبايت.',
        'string'  => 'يجب أن تكون السمة أكبر من أو تساوي: أحرف القيمة.',
        'array'   => 'يجب أن تحتوي السمة على: عناصر القيمة أو أكثر.',
    ],
    'exists'               => ':attribute المحدد غير صالح.',
    'image'                => 'يجب أن يكون :attribute صورة.',
    'image_extension'      => 'يجب أن تحتوي السمة على امتداد صورة صالح ومدعوم.',
    'in'                   => ':attribute المحدد غير صالح.',
    'integer'              => 'يجب أن يكون :attribute عدد صحيح.',
    'ip'                   => 'يجب أن يكون :attribute عنوان IP صالح.',
    'ipv4'                 => 'يجب أن تكون السمة: عنوان IPv4 صالحًا.',
    'ipv6'                 => 'يجب أن تكون السمة: عنوان IPv6 صالحًا.',
    'json'                 => 'يجب أن تكون السمة: سلسلة من نوع جسون JSON صالح.',
    'lt'                   => [
        'numeric' => 'يجب أن تكون السمة أقل من: القيمة.',
        'file'    => 'يجب أن تكون السمة أقل من: القيمة كيلوبايت.',
        'string'  => 'يجب أن تكون السمة أقل من: أحرف القيمة.',
        'array'   => 'يجب أن تحتوي السمة على أقل من: عناصر القيمة.',
    ],
    'lte'                  => [
        'numeric' => 'يجب أن تكون السمة أقل من أو تساوي: القيمة.',
        'file'    => 'يجب أن تكون السمة أقل من أو تساوي: القيمة كيلوبايت.',
        'string'  => 'يجب أن تكون السمة أقل من أو تساوي: أحرف القيمة.',
        'array'   => 'يجب ألا تحتوي السمة على أكثر من: عناصر القيمة.',
    ],
    'max'                  => [
        'numeric' => 'يجب ألا يكون :attribute أكبر من :max.',
        'file'    => 'يجب ألا يكون :attribute أكبر من :max كيلو بايت.',
        'string'  => 'يجب ألا يكون :attribute أكثر من :max حرف / حروف.',
        'array'   => 'يجب ألا يحتوي :attribute على أكثر من :max عنصر / عناصر.',
    ],
    'mimes'                => 'يجب أن يكون :attribute ملف من نوع: :values.',
    'min'                  => [
        'numeric' => 'يجب أن يكون :attribute على الأقل :min.',
        'file'    => 'يجب أن يكون :attribute على الأقل :min كيلو بايت.',
        'string'  => 'يجب أن يكون :attribute على الأقل :min حرف / حروف.',
        'array'   => 'يجب أن يحتوي :attribute على :min عنصر / عناصر كحد أدنى.',
    ],
    'not_in'               => ':attribute المحدد غير صالح.',
    'not_regex'            => 'صيغة السمة: غير صالحة.',
    'numeric'              => 'يجب أن يكون :attribute رقم.',
    'regex'                => 'صيغة :attribute غير صالحة.',
    'required'             => 'حقل :attribute مطلوب.',
    'required_if'          => 'حقل :attribute مطلوب عندما يكون :other :value.',
    'required_with'        => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_with_all'    => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without'     => 'حقل :attribute مطلوب عندما تكون :values غير موجودة.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا يكون أي من :values موجودة.',
    'same'                 => 'يجب تطابق :attribute مع :other.',
    'safe_url'             => 'قد لايكون الرابط المتوفر آمنا.',
    'size'                 => [
        'numeric' => 'يجب أن يكون :attribute بحجم :size.',
        'file'    => 'يجب أن يكون :attribute بحجم :size كيلو بايت.',
        'string'  => 'يجب أن يكون :attribute بعدد :size حرف / حروف.',
        'array'   => 'يجب أن يحتوي :attribute على :size عنصر / عناصر.',
    ],
    'string'               => 'يجب أن تكون السمة: سلسلة.',
    'timezone'             => 'يجب أن تكون :attribute منطقة صالحة.',
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => 'تم حجز :attribute من قبل.',
    'url'                  => 'صيغة :attribute غير صالحة.',
    'uploaded'             => 'تعذر تحميل الملف. قد لا يقبل الخادم ملفات بهذا الحجم.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'يجب تأكيد كلمة المرور',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
