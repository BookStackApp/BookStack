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
    'backup_codes'         => 'Предоставеният код не е валиден или вече е бил използван.',
    'before'               => ':attribute трябва да е дата след :date.',
    'between'              => [
        'numeric' => ':attribute трябва да е между :min и :max.',
        'file'    => ':attribute трябва да е между :min и :max килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде между :min и :max символа.',
        'array'   => 'Атрибутът :attribute трябва да има между :min и :max елемента.',
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
    'file'                 => 'Атрибутът :attribute трябва да бъде предоставен като валиден файл.',
    'filled'               => 'Полето :attribute е задължителен.',
    'gt'                   => [
        'numeric' => ':attribute трябва да бъде по-голям от :value.',
        'file'    => 'Големината на :attribute трябва да бъде по-голямо от :value килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде по-голямо от :value символа.',
        'array'   => 'Атрибутът :attribute трябва да има повече от :value елемента.',
    ],
    'gte'                  => [
        'numeric' => 'Атрибутът :attribute трябва бъде равен на или по-голям от :value.',
        'file'    => 'Големината на :attribute трябва да бъде по-голямо или равно на :value килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде по-голямо или равно на :value символа.',
        'array'   => 'Атрибутът :attribute трябва да има поне :value елемента или повече.',
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
        'array'   => 'Атрибутът :attribute трябва да има по-малко от :value елемента.',
    ],
    'lte'                  => [
        'numeric' => ':attribute трябва да бъде по-малко или равно на :value.',
        'file'    => 'Големината на :attribute трябва да бъде по-малко или равно на :value килобайта.',
        'string'  => 'Дължината на :attribute трябва да бъде по-малко или равно на :value символа.',
        'array'   => 'Атрибутът :attribute не трябва да има повече от :value елемента.',
    ],
    'max'                  => [
        'numeric' => ':attribute не трябва да бъде по-голям от :max.',
        'file'    => 'Големината на :attribute не може да бъде по-голямо от :value килобайта.',
        'string'  => 'Дължината на :attribute не може да бъде по-голямо от :value символа.',
        'array'   => 'Атрибутът :attribute не може да има повече от :max елемента.',
    ],
    'mimes'                => 'Атрибутът :attribute трябва да бъде файл от тип: :values.',
    'min'                  => [
        'numeric' => 'Атрибутът :attribute трябва да бъде поне :min.',
        'file'    => 'Атрибутът :attribute трябва да бъде поне :min килобайта.',
        'string'  => 'Атрибутът :attribute трябва да бъде съдържа поне :min символа.',
        'array'   => 'Атрибутът :attribute трябва да има поне :min елемента.',
    ],
    'not_in'               => 'Избраният :attribute не е валиден.',
    'not_regex'            => 'Форматът на :attribute не е валиден.',
    'numeric'              => 'Атрибутът :attribute трябва да бъде число.',
    'regex'                => 'Форматът на :attribute не е валиден.',
    'required'             => 'Полето :attribute е задължително.',
    'required_if'          => 'Полето :attribute е задължително, когато :other е :value.',
    'required_with'        => 'Полето :attribute е задължително, когато :values е налично.',
    'required_with_all'    => 'Полето :attribute е задължително, когато :values са налични.',
    'required_without'     => 'Полето :attribute е задължително, когато :values не е налично.',
    'required_without_all' => 'Полето :attribute е задължително, когато никоя стойност от :values не е налична.',
    'same'                 => 'Атрибутът :attribute и :other трябва да си съвпадат.',
    'safe_url'             => 'Предоставеният линк може да не е сигурен.',
    'size'                 => [
        'numeric' => 'Атрибутът :attribute трябва да бъде :size.',
        'file'    => 'Атрибутът :attribute трябва да бъде :size килобайта.',
        'string'  => 'Атрибутът :attribute трябва да бъде с дължина :size знака.',
        'array'   => 'Атрибутът :attribute трябва да съдържа :size елемента.',
    ],
    'string'               => 'Атрибутът :attribute трябва да бъде текст.',
    'timezone'             => 'Атрибутът :attribute трябва да бъде валидна зона.',
    'totp'                 => 'Предоставеният код не е валиден или е изтекъл.',
    'unique'               => 'Атрибутът :attribute вече е зает.',
    'url'                  => 'Форматът на :attribute не е валиден.',
    'uploaded'             => 'Файлът не можа да бъде качен. Сървърът може да не приема файлове с такъв размер.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Изисква се потвърждение на паролата',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
