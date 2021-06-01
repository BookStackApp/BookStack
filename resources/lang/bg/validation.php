<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute трябва да бъде одобрен.',
    'active_url'           => ':attribute не е валиден URL адрес.',
    'after'                => ':attribute трябва да е дата след :date.',
    'alpha'                => ':attribute може да съдържа само букви.',
    'alpha_dash'           => ':attribute може да съдържа само букви, числа, тире и долна черта.',
    'alpha_num'            => ':attribute може да съдържа само букви и числа.',
    'array'                => ':attribute трябва да е масив (array).',
    'before'               => ':attribute трябва да е дата след :date.',
    'between'              => [
        'numeric' => ':attribute трябва да е между :min и :max.',
        'file'    => ':attribute трябва да е между :min и :max килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде между :min и :max символа.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'Полето :attribute трябва да съдържа булева стойност (true или false).',
    'confirmed'            => 'Потвърждението на :attribute не съвпада.',
    'date'                 => ':attribute не е валидна дата.',
    'date_format'          => ':attribute не е в посоченият формат - :format.',
    'different'            => ':attribute и :other трябва да са различни.',
    'digits'               => ':attribute трябва да съдържа :digits цифри.',
    'digits_between'       => ':attribute трябва да бъде с дължина между :min и :max цифри.',
    'email'                => ':attribute трябва да бъде валиден имейл адрес.',
    'ends_with' => ':attribute трябва да свършва с един от следните символи: :values',
    'filled'               => 'Полето :attribute е задължителен.',
    'gt'                   => [
        'numeric' => ':attribute трябва да бъде по-голям от :value.',
        'file'    => 'Големината на :attribute трябва да бъде над :value килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде над :value символа.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'Големината на :attribute трябва да бъде по-голямо или равно на :value килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде по-голямо или равно на :value символа.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'exists'               => 'The selected :attribute is invalid.',
    'image'                => 'The :attribute must be an image.',
    'image_extension'      => 'The :attribute must have a valid & supported image extension.',
    'in'                   => 'The selected :attribute is invalid.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
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
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'safe_url'             => 'The provided link may not be safe.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'The :attribute has already been taken.',
    'url'                  => 'The :attribute format is invalid.',
    'uploaded'             => 'The file could not be uploaded. The server may not accept files of this size.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Password confirmation required',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
