<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => '',
    'active_url'           => '',
    'after'                => '',
    'alpha'                => '',
    'alpha_dash'           => '',
    'alpha_num'            => '',
    'array'                => '',
    'before'               => '',
    'between'              => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'boolean'              => '',
    'confirmed'            => '',
    'date'                 => '',
    'date_format'          => '',
    'different'            => '',
    'digits'               => '',
    'digits_between'       => '',
    'email'                => '',
    'ends_with' => '',
    'filled'               => '',
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
    'exists'               => '',
    'image'                => '',
    'image_extension'      => '',
    'in'                   => '',
    'integer'              => '',
    'ip'                   => '',
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
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'mimes'                => '',
    'min'                  => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'no_double_extension'  => '',
    'not_in'               => '',
    'not_regex'            => '',
    'numeric'              => '',
    'regex'                => '',
    'required'             => '',
    'required_if'          => '',
    'required_with'        => '',
    'required_with_all'    => '',
    'required_without'     => '',
    'required_without_all' => '',
    'same'                 => '',
    'size'                 => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'string'               => '',
    'timezone'             => '',
    'unique'               => '',
    'url'                  => '',
    'uploaded'             => '',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => '',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
