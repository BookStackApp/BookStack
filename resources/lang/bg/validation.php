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
        'file'    => 'Големината на :attribute трябва да бъде по-голямо от :value килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде по-голямо от :value символа.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'Големината на :attribute трябва да бъде по-голямо или равно на :value килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде по-голямо или равно на :value символа.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'exists'               => 'Избраният :attribute е невалиден.',
    'image'                => ':attribute трябва да e изображение.',
    'image_extension'      => ':attribute трябва да е валиден и/или допустим графичен файлов формат.',
    'in'                   => 'Избраният :attribute е невалиден.',
    'integer'              => ':attribute трябва да бъде цяло число.',
    'ip'                   => ':attribute трябва да бъде валиден IP адрес.',
    'ipv4'                 => ':attribute трябва да бъде валиден IPv4 адрес.',
    'ipv6'                 => ':attribute трябва да бъде валиден IPv6 адрес.',
    'json'                 => ':attribute трябва да съдържа валиден JSON.',
    'lt'                   => [
        'numeric' => ':attribute трябва да бъде по-малко от :value.',
        'file'    => 'Големината на :attribute трябва да бъде по-малко от :value килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде по-малко от :value символа.',
        'array'   => 'The :attribute must have less than :value items.',
    ],
    'lte'                  => [
        'numeric' => ':attribute трябва да бъде по-малко или равно на :value.',
        'file'    => 'Големината на :attribute трябва да бъде по-малко или равно на :value килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде по-малко или равно на :value символа.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max'                  => [
        'numeric' => ':attribute не трябва да бъде по-голям от :max.',
        'file'    => 'Големината на :attribute не може да бъде по-голямо от :value килобайта.',
        'string'  => 'Дължината на :attribute не може да бъде по-голямо от :value символа.',
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
