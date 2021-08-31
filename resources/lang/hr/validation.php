<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute mora biti prihvaćen.',
    'active_url'           => ':attribute nema valjan URL.',
    'after'                => ':attribute mora biti nakon :date.',
    'alpha'                => ':attribute može sadržavati samo slova.',
    'alpha_dash'           => ':attribute  može sadržavati samo slova, brojeve, crtice i donje crtice.',
    'alpha_num'            => ':attribute može sadržavati samo slova i brojeve.',
    'array'                => ':attribute mora biti niz.',
    'backup_codes'         => 'The provided code is not valid or has already been used.',
    'before'               => ':attribute mora biti prije :date.',
    'between'              => [
        'numeric' => ':attribute mora biti između :min i :max.',
        'file'    => ':attribute mora biti između :min i :max kilobajta.',
        'string'  => ':attribute mora biti između :min i :max znakova.',
        'array'   => ':attribute mora biti između :min i :max stavki',
    ],
    'boolean'              => ':attribute mora biti točno ili netočno.',
    'confirmed'            => ':attribute potvrde se ne podudaraju.',
    'date'                 => ':attribute nema valjani datum.',
    'date_format'          => ':attribute ne odgovara formatu :format.',
    'different'            => ':attribute i :other se moraju razlikovati.',
    'digits'               => ':attribute mora biti :digits znakova.',
    'digits_between'       => ':attribute mora biti između :min i :max znamenki.',
    'email'                => ':attribute mora biti valjana email adresa.',
    'ends_with' => ':attribute mora završiti s :values',
    'filled'               => ':attribute polje je obavezno.',
    'gt'                   => [
        'numeric' => ':attribute mora biti veći od :value.',
        'file'    => ':attribute mora biti veći od :value  kilobajta.',
        'string'  => ':attribute mora biti veći od :value znakova',
        'array'   => ':attribute mora biti veći od :value stavki.',
    ],
    'gte'                  => [
        'numeric' => ':attribute mora biti veći ili jednak :value.',
        'file'    => ':attribute mora biti veći ili jednak :value kilobajta.',
        'string'  => ':attribute mora biti veći ili jednak :value znakova.',
        'array'   => ':attribute mora imati :value stavki ili više.',
    ],
    'exists'               => 'Odabrani :attribute ne vrijedi.',
    'image'                => ':attribute mora biti slika.',
    'image_extension'      => ':attribute mora imati valjanu i podržanu ekstenziju.',
    'in'                   => 'Odabrani :attribute ne vrijedi.',
    'integer'              => ':attribute mora biti cijeli broj.',
    'ip'                   => ':attribute mora biti valjana IP adresa.',
    'ipv4'                 => ':attribute mora biti valjana IPv4 adresa.',
    'ipv6'                 => ':attribute mora biti valjana IPv6 adresa.',
    'json'                 => ':attribute mora biti valjani JSON niz.',
    'lt'                   => [
        'numeric' => ':attribute mora biti manji od :value.',
        'file'    => ':attribute mora biti manji od :value kilobajta.',
        'string'  => ':attribute mora biti manji od :value znakova.',
        'array'   => ':attribute mora biti manji od :value stavki.',
    ],
    'lte'                  => [
        'numeric' => ':attribute mora biti manji ili jednak :value.',
        'file'    => ':attribute mora biti manji ili jednak :value kilobajta.',
        'string'  => ':attribute mora biti manji ili jednak :value znakova.',
        'array'   => ':attribute mora imati više od :value stavki.',
    ],
    'max'                  => [
        'numeric' => ':attribute ne smije biti veći od :max.',
        'file'    => ':attribute ne smije biti veći od :max kilobajta.',
        'string'  => ':attribute ne smije biti duži od :max znakova.',
        'array'   => ':attribute ne smije imati više od :max stavki.',
    ],
    'mimes'                => ':attribute mora biti datoteka tipa: :values.',
    'min'                  => [
        'numeric' => ':attribute mora biti najmanje :min.',
        'file'    => ':attribute mora imati najmanje :min kilobajta.',
        'string'  => ':attribute mora imati najmanje :min znakova.',
        'array'   => ':attribute mora imati najmanje :min stavki.',
    ],
    'not_in'               => 'Odabrani :attribute ne vrijedi.',
    'not_regex'            => 'Format :attribute nije valjan.',
    'numeric'              => ':attribute mora biti broj.',
    'regex'                => 'Format :attribute nije valjan.',
    'required'             => ':attribute polje je obavezno.',
    'required_if'          => 'Polje :attribute je obavezno kada :other je :value.',
    'required_with'        => 'Polje :attribute je potrebno kada :values je sadašnjost.',
    'required_with_all'    => 'Polje :attribute je potrebno kada :values je sadašnjost.',
    'required_without'     => 'Polje :attribute je potrebno kada :values nije sadašnjost.',
    'required_without_all' => 'Polje :attribute je potrebno kada ništa od :values nije sadašnjost.',
    'same'                 => ':attribute i :other se moraju podudarati.',
    'safe_url'             => 'Navedena veza možda nije sigurna.',
    'size'                 => [
        'numeric' => ':attribute mora biti :size.',
        'file'    => ':attribute mora biti :size kilobajta.',
        'string'  => ':attribute mora biti :size znakova.',
        'array'   => ':attribute mora sadržavati :size stavki.',
    ],
    'string'               => ':attribute mora biti niz.',
    'timezone'             => ':attribute mora biti valjan.',
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => ':attribute se već koristi.',
    'url'                  => 'Format :attribute nije valjan.',
    'uploaded'             => 'Datoteka se ne može prenijeti. Server možda ne prihvaća datoteke te veličine.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Potrebna potvrda lozinke',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
