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
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'no_double_extension'  => 'The :attribute must only have a single file extension.',
    'not_in'               => 'The selected :attribute is invalid.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'safe_url'             => 'The provided link may not be safe.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'The :attribute has already been taken.',
    'url'                  => 'The :attribute format is invalid.',
    'uploaded'             => 'The file could not be uploaded. The server may not accept files of this size.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Password confirmation required',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
