<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute deve essere accettato.',
    'active_url'           => ':attribute non è uno URL valido.',
    'after'                => ':attribute deve essere una data dopo il :date.',
    'alpha'                => ':attribute deve contenere solo lettere.',
    'alpha_dash'           => ':attribute deve contenere solo lettere, numeri e meno.',
    'alpha_num'            => ':attribute deve contenere solo lettere e numeri.',
    'array'                => ':attribute deve essere un array.',
    'before'               => ':attribute deve essere una data prima del :date.',
    'between'              => [
        'numeric' => 'Il campo :attribute deve essere tra :min e :max.',
        'file'    => 'Il campo :attribute deve essere tra :min e :max kilobytes.',
        'string'  => 'Il campo :attribute deve essere tra :min e :max caratteri.',
        'array'   => 'Il campo :attribute deve essere tra :min e :max oggetti.',
    ],
    'boolean'              => ':attribute deve contenere vero o falso.',
    'confirmed'            => 'La conferma di :attribute non corrisponde.',
    'date'                 => ':attribute non è una data valida.',
    'date_format'          => 'Il campo :attribute non corrisponde al formato :format.',
    'different'            => 'Il campo :attribute e :other devono essere differenti.',
    'digits'               => 'Il campo :attribute deve essere di :digits numeri.',
    'digits_between'       => 'Il campo :attribute deve essere tra i numeri :min e :max.',
    'email'                => 'Il campo :attribute deve essere un indirizzo email valido.',
    'ends_with' => '',
    'filled'               => 'Il campo :attribute field is required.',
    'gt'                   => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'gte'                  => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'exists'               => 'Il campo :attribute non è valido.',
    'image'                => 'Il campo :attribute deve essere un\'immagine.',
    'image_extension'      => '',
    'in'                   => 'Il campo :attribute selezionato non è valido.',
    'integer'              => 'Il campo :attribute deve essere un intero.',
    'ip'                   => 'Il campo :attribute deve essere un indirizzo IP valido.',
    'ipv4'                 => '',
    'ipv6'                 => '',
    'json'                 => '',
    'lt'                   => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'lte'                  => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'max'                  => [
        'numeric' => 'Il campo :attribute non deve essere maggiore di :max.',
        'file'    => 'Il campo :attribute non deve essere maggiore di :max kilobytes.',
        'string'  => 'Il campo :attribute non deve essere maggiore di :max caratteri.',
        'array'   => 'Il campo :attribute non deve avere più di :max oggetti.',
    ],
    'mimes'                => 'Il campo :attribute deve essere: :values.',
    'min'                  => [
        'numeric' => 'Il campo :attribute deve essere almeno :min.',
        'file'    => 'Il campo :attribute deve essere almeno :min kilobytes.',
        'string'  => 'Il campo :attribute deve essere almeno :min caratteri.',
        'array'   => 'Il campo :attribute deve contenere almeno :min elementi.',
    ],
    'no_double_extension'  => '',
    'not_in'               => 'Il :attribute selezionato non è valido.',
    'not_regex'            => '',
    'numeric'              => ':attribute deve essere un numero.',
    'regex'                => 'Il formato di :attribute non è valido.',
    'required'             => 'Il campo :attribute è richiesto.',
    'required_if'          => 'Il campo :attribute è richiesto quando :other è :value.',
    'required_with'        => 'Il campo :attribute è richiesto quando :values è presente.',
    'required_with_all'    => 'Il campo :attribute è richiesto quando :values sono presenti.',
    'required_without'     => 'Il campo :attribute è richiesto quando :values non è presente.',
    'required_without_all' => 'Il campo :attribute è richiesto quando nessuno dei :values sono presenti.',
    'same'                 => ':attribute e :other devono corrispondere.',
    'size'                 => [
        'numeric' => 'Il campo :attribute deve essere :size.',
        'file'    => 'Il campo :attribute deve essere :size kilobytes.',
        'string'  => 'Il campo :attribute deve essere di :size caratteri.',
        'array'   => 'Il campo :attribute deve contenere :size elementi.',
    ],
    'string'               => ':attribute deve essere una stringa.',
    'timezone'             => ':attribute deve essere una zona valida.',
    'unique'               => ':attribute è già preso.',
    'url'                  => 'Il formato :attribute non è valido.',
    'uploaded'             => '',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Conferma della password richiesta',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
