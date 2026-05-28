<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'A(z) :attribute elfogadott kell legyen.',
    'active_url'           => 'A(z) :attribute nem egy érvényes URL.',
    'after'                => 'A(z) :attribute objektumnak egy :date utáni dátumnak kell lennie.',
    'alpha'                => 'A(z) :attribute csak betűket tartalmazhat.',
    'alpha_dash'           => 'A(z) :attribute csak betűket, számokat, kötőjeleket és alávonásokat tartalmazhat.',
    'alpha_num'            => 'A(z) :attribute csak betűket és számokat tartalmazhat.',
    'array'                => 'A(z) :attribute tömb kell legyen.',
    'backup_codes'         => 'A megadott kód érvénytelen, vagy már felhasználták.',
    'before'               => 'A(z) :attribute objektumnak egy :date előtti dátumnak kell lennie.',
    'between'              => [
        'numeric' => 'A(z) :attribute értékének :min és :max között kell lennie.',
        'file'    => 'A(z) :attribute értékének :min és :max kB között kell lennie.',
        'string'  => 'A(z) :attribute hosszának :min és :max karakter között kell lennie.',
        'array'   => 'A(z) :attribute tömbnek :min és :max közötti elemszámának kell lennie.',
    ],
    'boolean'              => 'A(z) :attribute mezőnek igaznak vagy hamisnak kell lennie.',
    'confirmed'            => 'A(z) :attribute megerősítés nem egyezik.',
    'date'                 => 'A(z) :attribute nem egy érvényes dátum.',
    'date_format'          => 'A(z) :attribute nem egyezik a(z) :format formátummal.',
    'different'            => 'A(z) :attribute és :other értékének különböznie kell.',
    'digits'               => 'A(z) :attribute :digits számjegyből kell álljon.',
    'digits_between'       => 'A(z) :attribute hosszának :min és :max számjegy között kell lennie.',
    'email'                => 'A(z) :attribute érvényes email cím kell legyen.',
    'ends_with' => 'A(z) :attribute értékének a következők egyikével kell végződnie: :values',
    'file'                 => 'A(z) :attribute érvényes fájl kell legyen.',
    'filled'               => 'A(z) :attribute mező kötelező.',
    'gt'                   => [
        'numeric' => 'A(z) :attribute nagyobb kell, hogy legyen, mint :value.',
        'file'    => 'A(z) :attribute nagyobb kell, hogy legyen, mint :value kB.',
        'string'  => 'A(z) :attribute hosszabb kell legyen mint :value karakter.',
        'array'   => 'A(z) :attribute több, mint :value elemet kell, hogy tartalmazzon.',
    ],
    'gte'                  => [
        'numeric' => 'A(z) :attribute számnak :value értéknél nagyobbnak vagy vele egyenlőnek kell lennie.',
        'file'    => 'A(z) :attribute mérete nem lehet kevesebb, mint :value kB.',
        'string'  => 'A(z) :attribute szövegnek legalább :value karakter hosszúnak kell lennie.',
        'array'   => 'A(z) :attribute tömbnek :value vagy több elemmel kell rendelkeznie.',
    ],
    'exists'               => 'A kiválasztott :attribute érvénytelen.',
    'image'                => 'A(z) :attribute kép kell legyen.',
    'image_extension'      => 'A(z) :attribute kép kiterjesztése érvényes és támogatott kell legyen.',
    'in'                   => 'A kiválasztott :attribute érvénytelen.',
    'integer'              => 'A(z) :attribute egész szám kell legyen.',
    'ip'                   => 'A(z) :attribute érvényes IP cím kell legyen.',
    'ipv4'                 => 'A(z) :attribute érvényes IPv4 cím kell legyen.',
    'ipv6'                 => 'A(z) :attribute érvényes IPv6 cím kell legyen.',
    'json'                 => 'A(z) :attribute érvényes JSON szöveg kell legyen.',
    'lt'                   => [
        'numeric' => 'A(z) :attribute szám kisebb kell, hogy legyen, mint :value.',
        'file'    => 'A(z) :attribute fájlnak kisebbnek kell lennie, mint :value kB.',
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
        'file'    => 'A(z) :attribute nem lehet nagyobb, mint :max kB.',
        'string'  => 'A(z) :attribute nem lehet hosszabb, mint :max karakter.',
        'array'   => 'A(z) :attribute nem tartalmazhat több, mint :max elemet.',
    ],
    'mimes'                => 'A(z) :attribute típusa csak :values lehet.',
    'min'                  => [
        'numeric' => 'A(z) :attribute legalább :min kell legyen.',
        'file'    => 'A(z) :attribute legalább :min kB kell legyen.',
        'string'  => 'A(z) :attribute legalább :min karakter kell legyen.',
        'array'   => 'A(z) :attribute legalább :min elemet kell tartalmazzon.',
    ],
    'not_in'               => 'A kiválasztott :attribute érvénytelen.',
    'not_regex'            => 'A(z) :attribute formátuma érvénytelen.',
    'numeric'              => 'A(z) :attribute szám kell legyen.',
    'regex'                => 'A(z) :attribute formátuma érvénytelen.',
    'required'             => 'A(z) :attribute mező kötelező.',
    'required_if'          => 'A(z) :attribute mező kötelező ha :other értéke :value.',
    'required_with'        => 'A(z) :attribute mező kötelező ha :values be van állítva.',
    'required_with_all'    => 'A(z) :attribute mező kötelező ha :values be van állítva.',
    'required_without'     => 'A(z) :attribute mező kötelező ha :values nincs beállítva.',
    'required_without_all' => 'A(z) :attribute mező kötelező ha egyik :values sincs beállítva.',
    'same'                 => 'A(z) :attribute és :other értékének egyeznie kell.',
    'safe_url'             => 'Előfordulhat, hogy a megadott link nem biztonságos.',
    'size'                 => [
        'numeric' => 'A(z) :attribute :size méretű kell legyen.',
        'file'    => 'A(z) :attribute :size kB méretű kell legyen.',
        'string'  => 'A(z) :attribute :size karakter kell legyen.',
        'array'   => 'A(z) :attribute :size elemet kell tartalmazzon.',
    ],
    'string'               => 'A(z) :attribute szöveg kell legyen.',
    'timezone'             => 'A(z) :attribute érvényes időzóna kell legyen.',
    'totp'                 => 'A megadott kód érvénytelen vagy lejárt.',
    'unique'               => 'A(z) :attribute már foglalt.',
    'url'                  => 'A(z) :attribute formátuma érvénytelen.',
    'uploaded'             => 'A fájlt nem lehet feltölteni. A szerver nem fogad el ilyen méretű fájlokat.',

    'zip_file' => 'A(z) :attribute egy a ZIP fájlban található fájlra kell, hogy hivatkozzon',
    'zip_file_size' => 'A(z) :attribute fájl nem haladhatja meg a :size MB méretet.',
    'zip_file_mime' => 'A(z) :attribute egy :validTypes típusú fájlra kell, hogy hivatkozzon, a :foundType helyett.',
    'zip_model_expected' => 'Adat objektum helyett ":type" lett találva.',
    'zip_unique' => 'A(z) :attribute egyedi kell hogy legyen a ZIP fájlban az adott objektum típushoz.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Jelszó megerősítés szükséges',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
