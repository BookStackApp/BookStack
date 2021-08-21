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
    'active_url'           => ':attribute nije ispravan URL.',
    'after'                => ':attribute mora biti datum nakon :date.',
    'alpha'                => ':attribute može sadržavati samo slova.',
    'alpha_dash'           => ':attribute može sadržavati samo slova, brojeve, crtice i donje crtice.',
    'alpha_num'            => ':attribute može sadržavati samo slova i brojeve.',
    'array'                => ':attribute mora biti niz.',
    'backup_codes'         => 'The provided code is not valid or has already been used.',
    'before'               => ':attribute mora biti datum prije :date.',
    'between'              => [
        'numeric' => ':attribute mora biti između :min i :max.',
        'file'    => ':attribute mora biti između :min i :max kilobajta.',
        'string'  => ':attribute mora biti između :min i :max karaktera.',
        'array'   => ':attribute mora imati između :min i :max stavki.',
    ],
    'boolean'              => ':attribute polje mora biti tačno ili netačno.',
    'confirmed'            => ':attribute potvrda se ne slaže.',
    'date'                 => ':attribute nije ispravan datum.',
    'date_format'          => ':attribute ne odgovara formatu :format.',
    'different'            => ':attribute i :other moraju biti različiti.',
    'digits'               => ':attribute mora imati :digits brojeve.',
    'digits_between'       => ':attribute mora imati između :min i :max brojeva.',
    'email'                => ':attribute mora biti ispravna e-mail adresa.',
    'ends_with' => ':attribute mora završavati sa jednom od sljedećih: :values',
    'filled'               => 'Polje :attribute je obavezno.',
    'gt'                   => [
        'numeric' => ':attribute mora biti veći od :value.',
        'file'    => ':attribute mota biti veći od :value kilobajta.',
        'string'  => ':attribute mora imati više od :value karaktera.',
        'array'   => ':attribute mora imati više od :value stavki.',
    ],
    'gte'                  => [
        'numeric' => ':attribute mora biti veći od ili jednak :value.',
        'file'    => ':attribute mora imati više od ili jednako :value kilobajta.',
        'string'  => ':attribute mora imati više od ili jednako :value karaktera.',
        'array'   => ':attribute mora imati :value stavki ili više.',
    ],
    'exists'               => 'Odabrani :attribute je neispravan.',
    'image'                => ':attribute mora biti slika.',
    'image_extension'      => ':attribute mora imati ispravnu i podržanu ekstenziju slike.',
    'in'                   => 'Odabrani :attribute je neispravan.',
    'integer'              => ':attribute mora biti integer.',
    'ip'                   => ':attribute mora biti ispravna IP adresa.',
    'ipv4'                 => ':attribute mora biti ispravna IPv4 adresa.',
    'ipv6'                 => ':attribute mora biti ispravna IPv6 adresa.',
    'json'                 => ':attribute mora biti ispravan JSON string.',
    'lt'                   => [
        'numeric' => ':attribute mora biti manji od :value.',
        'file'    => ':attribute mora imati manje od :value kilobajta.',
        'string'  => ':attribute mora imati manje od :value karaktera.',
        'array'   => ':attribute mora imati manje od :value stavki.',
    ],
    'lte'                  => [
        'numeric' => ':attribute mora imati vrijednost manju od ili jednaku :value.',
        'file'    => ':attribute mora imati manje od ili jednako :value kilobajta.',
        'string'  => ':attribute mora imati manje od ili jednako :value karaktera.',
        'array'   => ':attribute ne smije imati više od :value stavki.',
    ],
    'max'                  => [
        'numeric' => ':attribute ne može biti veći od :max.',
        'file'    => ':attribute ne može imati više od :max kilobajta.',
        'string'  => ':attribute ne može imati više od :max karaktera.',
        'array'   => ':attribute ne može imati više od :max stavki.',
    ],
    'mimes'                => ':attribute mora biti fajl vrste: values.',
    'min'                  => [
        'numeric' => ':attribute mora biti najmanje :min.',
        'file'    => ':attribute mora imati najmanje :min kilobajta.',
        'string'  => ':attribute mora imati najmanje :min karaktera.',
        'array'   => ':attribute mora imati najmanje :min stavki.',
    ],
    'not_in'               => 'Odabrani :attribute je neispravan.',
    'not_regex'            => 'Format :attribute je neispravan.',
    'numeric'              => ':attribute mora biti broj.',
    'regex'                => 'Format :attribute je neispravan.',
    'required'             => 'Polje :attribute je obavezno.',
    'required_if'          => 'Polje :attribute je obavezno kada :other ima vrijednost :value.',
    'required_with'        => 'Polje :attribute je obavezno kada su prisutne :values.',
    'required_with_all'    => 'Polje :attribute je obavezno kada su prisutne :values.',
    'required_without'     => 'Polje :attribute je obavezno kada :values nisu prisutne.',
    'required_without_all' => 'Polje :attribute je obavezno kada nijedno od :values nije prisutno.',
    'same'                 => ':attribute i :other se moraju poklapati.',
    'safe_url'             => 'Navedeni link možda nije siguran.',
    'size'                 => [
        'numeric' => ':attribute mora biti :size.',
        'file'    => ':attribute mora imati :size kilobajta.',
        'string'  => ':attribute mora imati :size karaktera.',
        'array'   => ':attribute mora sadržavati :size stavki.',
    ],
    'string'               => ':attribute mora biti string.',
    'timezone'             => ':attribute mora biti ispravna zona.',
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => ':attribute je zauzet.',
    'url'                  => 'Format :attribute je neispravan.',
    'uploaded'             => 'Fajl nije učitan. Server ne prihvata fajlove ove veličine.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Zahtijeva se potvrda lozinke',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
