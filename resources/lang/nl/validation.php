<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute moet geaccepteerd worden.',
    'active_url'           => ':attribute is geen geldige URL.',
    'after'                => ':attribute moet een datum zijn later dan :date.',
    'alpha'                => ':attribute mag alleen letters bevatten.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => ':attribute mag alleen letters en nummers bevatten.',
    'array'                => ':attribute moet een reeks zijn.',
    'before'               => ':attribute moet een datum zijn voor :date.',
    'between'              => [
        'numeric' => ':attribute moet tussen de :min en :max zijn.',
        'file'    => ':attribute moet tussen de :min en :max kilobytes zijn.',
        'string'  => ':attribute moet tussen de :min en :max karakters zijn.',
        'array'   => ':attribute moet tussen de :min en :max items bevatten.',
    ],
    'boolean'              => ':attribute moet ja of nee zijn.',
    'confirmed'            => ':attribute bevestiging komt niet overeen.',
    'date'                 => ':attribute is geen geldige datum.',
    'date_format'          => ':attribute komt niet overeen met het formaat :format.',
    'different'            => ':attribute en :other moeten verschillend zijn.',
    'digits'               => ':attribute moet bestaan uit :digits cijfers.',
    'digits_between'       => ':attribute moet tussen de :min en :max cijfers zijn.',
    'email'                => ':attribute is geen geldig e-mailadres.',
    'ends_with' => ':attribute moet eindigen met een van de volgende: :values',
    'filled'               => ':attribute is verplicht.',
    'gt'                   => [
        'numeric' => ':attribute moet groter zijn dan :value.',
        'file'    => ':attribute moet groter zijn dan :value kilobytes.',
        'string'  => ':attribute moet meer dan :value karakters bevatten.',
        'array'   => ':attribute moet meer dan :value items bevatten.',
    ],
    'gte'                  => [
        'numeric' => ':attribute moet groter of gelijk zijn aan :value.',
        'file'    => ':attribute moet groter of gelijk zijn aan :value kilobytes.',
        'string'  => ':attribute moet :value of meer karakters bevatten.',
        'array'   => ':attribute moet :value items of meer bevatten.',
    ],
    'exists'               => ':attribute is ongeldig.',
    'image'                => ':attribute moet een afbeelding zijn.',
    'image_extension'      => ':attribute moet een geldige en ondersteunde afbeeldings-extensie hebben.',
    'in'                   => ':attribute is ongeldig.',
    'integer'              => ':attribute moet een getal zijn.',
    'ip'                   => ':attribute moet een geldig IP-adres zijn.',
    'ipv4'                 => ':attribute moet een geldig IPv4-adres zijn.',
    'ipv6'                 => ':attribute moet een geldig IPv6-adres zijn.',
    'json'                 => ':attribute moet een geldige JSON-string zijn.',
    'lt'                   => [
        'numeric' => ':attribute moet kleiner zijn dan :value.',
        'file'    => ':attribute moet kleiner zijn dan :value kilobytes.',
        'string'  => ':attribute moet minder dan :value karakters bevatten.',
        'array'   => ':attribute moet minder dan :value items bevatten.',
    ],
    'lte'                  => [
        'numeric' => ':attribute moet kleiner of gelijk zijn aan :value.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
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
