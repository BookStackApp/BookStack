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
    'alpha_dash'           => 'El camp :attribute només pot contenir lletres, xifres, guionets i guions baixos.',
    'alpha_num'            => 'El camp :attribute només pot contenir lletres, xifres.',
    'array'                => 'El camp :attribute ha de ser una matriu.',
    'backup_codes'         => 'El codi que heu proporcionat no és vàlid o ja s’ha utilitzat.',
    'before'               => 'El camp :attribute ha de ser una data posterior a :date.',
    'between'              => [
        'numeric' => 'El camp :attribute ha de ser un nombre entre :min i :max.',
        'file'    => 'El camp :attribute ha de tenir entre :min i :max kilobytes.',
        'string'  => 'El camp :attribute ha de tenir entre :min i :max caràcters.',
        'array'   => 'El camp :attribute ha de tenir entre :min i :max elements.',
    ],
    'boolean'              => 'El camp :attribute ha de ser cert o fals.',
    'confirmed'            => 'La confirmació del camp :attribute no coincideix.',
    'date'                 => 'El camp :attribute no és una data vàlida.',
    'date_format'          => 'El camp :attribute no coincideix amb el format :format.',
    'different'            => 'El camp :attribute i :other han de ser diferents.',
    'digits'               => 'El camp :attribute ha de tenir :digits xifres.',
    'digits_between'       => 'El camp :attribute ha de tenir entre :min i :max xifres.',
    'email'                => 'El camp :attribute ha de ser un adreça electrònica vàlida.',
    'ends_with' => 'El camp :attribute ha d’acabar amb un dels signes següents: :values',
    'file'                 => 'El camp :attribute ha de ser un fitxer vàlid.',
    'filled'               => 'El camp :attribute és obligatori.',
    'gt'                   => [
        'numeric' => 'El camp :attribute ha de ser més gran que :value.',
        'file'    => 'El camp :attribute ha de tenir més de :value kilobytes.',
        'string'  => 'El camp :attribute ha de tenir més de :value caràcters.',
        'array'   => 'El camp :attribute ha de tenir més de :value elements.',
    ],
    'gte'                  => [
        'numeric' => 'El camp :attribute ha de ser com a mínim :value.',
        'file'    => 'El camp :attribute ha de tenir com a mínim :value kilobytes.',
        'string'  => 'El camp :attribute ha de tenir com a mínim :value caràcters.',
        'array'   => 'El camp :attribute ha de tenir com a mínim :value elements.',
    ],
    'exists'               => 'El camp :attribute seleccionat no és vàlid.',
    'image'                => 'El camp :attribute ha de ser una imatge.',
    'image_extension'      => 'El camp :attribute ha de tenir una extensió d’imatge compatible.',
    'in'                   => 'El camp :attribute no és vàlid.',
    'integer'              => 'El camp :attribute ha de ser un nombre enter.',
    'ip'                   => 'El camp :attribute ha de ser un adreça IP vàlida.',
    'ipv4'                 => 'El camp :attribute ha de ser un adreça IPv4 vàlida.',
    'ipv6'                 => 'El camp :attribute ha de ser un adreça IPv6 vàlida.',
    'json'                 => 'El camp :attribute ha de ser una cadena JSON vàlida.',
    'lt'                   => [
        'numeric' => 'El camp :attribute ha de ser més petit que :value.',
        'file'    => 'El camp :attribute ha de tenir menys de :value kilobytes.',
        'string'  => 'El camp :attribute ha de tenir menys de :value caràcters.',
        'array'   => 'El camp :attribute ha de tenir menys de :value elements.',
    ],
    'lte'                  => [
        'numeric' => 'El camp :attribute ha de ser com a màxim :value.',
        'file'    => 'El camp :attribute ha de tenir com a màxim :value kilobytes.',
        'string'  => 'El camp :attribute ha de tenir com a màxim :value caràcters.',
        'array'   => 'El camp :attribute ha de tenir com a màxim :value elements.',
    ],
    'max'                  => [
        'numeric' => 'El camp :attribute ha de ser com a màxim :max.',
        'file'    => 'El camp :attribute ha de tenir com a màxim :max kilobytes.',
        'string'  => 'El camp :attribute ha de tenir com a màxim :max caràcters.',
        'array'   => 'El camp :attribute ha de tenir com a màxim :max elements.',
    ],
    'mimes'                => 'El camp :attribute ha de ser un fitxer del tipus: :values.',
    'min'                  => [
        'numeric' => 'El camp :attribute ha de ser com a mínim :min.',
        'file'    => 'El camp :attribute ha de tenir com a mínim :min kilobytes.',
        'string'  => 'El camp :attribute ha de tenir com a mínim :min caràcters.',
        'array'   => 'El camp :attribute ha de tenir com a mínim :min elements.',
    ],
    'not_in'               => 'El camp :attribute no és vàlid.',
    'not_regex'            => 'El format :attribute no és vàlid.',
    'numeric'              => 'El camp :attribute ha de ser un nombre.',
    'regex'                => 'El format :attribute no és vàlid.',
    'required'             => 'El camp :attribute és obligatori.',
    'required_if'          => 'El camp :attribute és obligatori quan :other és :value.',
    'required_with'        => 'El camp :attribute és obligatori quan hi ha :values.',
    'required_with_all'    => 'El camp :attribute és obligatori quan hi ha tots aquests valors: :values.',
    'required_without'     => 'El camp :attribute és obligatori quan no hi ha :values.',
    'required_without_all' => 'El camp :attribute és obligatori quan no hi ha cap d’aquests valors: :values.',
    'same'                 => 'El camp :attribute i :other han de coincidir.',
    'safe_url'             => 'És possible que l’enllaç proporcionat no sigui segur.',
    'size'                 => [
        'numeric' => 'El camp :attribute ha de ser :size.',
        'file'    => 'El camp :attribute ha de tenir :size kilobytes.',
        'string'  => 'El camp :attribute ha de tenir :size caràcters',
        'array'   => 'El camp :attribute ha de tenir :size elements.',
    ],
    'string'               => 'El camp :attribute ha de ser una cadena de text.',
    'timezone'             => 'El camp :attribute ha de ser un fus horari vàlid.',
    'totp'                 => 'El codi proporcionat no és vàlid o ha caducat.',
    'unique'               => 'El camp :attribute ja s’ha utilitzat.',
    'url'                  => 'El format :attribute no és vàlid.',
    'uploaded'             => 'No s’ha pogut pujar el fitxer. És possible que el servidor no admeti fitxers d’aquesta mida.',

    'zip_file' => 'El :attribute necessita fer referència a un arxiu dins del ZIP.',
    'zip_file_mime' => 'El :attribute necessita fer referència a un arxiu de tipus :validTyes, trobat :foundType.',
    'zip_model_expected' => 'S\'esperava un objecte de dades, però s\'ha trobat ":type".',
    'zip_unique' => 'El :attribute ha de ser únic pel tipus d\'objecte dins del ZIP.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Heu de confirmar la contrasenya.',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
