<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute skal være accepteret.',
    'active_url'           => ':attribute er ikke en gyldig URL.',
    'after'                => ':attribute skal være en dato efter :date.',
    'alpha'                => ':attribute må kun indeholde bogstaver.',
    'alpha_dash'           => ':attribute må kun bestå af bogstaver, tal, binde- og under-streger.',
    'alpha_num'            => ':attribute må kun indeholde bogstaver og tal.',
    'array'                => ':attribute skal være et array.',
    'backup_codes'         => 'Den angivne kode er ikke gyldig eller er allerede brugt.',
    'before'               => ':attribute skal være en dato før :date.',
    'between'              => [
        'numeric' => ':attribute skal være mellem :min og :max.',
        'file'    => ':attribute skal være mellem :min og :max kilobytes.',
        'string'  => ':attribute skal være mellem :min og :max tegn.',
        'array'   => ':attribute skal have mellem :min og :max elementer.',
    ],
    'boolean'              => ':attribute-feltet skal være enten sandt eller falsk.',
    'confirmed'            => ':attribute-bekræftelsen matcher ikke.',
    'date'                 => ':attribute er ikke en gyldig dato.',
    'date_format'          => ':attribute matcher ikke formatet :format.',
    'different'            => ':attribute og :other skal være forskellige.',
    'digits'               => ':attribute skal være :digits cifre.',
    'digits_between'       => ':attribute skal være mellem :min og :max cifre.',
    'email'                => ':attribute skal være en gyldig mail-adresse.',
    'ends_with' => ':attribute skal slutte på en af følgende værdier: :values',
    'filled'               => ':attribute er obligatorisk.',
    'gt'                   => [
        'numeric' => ':attribute skal være større end :value.',
        'file'    => ':attribute skal være større end :value kilobytes.',
        'string'  => ':attribute skal have mere end :value tegn.',
        'array'   => ':attribute skal indeholde mere end :value elementer.',
    ],
    'gte'                  => [
        'numeric' => ':attribute skal mindst være :value.',
        'file'    => ':attribute skal være mindst :value kilobytes.',
        'string'  => ':attribute skal indeholde mindst :value tegn.',
        'array'   => ':attribute skal have :value elementer eller flere.',
    ],
    'exists'               => 'Den valgte :attribute er ikke gyldig.',
    'image'                => ':attribute skal være et billede.',
    'image_extension'      => ':attribute skal være et gyldigt og understøttet billedformat.',
    'in'                   => 'Den valgte :attribute er ikke gyldig.',
    'integer'              => ':attribute skal være et heltal.',
    'ip'                   => ':attribute skal være en gyldig IP-adresse.',
    'ipv4'                 => ':attribute skal være en gyldig IPv4-adresse.',
    'ipv6'                 => ':attribute skal være en gyldig IPv6-adresse.',
    'json'                 => ':attribute skal være en gyldig JSON-streng.',
    'lt'                   => [
        'numeric' => ':attribute skal være mindre end :value.',
        'file'    => ':attribute skal være mindre end :value kilobytes.',
        'string'  => ':attribute skal have mindre end :value tegn.',
        'array'   => ':attribute skal indeholde mindre end :value elementer.',
    ],
    'lte'                  => [
        'numeric' => ':attribute skal være mindre end eller lig med :value.',
        'file'    => 'The :attribute skal være mindre eller lig med :value kilobytes.',
        'string'  => ':attribute skal maks være :value tegn.',
        'array'   => ':attribute må ikke indeholde mere end :value elementer.',
    ],
    'max'                  => [
        'numeric' => ':attribute må ikke overstige :max.',
        'file'    => ':attribute må ikke overstige :max kilobytes.',
        'string'  => ':attribute må ikke overstige :max. tegn.',
        'array'   => ':attribute må ikke have mere end :max elementer.',
    ],
    'mimes'                => ':attribute skal være en fil af typen: :values.',
    'min'                  => [
        'numeric' => ':attribute skal mindst være :min.',
        'file'    => ':attribute skal være mindst :min kilobytes.',
        'string'  => ':attribute skal mindst være :min tegn.',
        'array'   => ':attribute skal have mindst :min elementer.',
    ],
    'not_in'               => 'Den valgte :attribute er ikke gyldig.',
    'not_regex'            => ':attribute-formatet er ugyldigt.',
    'numeric'              => ':attribute skal være et tal.',
    'regex'                => ':attribute-formatet er ugyldigt.',
    'required'             => ':attribute er obligatorisk.',
    'required_if'          => ':attribute skal udfyldes når :other er :value.',
    'required_with'        => ':attribute skal udfyldes når :values er udfyldt.',
    'required_with_all'    => ':attribute skal udfyldes når :values er udfyldt.',
    'required_without'     => ':attribute skal udfyldes når :values ikke er udfyldt.',
    'required_without_all' => ':attribute skal udfyldes når ingen af :values er udfyldt.',
    'same'                 => ':attribute og :other skal være ens.',
    'safe_url'             => 'Det angivne link kan være usikkert.',
    'size'                 => [
        'numeric' => ':attribute skal være :size.',
        'file'    => ':attribute skal være :size kilobytes.',
        'string'  => ':attribute skal være :size tegn.',
        'array'   => ':attribute skal indeholde :size elementer.',
    ],
    'string'               => ':attribute skal være tekst.',
    'timezone'             => ':attribute skal være en gyldig zone.',
    'totp'                 => 'Den angivne kode er ikke gyldig eller er udløbet.',
    'unique'               => ':attribute er allerede i brug.',
    'url'                  => ':attribute-formatet er ugyldigt.',
    'uploaded'             => 'Filen kunne ikke oploades. Serveren accepterer muligvis ikke filer af denne størrelse.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Adgangskodebekræftelse påkrævet',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
