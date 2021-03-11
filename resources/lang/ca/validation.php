<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'Cal que accepteu el camp :attribute.',
    'active_url'           => 'El camp :attribute no és un URL vàlid.',
    'after'                => 'El camp :attribute ha de ser una data posterior a :date.',
    'alpha'                => 'El camp :attribute només pot contenir lletres.',
    'alpha_dash'           => 'El camp :attribute només pot contenir lletres, números, guions i guions baixos.',
    'alpha_num'            => 'El camp :attribute només pot contenir lletres i números.',
    'array'                => 'El camp :attribute ha de ser un vector.',
    'before'               => 'El camp :attribute ha de ser una data anterior a :date.',
    'between'              => [
        'numeric' => 'El camp :attribute ha d\'estar entre :min i :max.',
        'file'    => 'El camp :attribute ha de tenir entre :min i :max kilobytes.',
        'string'  => 'El camp :attribute ha de tenir entre :min i :max caràcters.',
        'array'   => 'El camp :attribute ha de tenir entre :min i :max elements.',
    ],
    'boolean'              => 'El camp :attribute ha de ser cert o fals.',
    'confirmed'            => 'La confirmació del camp :attribute no coincideix.',
    'date'                 => 'El camp :attribute no és una data vàlida.',
    'date_format'          => 'El camp :attribute no coincideix amb el format :format.',
    'different'            => 'Els camps :attribute i :other han de ser diferents.',
    'digits'               => 'El camp :attribute ha de tenir :digits dígits.',
    'digits_between'       => 'El camp :attribute ha de tenir entre :min i :max dígits.',
    'email'                => 'El camp :attribute ha de ser una adreça electrònica vàlida.',
    'ends_with' => 'El camp :attribute ha d\'acabar amb un dels següents valors: :values',
    'filled'               => 'El camp :attribute és obligatori.',
    'gt'                   => [
        'numeric' => 'El camp :attribute ha de ser més gran que :value.',
        'file'    => 'El camp :attribute ha de tenir més de :value kilobytes.',
        'string'  => 'El camp :attribute ha de tenir més de :value caràcters.',
        'array'   => 'El camp :attribute ha de tenir més de :value elements.',
    ],
    'gte'                  => [
        'numeric' => 'El camp :attribute ha de ser més gran o igual que :value.',
        'file'    => 'El camp :attribute ha de tenir :value kilobytes o més.',
        'string'  => 'El camp :attribute ha de tenir :value caràcters o més.',
        'array'   => 'El camp :attribute ha de tenir :value elements o més.',
    ],
    'exists'               => 'El camp :attribute no és vàlid.',
    'image'                => 'El camp :attribute ha de ser una imatge.',
    'image_extension'      => 'El camp :attribute ha de tenir una extensió d\'imatge vàlida i suportada.',
    'in'                   => 'El camp :attribute seleccionat no és vàlid.',
    'integer'              => 'El camp :attribute ha de ser un enter.',
    'ip'                   => 'El camp :attribute ha de ser una adreça IP vàlida.',
    'ipv4'                 => 'El camp :attribute ha de ser una adreça IPv4 vàlida.',
    'ipv6'                 => 'El camp :attribute ha de ser una adreça IPv6 vàlida.',
    'json'                 => 'El camp :attribute ha de ser una cadena JSON vàlida.',
    'lt'                   => [
        'numeric' => 'El camp :attribute ha de ser menor que :value.',
        'file'    => 'El camp :attribute ha de tenir menys de :value kilobytes.',
        'string'  => 'El camp :attribute ha de tenir menys de :value caràcters.',
        'array'   => 'El camp :attribute ha de tenir menys de :value elements.',
    ],
    'lte'                  => [
        'numeric' => 'El camp :attribute ha de ser més petit o igual que :value.',
        'file'    => 'El camp :attribute ha de tenir :value kilobytes o menys.',
        'string'  => 'El camp :attribute ha de tenir :value caràcters o menys.',
        'array'   => 'El camp :attribute ha de tenir :value elements o menys.',
    ],
    'max'                  => [
        'numeric' => 'El camp :attribute no pot ser més gran que :max.',
        'file'    => 'El camp :attribute no pot tenir més de :max kilobytes.',
        'string'  => 'El camp :attribute no pot tenir més de :max caràcters.',
        'array'   => 'El camp :attribute no pot tenir més de :max elements.',
    ],
    'mimes'                => 'El camp :attribute ha de ser un fitxer del tipus: :values.',
    'min'                  => [
        'numeric' => 'El camp :attribute no pot ser més petit que :min.',
        'file'    => 'El camp :attribute no pot tenir menys de :min kilobytes.',
        'string'  => 'El camp :attribute no pot tenir menys de :min caràcters.',
        'array'   => 'El camp :attribute no pot tenir menys de :min elements.',
    ],
    'no_double_extension'  => 'El camp :attribute només pot tenir una única extensió de fitxer.',
    'not_in'               => 'El camp :attribute seleccionat no és vàlid.',
    'not_regex'            => 'El format del camp :attribute no és vàlid.',
    'numeric'              => 'El camp :attribute ha de ser un número.',
    'regex'                => 'El format del camp :attribute no és vàlid.',
    'required'             => 'El camp :attribute és obligatori.',
    'required_if'          => 'El camp :attribute és obligatori quan :other és :value.',
    'required_with'        => 'El camp :attribute és obligatori quan hi ha aquest valor: :values.',
    'required_with_all'    => 'El camp :attribute és obligatori quan hi ha algun d\'aquests valors: :values.',
    'required_without'     => 'El camp :attribute és obligatori quan no hi ha aquest valor: :values.',
    'required_without_all' => 'El camp :attribute és obligatori quan no hi ha cap d\'aquests valors: :values.',
    'same'                 => 'Els camps :attribute i :other han de coincidir.',
    'safe_url'             => 'L\'enllaç proporcionat podria no ser segur.',
    'size'                 => [
        'numeric' => 'El camp :attribute ha de ser :size.',
        'file'    => 'El camp :attribute ha de tenir :size kilobytes.',
        'string'  => 'El camp :attribute ha de tenir :size caràcters.',
        'array'   => 'El camp :attribute ha de contenir :size elements.',
    ],
    'string'               => 'El camp :attribute ha de ser una cadena.',
    'timezone'             => 'El camp :attribute ha de ser una zona vàlida.',
    'unique'               => 'El camp :attribute ja està ocupat.',
    'url'                  => 'El format del camp :attribute no és vàlid.',
    'uploaded'             => 'No s\'ha pogut pujar el fitxer. És possible que el servidor no accepti fitxers d\'aquesta mida.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Cal la confirmació de la contrasenya',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
