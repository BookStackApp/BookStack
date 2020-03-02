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
        'numeric' => ':attribute skal være større end eller lig med :value.',
        'file'    => ':attribute skal være større end eller lig med :value kilobytes.',
        'string'  => ':attribute skal indeholde flere end eller lig med :value tegn.',
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
        'string'  => 'The :attribute must be less than or equal :value characters.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
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
