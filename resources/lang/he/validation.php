<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'שדה :attribute חייב להיות מסומן.',
    'active_url'           => 'שדה :attribute הוא לא כתובת אתר תקנית.',
    'after'                => 'שדה :attribute חייב להיות תאריך אחרי :date.',
    'alpha'                => 'שדה :attribute יכול להכיל אותיות בלבד.',
    'alpha_dash'           => 'שדה :attribute יכול להכיל אותיות, מספרים ומקפים בלבד.',
    'alpha_num'            => 'שדה :attribute יכול להכיל אותיות ומספרים בלבד.',
    'array'                => 'שדה :attribute חייב להיות מערך.',
    'before'               => 'שדה :attribute חייב להיות תאריך לפני :date.',
    'between'              => [
        'numeric' => 'שדה :attribute חייב להיות בין :min ל-:max.',
        'file'    => 'שדה :attribute חייב להיות בין :min ל-:max קילובייטים.',
        'string'  => 'שדה :attribute חייב להיות בין :min ל-:max תווים.',
        'array'   => 'שדה :attribute חייב להיות בין :min ל-:max פריטים.',
    ],
    'boolean'              => 'שדה :attribute חייב להיות אמת או שקר.',
    'confirmed'            => 'שדה האישור של :attribute לא תואם.',
    'date'                 => 'שדה :attribute אינו תאריך תקני.',
    'date_format'          => 'שדה :attribute לא תואם את הפורמט :format.',
    'different'            => 'שדה :attribute ושדה :other חייבים להיות שונים.',
    'digits'               => 'שדה :attribute חייב להיות בעל :digits ספרות.',
    'digits_between'       => 'שדה :attribute חייב להיות בין :min ו-:max ספרות.',
    'email'                => 'שדה :attribute חייב להיות כתובת אימייל תקנית.',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'filled'               => 'שדה :attribute הוא חובה.',
    'gt'                   => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'string'  => 'The :attribute must be greater than :value characters.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'string'  => 'The :attribute must be greater than or equal :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'exists'               => 'בחירת ה-:attribute אינה תקפה.',
    'image'                => 'שדה :attribute חייב להיות תמונה.',
    'image_extension'      => 'שדה :attribute חייב להיות מסוג תמונה נתמך',
    'in'                   => 'בחירת ה-:attribute אינה תקפה.',
    'integer'              => 'שדה :attribute חייב להיות מספר שלם.',
    'ip'                   => 'שדה :attribute חייב להיות כתובת IP תקנית.',
    'ipv4'                 => 'The :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'The :attribute must be a valid IPv6 address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'lt'                   => [
        'numeric' => 'The :attribute must be less than :value.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'string'  => 'The :attribute must be less than :value characters.',
        'array'   => 'The :attribute must have less than :value items.',
    ],
    'lte'                  => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'string'  => 'The :attribute must be less than or equal :value characters.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max'                  => [
        'numeric' => 'שדה :attribute אינו יכול להיות גדול מ-:max.',
        'file'    => 'שדה :attribute לא יכול להיות גדול מ-:max קילובייטים.',
        'string'  => 'שדה :attribute לא יכול להיות גדול מ-:max characters.',
        'array'   => 'שדה :attribute לא יכול להכיל יותר מ-:max פריטים.',
    ],
    'mimes'                => 'שדה :attribute צריך להיות קובץ מסוג: :values.',
    'min'                  => [
        'numeric' => 'שדה :attribute חייב להיות לפחות :min.',
        'file'    => 'שדה :attribute חייב להיות לפחות :min קילובייטים.',
        'string'  => 'שדה :attribute חייב להיות לפחות :min תווים.',
        'array'   => 'שדה :attribute חייב להיות לפחות :min פריטים.',
    ],
    'not_in'               => 'בחירת ה-:attribute אינה תקפה.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => 'שדה :attribute חייב להיות מספר.',
    'regex'                => 'שדה :attribute בעל פורמט שאינו תקין.',
    'required'             => 'שדה :attribute הוא חובה.',
    'required_if'          => 'שדה :attribute נחוץ כאשר :other הוא :value.',
    'required_with'        => 'שדה :attribute נחוץ כאשר :values נמצא.',
    'required_with_all'    => 'שדה :attribute נחוץ כאשר :values נמצא.',
    'required_without'     => 'שדה :attribute נחוץ כאשר :values לא בנמצא.',
    'required_without_all' => 'שדה :attribute נחוץ כאשר אף אחד מ-:values נמצאים.',
    'same'                 => 'שדה :attribute ו-:other חייבים להיות זהים.',
    'safe_url'             => 'The provided link may not be safe.',
    'size'                 => [
        'numeric' => 'שדה :attribute חייב להיות :size.',
        'file'    => 'שדה :attribute חייב להיות :size קילובייטים.',
        'string'  => 'שדה :attribute חייב להיות :size תווים.',
        'array'   => 'שדה :attribute חייב להכיל :size פריטים.',
    ],
    'string'               => 'שדה :attribute חייב להיות מחרוזת.',
    'timezone'             => 'שדה :attribute חייב להיות איזור תקני.',
    'unique'               => 'שדה :attribute כבר תפוס.',
    'url'                  => 'שדה :attribute בעל פורמט שאינו תקין.',
    'uploaded'             => 'שדה :attribute ארעה שגיאה בעת ההעלאה.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'נדרש אימות סיסמא',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
