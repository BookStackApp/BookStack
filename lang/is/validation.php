<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'Það verður að samþykkja :attribute.',
    'active_url'           => ':attribute Er ekki gilt vistfang.',
    'after'                => ':attribute verður að vera dagsetning eftir :date.',
    'alpha'                => ':attribute má eingöngu innihalda stafi.',
    'alpha_dash'           => ':attribute má eingöngu innihalda stafi, tölustafi, strik og undirstrik.',
    'alpha_num'            => ':attribute má eingöngu innihalda stafi og tölustafi.',
    'array'                => 'The :attribute must be an array.',
    'backup_codes'         => 'Kóðinn sem þú gafst upp er ekki gildur eða hefur þegar verið notaður.',
    'before'               => ':attribute verður að vera dagsetning á undan :date.',
    'between'              => [
        'numeric' => ':attribute verður að vera á milli :min og :max.',
        'file'    => ':attribute verður að vera á milli :min og :max kílóbæti.',
        'string'  => ':attribute verður að vera á milli :min og :max stafir.',
        'array'   => ':attribute verður að vera á milli :min og :max fjöldi.',
    ],
    'boolean'              => ':attribute gildið verður að vera true og false.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'date'                 => 'The :attribute is not a valid date.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'email'                => 'The :attribute must be a valid email address.',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'file'                 => 'The :attribute must be provided as a valid file.',
    'filled'               => 'The :attribute field is required.',
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
    'exists'               => 'The selected :attribute is invalid.',
    'image'                => 'The :attribute must be an image.',
    'image_extension'      => 'The :attribute must have a valid & supported image extension.',
    'in'                   => 'The selected :attribute is invalid.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
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
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => 'The :attribute has already been taken.',
    'url'                  => 'The :attribute format is invalid.',
    'uploaded'             => 'The file could not be uploaded. The server may not accept files of this size.',

    'zip_file' => 'The :attribute needs to reference a file within the ZIP.',
    'zip_file_mime' => 'The :attribute needs to reference a file of type :validTypes, found :foundType.',
    'zip_model_expected' => 'Data object expected but ":type" found.',
    'zip_unique' => 'The :attribute must be unique for the object type within the ZIP.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Staðfestingu á lykilorði er krafist',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
