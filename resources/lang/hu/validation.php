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
    'filled'               => ':attribute mező kötelező.',
    'exists'               => 'A kiválasztott :attribute érvénytelen.',
    'image'                => ':attribute kép kell legyen.',
    'image_extension'      => 'A :attribute kép kiterjesztése érvényes és támogatott kell legyen.',
    'in'                   => 'A kiválasztott :attribute érvénytelen.',
    'integer'              => ':attribute egész szám kell legyen.',
    'ip'                   => ':attribute érvényes IP cím kell legyen.',
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
