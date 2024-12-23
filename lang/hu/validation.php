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
    'backup_codes'         => 'A megadott kód érvénytelen, vagy már felhasználták.',
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
    'ends_with' => ':attribute attribútumnak a következők egyikével kell végződnie: :values',
    'file'                 => 'A(z) :attribute érvényes fájlnak kell lennie.',
    'filled'               => ':attribute mező kötelező.',
    'gt'                   => [
        'numeric' => ':attribute nagyobb kell, hogy legyen, mint :value.',
        'file'    => ':attribute nagyobb kell, hogy legyen, mint :value kilobájt.',
        'string'  => ':attribute nagyobb kell legyen mint :value karakter.',
        'array'   => ':attribute több, mint :value elemet kell, hogy tartalmazzon.',
    ],
    'gte'                  => [
        'numeric' => ':attribute attribútumnak :value értéknél nagyobbnak vagy vele egyenlőnek kell lennie.',
        'file'    => 'A(z) :attribute mérete nem lehet kevesebb, mint :value kilobájt.',
        'string'  => 'A(z) :attribute nagyobbnak, vagy egyenlőnek kell lennie, mint a :value karakter.',
        'array'   => 'A(z) :attribute rendelkezzen :value vagy több elemmel.',
    ],
    'exists'               => 'A kiválasztott :attribute érvénytelen.',
    'image'                => ':attribute kép kell legyen.',
    'image_extension'      => 'A :attribute kép kiterjesztése érvényes és támogatott kell legyen.',
    'in'                   => 'A kiválasztott :attribute érvénytelen.',
    'integer'              => ':attribute egész szám kell legyen.',
    'ip'                   => ':attribute érvényes IP cím kell legyen.',
    'ipv4'                 => 'A(z) :attribute érvényes IPv4 címnek kell lennie.',
    'ipv6'                 => 'A(z) :attribute érvényes IPv6 címnek kell lennie.',
    'json'                 => 'A(z) :attribute érvényes JSON stringnek kell lennie.',
    'lt'                   => [
        'numeric' => 'A(z) :attribute kisebb kell, hogy legyen, mint :value.',
        'file'    => 'A(z) :attribute kevesebbnek kell lennie, mint :value kilobájt.',
        'string'  => 'A(z) :attribute rövidebb kell, hogy legyen, mint :value karakter.',
        'array'   => 'A(z) :attribute kevesebb, mint :value elemet kell, hogy tartalmazzon.',
    ],
    'lte'                  => [
        'numeric' => 'A(z) :attribute kisebb vagy egyenlő kell, hogy legyen, mint :value.',
        'file'    => 'A(z) :attribute mérete nem lehet több, mint :value kilobájt.',
        'string'  => 'A(z) :attribute hossza nem lehet több, mint :value karakter.',
        'array'   => 'A(z) :attribute legfeljebb :value elemet kell, hogy tartalmazzon.',
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
    'not_in'               => 'A kiválasztott :attribute érvénytelen.',
    'not_regex'            => ':attribute formátuma érvénytelen.',
    'numeric'              => ':attribute szám kell legyen.',
    'regex'                => ':attribute formátuma érvénytelen.',
    'required'             => ':attribute mező kötelező.',
    'required_if'          => ':attribute mező kötelező ha :other értéke :value.',
    'required_with'        => ':attribute mező kötelező ha :values be van állítva.',
    'required_with_all'    => ':attribute mező kötelező ha van :value.',
    'required_without'     => ':attribute mező kötelező ha :values nincs beállítva.',
    'required_without_all' => ':attribute mező kötelező ha egyik :values sincs beállítva.',
    'same'                 => ':attribute és :other értékének egyeznie kell.',
    'safe_url'             => 'Előfordulhat, hogy a megadott link nem biztonságos.',
    'size'                 => [
        'numeric' => ':attribute :size méretű kell legyen.',
        'file'    => ':attribute :size kilobájt méretű kell legyen.',
        'string'  => ':attribute :size karakter kell legyen.',
        'array'   => ':attribute : size elemet kell tartalmazzon.',
    ],
    'string'               => ':attribute karaktersorozatnak kell legyen.',
    'timezone'             => ':attribute érvényes zóna kell legyen.',
    'totp'                 => 'A megadott kód érvénytelen vagy lejárt.',
    'unique'               => ':attribute már elkészült.',
    'url'                  => ':attribute formátuma érvénytelen.',
    'uploaded'             => 'A fájlt nem lehet feltölteni. A kiszolgáló nem fogad el ilyen méretű fájlokat.',

    'zip_file' => 'The :attribute needs to reference a file within the ZIP.',
    'zip_file_mime' => 'The :attribute needs to reference a file of type :validTypes, found :foundType.',
    'zip_model_expected' => 'Data object expected but ":type" found.',
    'zip_unique' => 'The :attribute must be unique for the object type within the ZIP.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Jelszó megerősítés szükséges',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
