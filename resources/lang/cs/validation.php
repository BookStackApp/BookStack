<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute musí být přijat.',
    'active_url'           => ':attribute není platnou URL adresou.',
    'after'                => ':attribute musí být datum po :date.',
    'alpha'                => ':attribute může obsahovat pouze písmena.',
    'alpha_dash'           => ':attribute může obsahovat pouze písmena, číslice, pomlčky a podtržítka. České znaky (á, é, í, ó, ú, ů, ž, š, č, ř, ď, ť, ň) nejsou podporovány.',
    'alpha_num'            => ':attribute může obsahovat pouze písmena a číslice.',
    'array'                => ':attribute musí být pole.',
    'backup_codes'         => 'Zadaný kód není platný nebo již byl použit.',
    'before'               => ':attribute musí být datum před :date.',
    'between'              => [
        'numeric' => ':attribute musí být hodnota mezi :min a :max.',
        'file'    => ':attribute musí být větší než :min a menší než :max Kilobytů.',
        'string'  => ':attribute musí být delší než :min a kratší než :max znaků.',
        'array'   => ':attribute musí obsahovat nejméně :min a nesmí obsahovat více než :max prvků.',
    ],
    'boolean'              => ':attribute musí být true nebo false',
    'confirmed'            => ':attribute nesouhlasí.',
    'date'                 => ':attribute musí být platné datum.',
    'date_format'          => ':attribute není platný formát data podle :format.',
    'different'            => ':attribute a :other se musí lišit.',
    'digits'               => ':attribute musí být :digits pozic dlouhé.',
    'digits_between'       => ':attribute musí být dlouhé nejméně :min a nejvíce :max pozic.',
    'email'                => ':attribute není platný formát.',
    'ends_with' => ':attribute musí končit jednou z následujících hodnot: :values',
    'file'                 => 'The :attribute must be provided as a valid file.',
    'filled'               => ':attribute musí být vyplněno.',
    'gt'                   => [
        'numeric' => ':attribute musí být větší než :value.',
        'file'    => 'Velikost souboru :attribute musí být větší než :value kB.',
        'string'  => 'Počet znaků :attribute musí být větší :value.',
        'array'   => 'Pole :attribute musí mít více prvků než :value.',
    ],
    'gte'                  => [
        'numeric' => ':attribute musí být větší nebo rovno :value.',
        'file'    => 'Velikost souboru :attribute musí být větší nebo rovno :value kB.',
        'string'  => 'Počet znaků :attribute musí být větší nebo rovno :value.',
        'array'   => 'Pole :attribute musí mít :value prvků nebo více.',
    ],
    'exists'               => 'Zvolená hodnota pro :attribute není platná.',
    'image'                => ':attribute musí být obrázek.',
    'image_extension'      => ':attribute musí mít platné a podporované rozšíření obrázku.',
    'in'                   => 'Zvolená hodnota pro :attribute je neplatná.',
    'integer'              => ':attribute musí být celé číslo.',
    'ip'                   => ':attribute musí být platnou IP adresou.',
    'ipv4'                 => ':attribute musí být platná IPv4 adresa.',
    'ipv6'                 => ':attribute musí být platná IPv6 adresa.',
    'json'                 => ':attribute musí být platný JSON řetězec.',
    'lt'                   => [
        'numeric' => ':attribute musí být menší než :value.',
        'file'    => 'Velikost souboru :attribute musí být menší než :value kB.',
        'string'  => ':attribute musí obsahovat méně než :value znaků.',
        'array'   => ':attribute by měl obsahovat méně než :value položek.',
    ],
    'lte'                  => [
        'numeric' => ':attribute musí být menší nebo rovno :value.',
        'file'    => 'Velikost souboru :attribute musí být menší než :value kB.',
        'string'  => ':attribute nesmí být delší než :value znaků.',
        'array'   => ':attribute by měl obsahovat maximálně :value položek.',
    ],
    'max'                  => [
        'numeric' => ':attribute nemůže být větší než :max.',
        'file'    => 'Velikost souboru :attribute musí být menší než :value kB.',
        'string'  => ':attribute nemůže být delší než :max znaků.',
        'array'   => ':attribute nemůže obsahovat více než :max prvků.',
    ],
    'mimes'                => ':attribute musí být jeden z následujících datových typů :values.',
    'min'                  => [
        'numeric' => ':attribute musí být větší než :min.',
        'file'    => ':attribute musí být větší než :min kB.',
        'string'  => ':attribute musí být delší než :min znaků.',
        'array'   => ':attribute musí obsahovat více než :min prvků.',
    ],
    'not_in'               => 'Zvolená hodnota pro :attribute je neplatná.',
    'not_regex'            => ':attribute musí být regulární výraz.',
    'numeric'              => ':attribute musí být číslo.',
    'regex'                => ':attribute nemá správný formát.',
    'required'             => ':attribute musí být vyplněno.',
    'required_if'          => ':attribute musí být vyplněno pokud :other je :value.',
    'required_with'        => ':attribute musí být vyplněno pokud :values je vyplněno.',
    'required_with_all'    => ':attribute musí být vyplněno pokud :values je zvoleno.',
    'required_without'     => ':attribute musí být vyplněno pokud :values není vyplněno.',
    'required_without_all' => ':attribute musí být vyplněno pokud není žádné z :values zvoleno.',
    'same'                 => ':attribute a :other se musí shodovat.',
    'safe_url'             => 'Zadaný odkaz může být nebezpečný.',
    'size'                 => [
        'numeric' => ':attribute musí být přesně :size.',
        'file'    => ':attribute musí mít přesně :size Kilobytů.',
        'string'  => ':attribute musí být přesně :size znaků dlouhý.',
        'array'   => ':attribute musí obsahovat právě :size prvků.',
    ],
    'string'               => ':attribute musí být řetězec znaků.',
    'timezone'             => ':attribute musí být platná časová zóna.',
    'totp'                 => 'Zadaný kód je neplatný nebo vypršel.',
    'unique'               => ':attribute musí být unikátní.',
    'url'                  => 'Formát :attribute je neplatný.',
    'uploaded'             => 'Nahrávání :attribute se nezdařilo.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Je nutné potvrdit heslo',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
