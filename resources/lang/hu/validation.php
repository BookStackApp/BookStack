<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute elfogadott kell legyen.',
    'active_url'           => ':attribute nem érvényes webcím.',
    'after'                => ':attribute dátumnak :date utáninak kell lennie.',
    'alpha'                => ':attribute csak betűket tartalmazhat.',
    'alpha_dash'           => ':attribute csak betűket, számokat és kötőjeleket tartalmazhat.',
    'alpha_num'            => ':attribute csak betűket és számokat tartalmazhat.',
    'array'                => ':attribute tömb kell legyen.',
    'before'               => ':attribute dátumnak :date előttinek kell lennie.',
    'between'              => [
        'numeric' => ':attribute értékének :min és :max között kell lennie.',
        'file'    => ':attribute értékének :min és :max kilobájt között kell lennie.',
        'string'  => ':attribute hosszának :min és :max karakter között kell lennie.',
        'array'   => ':attribute mennyiségének :min és :max elem között kell lennie.',
    ],
    'boolean'              => ':attribute mezőnek igaznak vagy hamisnak kell lennie.',
    'confirmed'            => ':attribute megerősítés nem egyezik.',
    'date'                 => ':attribute nem érvényes dátum.',
    'date_format'          => ':attribute nem egyezik :format formátummal.',
    'different'            => ':attribute és :other értékének különböznie kell.',
    'digits'               => ':attribute :digits számból kell álljon.',
    'digits_between'       => ':attribute hosszának :min és :max számjegy között kell lennie.',
    'email'                => ':attribute érvényes email cím kell legyen.',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'filled'               => ':attribute mező kötelező.',
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
    'exists'               => 'A kiválasztott :attribute érvénytelen.',
    'image'                => ':attribute kép kell legyen.',
    'image_extension'      => 'A :attribute kép kiterjesztése érvényes és támogatott kell legyen.',
    'in'                   => 'A kiválasztott :attribute érvénytelen.',
    'integer'              => ':attribute egész szám kell legyen.',
    'ip'                   => ':attribute érvényes IP cím kell legyen.',
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
        'numeric' => ':attribute nem lehet nagyobb mint :max.',
        'file'    => ':attribute nem lehet nagyobb mint :max kilobájt.',
        'string'  => ':attribute nem lehet nagyobb mint :max karakter.',
        'array'   => ':attribute mennyisége nem lehet több mint :max elem.',
    ],
    'mimes'                => 'A :attribute típusa csak :values lehet.',
    'min'                  => [
        'numeric' => ':attribute legalább :min kell legyen.',
        'file'    => ':attribute legalább :min kilobájt kell legyen.',
        'string'  => ':attribute legalább :min karakter kell legyen.',
        'array'   => ':attribute legalább :min elem kell legyen.',
    ],
    'no_double_extension'  => ':attribute csak egy fájlkiterjesztéssel rendelkezhet.',
    'not_in'               => 'A kiválasztott :attribute érvénytelen.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => ':attribute szám kell legyen.',
    'regex'                => ':attribute formátuma érvénytelen.',
    'required'             => ':attribute mező kötelező.',
    'required_if'          => ':attribute mező kötelező ha :other értéke :value.',
    'required_with'        => ':attribute mező kötelező ha :values be van állítva.',
    'required_with_all'    => ':attribute mező kötelező ha van :value.',
    'required_without'     => ':attribute mező kötelező ha :values nincs beállítva.',
    'required_without_all' => ':attribute mező kötelező ha egyik :values sincs beállítva.',
    'same'                 => ':attribute és :other értékének egyeznie kell.',
    'size'                 => [
        'numeric' => ':attribute :size méretű kell legyen.',
        'file'    => ':attribute :size kilobájt méretű kell legyen.',
        'string'  => ':attribute :size karakter kell legyen.',
        'array'   => ':attribute : size elemet kell tartalmazzon.',
    ],
    'string'               => ':attribute karaktersorozatnak kell legyen.',
    'timezone'             => ':attribute érvényes zóna kell legyen.',
    'unique'               => ':attribute már elkészült.',
    'url'                  => ':attribute formátuma érvénytelen.',
    'uploaded'             => 'A fájlt nem lehet feltölteni. A kiszolgáló nem fogad el ilyen méretű fájlokat.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Jelszó megerősítés szükséges',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
