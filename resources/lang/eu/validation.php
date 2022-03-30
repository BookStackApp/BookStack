<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute onartua izan behar du.',
    'active_url'           => ':attribute ez da baliozko URLa.',
    'after'                => ':attribute :date baino zaharragoa izan behar da.',
    'alpha'                => ':attribute eremuak hizkiak solik izan ditzake.',
    'alpha_dash'           => ':attribute eremuak letrak, zenbakiak, laburpenak eta azpizenbakiak bakarrik eduki ditzake.',
    'alpha_num'            => ':attribute eremuak hizki eta zenbakiak solik izan ditzake.',
    'array'                => ':attribute array bat izan behar da.',
    'backup_codes'         => 'Kode hau ez da baliagarria edo iada erabilia izan da.',
    'before'               => ':attribute :date baino berriagoa izan behar da.',
    'between'              => [
        'numeric' => ':min eta :max bitartean egon behar da :attribute.',
        'file'    => ':min eta :max kilobytes tartean egon behar da :attribute.',
        'string'  => ':min eta :max karaktere tartean egon behar da :attribute.',
        'array'   => ':min eta :max item tartean egon behar da :attribute.',
    ],
    'boolean'              => ':attribute true edo false izan behar da.',
    'confirmed'            => ':attribute berrezpena ez da aurkitu.',
    'date'                 => ':attribute ez da baliozko data.',
    'date_format'          => ':attribute ez da :format formatuan aurkitu.',
    'different'            => ':attribute eta :other ezberdinak izan behar dira.',
    'digits'               => ':attribute :digits digitu behar ditu.',
    'digits_between'       => ':min eta :max digitu tartean egon behar da :attribute.',
    'email'                => ':attribute baliozko email helbide bat izan behar da.',
    'ends_with' => ':attribute ondorengo balio hauetako batekin bukatu behar da :values',
    'file'                 => 'The :attribute must be provided as a valid file.',
    'filled'               => ':attribute eremua beharrezkoa da.',
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
    'image'                => ':attribute irudi bat izan behar da.',
    'image_extension'      => 'The :attribute must have a valid & supported image extension.',
    'in'                   => 'The selected :attribute is invalid.',
    'integer'              => ':attribute zenbaki oso bat izan behar da.',
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
    'not_regex'            => ':attribute formatua baliogabea da.',
    'numeric'              => ':attribute zenbaki bat izan behar da.',
    'regex'                => ':attribute formatua baliogabea da.',
    'required'             => ':attribute eremua beharrezkoa da.',
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
    'string'               => ':attribute textua izan behar da.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => 'The :attribute has already been taken.',
    'url'                  => 'The :attribute format is invalid.',
    'uploaded'             => 'The file could not be uploaded. The server may not accept files of this size.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Baieztatu zure pasahitza',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
