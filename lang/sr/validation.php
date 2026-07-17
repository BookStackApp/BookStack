<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute мора бити прихваћен.',
    'active_url'           => ':attribute није исправна URL адреса.',
    'after'                => ':attribute мора бити датум после :date.',
    'alpha'                => ':attribute може да садржи само слова.',
    'alpha_dash'           => ':attribute може да садржи само слова, бројеве, црте и подцрте.',
    'alpha_num'            => ':attribute може да садржи само слова и бројеве.',
    'array'                => ':attribute мора бити низ.',
    'backup_codes'         => 'Достављени код није исправан или је већ искоришћен.',
    'before'               => ':attribute мора бити датум пре :date.',
    'between'              => [
        'numeric' => ':attribute мора бити између :min и :max.',
        'file'    => ':attribute мора бити између :min и :max килобајта.',
        'string'  => ':attribute мора бити између :min и :max карактера.',
        'array'   => ':attribute мора бити између :min и :max ставки.',
    ],
    'boolean'              => 'Поље :attribute мора бити тачно или нетачно.',
    'confirmed'            => ':attribute потврда се не подудара.',
    'date'                 => ':attribute није исправан датум.',
    'date_format'          => ':attribute се не подудара са форматом :format.',
    'different'            => ':attribute и :other се морају разликовати.',
    'digits'               => ':attribute мора бити :digits цифри.',
    'digits_between'       => ':attribute мора бити између :min и :max цифара.',
    'email'                => ':attribute морабити исправна адреса е-поште.',
    'ends_with' => ':attribute се мора завршити са једним од следећих: :values',
    'file'                 => ':attribute мора бити исправна достављена датотека.',
    'filled'               => ':attribute поље је неопходно.',
    'gt'                   => [
        'numeric' => ':attribute мора бити веће од :value.',
        'file'    => ':attribute мора бити веће од :value килобајта.',
        'string'  => ':attribute мора бити веће од :value карактера.',
        'array'   => ':attribute мора садржати више од :value ставки.',
    ],
    'gte'                  => [
        'numeric' => ':attribute мора бити веће од или једнако :value.',
        'file'    => ':attribute мора бити веће од или једнако :value килобајта.',
        'string'  => ':attribute мора бити веће од или једнако :value карактера.',
        'array'   => ':attribute мора да садржи :value или више ставки.',
    ],
    'exists'               => 'Изабрани :attribute је неисправан.',
    'image'                => ':attribute мора бити слика.',
    'image_extension'      => ':attribute мора да има исправну и подржану екстензију слике.',
    'in'                   => 'Изабрани :attribute је неисправан.',
    'integer'              => ':attribute мора бити цели број.',
    'ip'                   => ':attribute мора бити исправна ИП адреса.',
    'ipv4'                 => ':attribute мора бити исправна IPv4 адреса.',
    'ipv6'                 => ':attribute мора бити исправна IPv6 адреса.',
    'json'                 => ':attribute мора бити исправна JSON ниска.',
    'lt'                   => [
        'numeric' => ':attribute мора бити мање од :value.',
        'file'    => ':attribute мора бити мање од :value килобајта.',
        'string'  => ':attribute мора бити мање од :value карактера.',
        'array'   => ':attribute мора садржати мање од :value ставки.',
    ],
    'lte'                  => [
        'numeric' => ':attribute мора бити мање од или једнако :value.',
        'file'    => ':attribute мора бити мање од или једнако :value килобајта.',
        'string'  => ':attribute мора бити мање од или једнако :value карактера.',
        'array'   => ':attribute не сме садржати више од :value ставки.',
    ],
    'max'                  => [
        'numeric' => ':attribute не може бити већи од :max.',
        'file'    => ':attribute не може бити већи од :max килобајта.',
        'string'  => ':attribute не може бити већи од :max знакова.',
        'array'   => ':attribute не може садржати више од :max ставки.',
    ],
    'mimes'                => ':attribute мора бити датотека типа: :values.',
    'min'                  => [
        'numeric' => ':attribute мора бити најмање :min.',
        'file'    => ':attribute мора бити најмање :min килобајта.',
        'string'  => ':attribute мора бити најмање :min карактера.',
        'array'   => ':attribute мора садржати најмање :min ставки.',
    ],
    'not_in'               => 'Изабрани :attribute је неисправан.',
    'not_regex'            => ':attribute формат је неисправан.',
    'numeric'              => ':attribute мора бити број.',
    'regex'                => ':attribute формат је неисправан.',
    'required'             => ':attribute поље је неопходно.',
    'required_if'          => ':attribute поље је неопходно када :other је :value.',
    'required_with'        => 'Поље :attribute је обавезно када је :values присутно.',
    'required_with_all'    => 'Поље :attribute је обавезно када је :values присутно.',
    'required_without'     => 'Поље :attribute је обавезно када :values није присутно.',
    'required_without_all' => 'Поље :attribute је обавезно када ниједно од :values није присутно.',
    'same'                 => ':attribute и :other се морају поклапати.',
    'safe_url'             => 'Достављена веза можда није безбедна.',
    'size'                 => [
        'numeric' => ':attribute мора бити :size.',
        'file'    => ':attribute мора бити :size килобајта.',
        'string'  => ':attribute мора бити :size карактера.',
        'array'   => ':attribute мора да садржи :size ставки.',
    ],
    'string'               => ':attribute мора бити текст.',
    'timezone'             => ':attribute мора бити исправна зона.',
    'totp'                 => 'Достављени код није исправан или је истекао.',
    'unique'               => ':attribute је већ заузет.',
    'url'                  => ':attribute формат је неисправан.',
    'uploaded'             => 'Датотека није могла бити отпремљена. Сервер можда не прихвата датотеке ове величине.',

    'zip_file' => ':attribute мора бити референца за датотеку унутар ZIP-а.',
    'zip_file_size' => ':attribute датотеке не сме бити већи од :size MB.',
    'zip_file_mime' => ':attribute мора бити референца за датотеку типа :validTypes, пронађено :foundType.',
    'zip_model_expected' => 'Очекиван је објекат податка али је ":type" пронађен.',
    'zip_unique' => ':attribute мора бити јединствено за тип објекта унутар ZIP-а.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Неопходна је потврда лозинке',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
