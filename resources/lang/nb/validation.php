<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute må aksepteres.',
    'active_url'           => ':attribute er ikke en godkjent URL.',
    'after'                => ':attribute må være en dato etter :date.',
    'alpha'                => ':attribute kan kun inneholde bokstaver.',
    'alpha_dash'           => ':attribute kan kunne inneholde bokstaver, tall, bindestreker eller understreker.',
    'alpha_num'            => ':attribute kan kun inneholde bokstaver og tall.',
    'array'                => ':attribute må være en liste.',
    'before'               => ':attribute må være en dato før :date.',
    'between'              => [
        'numeric' => ':attribute må være mellom :min og :max.',
        'file'    => ':attribute må være mellom :min og :max kilobytes.',
        'string'  => ':attribute må være mellom :min og :max tegn.',
        'array'   => ':attribute må være mellom :min og :max ting.',
    ],
    'boolean'              => ':attribute feltet kan bare være sann eller falsk.',
    'confirmed'            => ':attribute bekreftelsen samsvarer ikke.',
    'date'                 => ':attribute er ikke en gyldig dato.',
    'date_format'          => ':attribute samsvarer ikke med :format.',
    'different'            => ':attribute og :other må være forskjellige.',
    'digits'               => ':attribute må være :digits tall.',
    'digits_between'       => ':attribute må være mellomg :min og :max tall.',
    'email'                => ':attribute må være en gyldig e-post.',
    'ends_with' => ':attribute må slutte med en av verdiene: :values',
    'filled'               => ':attribute feltet er påkrevd.',
    'gt'                   => [
        'numeric' => ':attribute må være større enn :value.',
        'file'    => ':attribute må være større enn :value kilobytes.',
        'string'  => ':attribute må være større enn :value tegn.',
        'array'   => ':attribute må ha mer en :value ting.',
    ],
    'gte'                  => [
        'numeric' => ':attribute må være større enn eller lik :value.',
        'file'    => ':attribute må være større enn eller lik :value kilobytes.',
        'string'  => ':attribute må være større enn eller lik :value tegn.',
        'array'   => ':attribute må ha :value eller flere ting.',
    ],
    'exists'               => 'Den valgte :attribute er ugyldig.',
    'image'                => ':attribute må være et bilde.',
    'image_extension'      => ':attribute må ha støttet formattype.',
    'in'                   => 'Den valgte :attribute er ugyldig.',
    'integer'              => ':attribute må være et heltall',
    'ip'                   => ':attribute må være en gyldig IP adresse.',
    'ipv4'                 => ':attribute må være en gyldig IPv4 adresse.',
    'ipv6'                 => ':attribute må være en gyldig IPv6 adresse.',
    'json'                 => ':attribute må være en gyldig JSON tekststreng.',
    'lt'                   => [
        'numeric' => ':attribute må være mindre enn :value.',
        'file'    => ':attribute må være mindre enn :value kilobytes.',
        'string'  => ':attribute må være mindre enn :value tegn.',
        'array'   => ':attribute må ha mindre enn :value ting.',
    ],
    'lte'                  => [
        'numeric' => ':attribute må være mindre enn eller lik :value.',
        'file'    => ':attribute må være mindre enn eller lik :value kilobytes.',
        'string'  => ':attribute må være mindre enn eller lik :value characters.',
        'array'   => ':attribute må ha mindre enn eller lik :value ting.',
    ],
    'max'                  => [
        'numeric' => ':attribute kan ikke være større enn :max.',
        'file'    => ':attribute kan ikke være større enn :max kilobytes.',
        'string'  => ':attribute kan ikke være større enn :max tegn.',
        'array'   => ':attribute kan ikke inneholde mer enn :max ting.',
    ],
    'mimes'                => ':attribute må være en fil av typen: :values.',
    'min'                  => [
        'numeric' => ':attribute må være på minst :min.',
        'file'    => ':attribute må være på minst :min kilobytes.',
        'string'  => ':attribute må være på minst :min tegn.',
        'array'   => ':attribute må minst ha :min ting.',
    ],
    'not_in'               => 'Den valgte :attribute er ugyldig.',
    'not_regex'            => ':attribute format er ugyldig.',
    'numeric'              => ':attribute må være et nummer.',
    'regex'                => ':attribute format er ugyldig.',
    'required'             => ':attribute feltet er påkrevt.',
    'required_if'          => ':attribute feltet er påkrevt når :other er :value.',
    'required_with'        => ':attribute feltet er påkrevt når :values er tilgjengelig.',
    'required_with_all'    => ':attribute feltet er påkrevt når :values er tilgjengelig',
    'required_without'     => ':attribute feltet er påkrevt når :values ikke er tilgjengelig.',
    'required_without_all' => ':attribute feltet er påkrevt når ingen av :values er tilgjengelig.',
    'same'                 => ':attribute og :other må samsvare.',
    'safe_url'             => 'The provided link may not be safe.',
    'size'                 => [
        'numeric' => ':attribute må være :size.',
        'file'    => ':attribute må være :size kilobytes.',
        'string'  => ':attribute må være :size tegn.',
        'array'   => ':attribute må inneholde :size ting.',
    ],
    'string'               => ':attribute må være en tekststreng.',
    'timezone'             => ':attribute må være en tidssone.',
    'unique'               => ':attribute har allerede blitt tatt.',
    'url'                  => ':attribute format er ugyldig.',
    'uploaded'             => 'kunne ikke lastes opp, tjeneren støtter ikke filer av denne størrelsen.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'passordbekreftelse er påkrevd',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
