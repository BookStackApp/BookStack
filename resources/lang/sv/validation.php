<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute måste godkännas.',
    'active_url'           => ':attribute är inte en giltig URL.',
    'after'                => ':attribute måste vara efter :date.',
    'alpha'                => ':attribute får bara innehålla bokstäver.',
    'alpha_dash'           => ':attribute får bara innehålla bokstäver, siffror och bindestreck.',
    'alpha_num'            => ':attribute får bara innehålla bokstäver och siffror.',
    'array'                => ':attribute måste vara en array.',
    'before'               => ':attribute måste vara före :date.',
    'between'              => [
        'numeric' => ':attribute måste vara mellan :min och :max.',
        'file'    => ':attribute måste vara mellan :min och :max kilobyte stor.',
        'string'  => ':attribute måste vara mellan :min och :max tecken.',
        'array'   => ':attribute måste innehålla mellan :min och :max poster.',
    ],
    'boolean'              => ':attribute måste vara sant eller falskt.',
    'confirmed'            => 'Bekräftelsen av :attribute stämmer inte.',
    'date'                 => ':attribute är inte ett giltigt datum.',
    'date_format'          => ':attribute matchar inte formatet :format.',
    'different'            => ':attribute och :other måste vara olika.',
    'digits'               => ':attribute måste vara :digits siffror.',
    'digits_between'       => ':attribute måste vara mellan :min och :max siffror.',
    'email'                => ':attribute måste vara en giltig e-postadress.',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'filled'               => ':attribute är obligatoriskt.',
    'gt'                   => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'string'  => 'The :attribute must be greater than :value characters.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'string'  => 'The :attribute must be greater than or equal :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'exists'               => 'Valt värde för :attribute är ogiltigt.',
    'image'                => ':attribute måste vara en bild.',
    'image_extension'      => ':attribute måste ha ett giltigt filtillägg.',
    'in'                   => 'Vald :attribute är ogiltigt.',
    'integer'              => ':attribute måste vara en integer.',
    'ip'                   => ':attribute måste vara en giltig IP-adress.',
    'ipv4'                 => 'The :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'The :attribute must be a valid IPv6 address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'lt'                   => [
        'numeric' => 'The :attribute must be less than :value.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'string'  => 'The :attribute must be less than :value characters.',
        'array'   => 'The :attribute must have less than :value items.',
    ],
    'lte'                  => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'string'  => 'The :attribute must be less than or equal :value characters.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max'                  => [
        'numeric' => ':attribute får inte vara större än :max.',
        'file'    => ':attribute får inte vara större än :max kilobyte.',
        'string'  => ':attribute får inte vara längre än :max tecken.',
        'array'   => ':attribute får inte ha fler än :max poster.',
    ],
    'mimes'                => ':attribute måste vara en fil av typen: :values.',
    'min'                  => [
        'numeric' => ':attribute måste vara minst :min.',
        'file'    => ':attribute måste vara minst :min kilobyte stor.',
        'string'  => ':attribute måste vara minst :min tecken.',
        'array'   => ':attribute måste ha minst :min poster.',
    ],
    'no_double_extension'  => ':attribute får bara ha ett filtillägg.',
    'not_in'               => 'Vald :attribute är inte giltig',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => ':attribute måste vara ett nummer.',
    'regex'                => ':attribute har ett ogiltigt format.',
    'required'             => ':attribute är obligatoriskt.',
    'required_if'          => ':attribute är obligatoriskt när :other är :value.',
    'required_with'        => ':attribute är obligatoriskt när :values finns.',
    'required_with_all'    => ':attribute är obligatoriskt när :values finns.',
    'required_without'     => ':attribute är obligatoriskt när :values inte finns.',
    'required_without_all' => ':attribute är obligatirskt när ingen av :values finns.',
    'same'                 => ':attribute och :other måste stämma överens.',
    'size'                 => [
        'numeric' => ':attribute måste vara :size.',
        'file'    => ':attribute måste vara :size kilobyte.',
        'string'  => ':attribute måste vara :size tecken.',
        'array'   => ':attribute måste innehålla :size poster.',
    ],
    'string'               => ':attribute måste vara en sträng.',
    'timezone'             => ':attribute måste vara en giltig tidszon.',
    'unique'               => ':attribute är upptaget',
    'url'                  => 'Formatet på :attribute är ogiltigt.',
    'uploaded'             => 'Filen kunde inte laddas upp. Servern kanske inte tillåter filer med denna storlek.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Lösenordet måste bekräftas',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
