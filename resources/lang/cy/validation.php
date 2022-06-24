<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'Rhaid derbyn y :attribute.',
    'active_url'           => 'Nid ywr :attribute yn URL dilys.',
    'after'                => 'Rhaid i\'r :attribute bod yn dyddiad ar ol :date.',
    'alpha'                => 'Rhaid ir :attribute cynnwys llythrennau yn unig.',
    'alpha_dash'           => 'Dim ond llythrennau, rhifau, llinellau toriad a thanlinellau y gall y :attribute gynnwys.',
    'alpha_num'            => 'Rhaid ir :attribute cynnwys llythrennau a rhifau yn unig.',
    'array'                => 'Rhaid i :attribute fod yn array.',
    'backup_codes'         => 'Nid yw\'r cod a ddarparwyd yn ddilys neu mae eisoes wedi\'i ddefnyddio.',
    'before'               => 'Rhaid i\'r :attribute bod yn dyddiad cyn :date.',
    'between'              => [
        'numeric' => 'Rhaid i\'r :attribute bod rhwng :min a :max.',
        'file'    => 'Rhaid i\'r :attribute bod rhwng :min a :max kilobytes.',
        'string'  => 'Rhaid i\'r :attribute bod rhwng :min a :max cymeriadau.',
        'array'   => 'Rhaid i\'r :attribute cael rhwng :min a :max o eitemau.',
    ],
    'boolean'              => 'Rhaid i :attribute fod yn wir neu ddim.',
    'confirmed'            => 'Dydi\'r cadarnhad :attribute ddim yn cydfynd.',
    'date'                 => 'Nid yw\'r :attribute yn dyddiad dilys.',
    'date_format'          => 'Nid yw\'r :attribute yn cydfynd ar format :format.',
    'different'            => 'Rhaid i :attribute a :other bod yn wahanol.',
    'digits'               => 'Rhai i\'r :attribute bod yn :digits o ddigidau.',
    'digits_between'       => 'Rhaid i\'r :attribute bod rhwng :min a :max o digidau.',
    'email'                => 'Rhaid i\'r :attribute bod yn cyfeiriad e-bost dilys.',
    'ends_with' => 'Rhaid i\'r :attribute orffen gydag un o\'r canlynol: :values',
    'file'                 => 'Rhaid darparu\'r :attribute fel ffeil ddilys.',
    'filled'               => 'Mae angen llenwi\'r maes :attribute.',
    'gt'                   => [
        'numeric' => 'Rhaid i\'r :attribute fod yn fwy na :value.',
        'file'    => 'Rhaid i\'r :attribute fod yn fwy na :value kilobytes.',
        'string'  => 'Rhaid i\'r :attribute fod yn fwy na :value cymeriadau.',
        'array'   => 'Rhaid i\'r :attribute fod yn fwy na :value eitemau.',
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

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Password confirmation required',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
