<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'يجب الموافقة على :attribute.',
    'active_url'           => ':attribute ليس رابط صالح.',
    'after'                => 'يجب أن يكون التاريخ :attribute بعد :date.',
    'alpha'                => 'يجب أن يقتصر :attribute على الحروف فقط.',
    'alpha_dash'           => 'يجب أن يقتصر :attribute على حروف أو أرقام أو شرطات فقط.',
    'alpha_num'            => 'يجب أن يقتصر :attribute على الحروف والأرقام فقط.',
    'array'                => 'The :attribute must be an array.', // جار البحث عن الترجمة الأنسب
    'before'               => 'يجب أن يكون التاريخ :attribute قبل :date.',
    'between'              => [
        'numeric' => 'يجب أن يكون :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون :attribute بين :min و :max كيلو بايت.',
        'string'  => 'يجب أن يكون :attribute بين :min و :max حرف / حروف.',
        'array'   => 'يجب أن يكون :attribute بين :min و :max عنصر / عناصر.',
    ],
    'boolean'              => 'The :attribute field must be true or false.', // جار البحث عن الترجمة الأنسب
    'confirmed'            => ':attribute غير مطابق.',
    'date'                 => ':attribute ليس تاريخ صالح.',
    'date_format'          => ':attribute لا يطابق الصيغة :format.',
    'different'            => 'يجب أن يكون :attribute مختلف عن :other.',
    'digits'               => 'يجب أن يكون :attribute بعدد :digits خانات.',
    'digits_between'       => 'يجب أن يكون :attribute بعدد خانات بين :min و :max.',
    'email'                => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالح.',
    'filled'               => 'حقل :attribute مطلوب.',
    'exists'               => ':attribute المحدد غير صالح.',
    'image'                => 'يجب أن يكون :attribute صورة.',
    'in'                   => ':attribute المحدد غير صالح.',
    'integer'              => 'يجب أن يكون :attribute عدد صحيح.',
    'ip'                   => 'يجب أن يكون :attribute عنوان IP صالح.',
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
    'numeric'              => 'يجب أن يكون :attribute رقم.',
    'regex'                => 'صيغة :attribute غير صالحة.',
    'required'             => 'حقل :attribute مطلوب.',
    'required_if'          => 'حقل :attribute مطلوب عندما يكون :other :value.',
    'required_with'        => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_with_all'    => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without'     => 'حقل :attribute مطلوب عندما تكون :values غير موجودة.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا يكون أي من :values موجودة.',
    'same'                 => 'يجب تطابق :attribute مع :other.',
    'size'                 => [
        'numeric' => 'يجب أن يكون :attribute بحجم :size.',
        'file'    => 'يجب أن يكون :attribute بحجم :size كيلو بايت.',
        'string'  => 'يجب أن يكون :attribute بعدد :size حرف / حروف.',
        'array'   => 'يجب أن يحتوي :attribute على :size عنصر / عناصر.',
    ],
    'string'               => 'The :attribute must be a string.', // جار البحث عن الترجمة الأنسب
    'timezone'             => 'يجب أن تكون :attribute منطقة صالحة.',
    'unique'               => 'تم حجز :attribute من قبل.',
    'url'                  => 'صيغة :attribute غير صالحة.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'password-confirm' => [
            'required_with' => 'يجب تأكيد كلمة المرور',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
