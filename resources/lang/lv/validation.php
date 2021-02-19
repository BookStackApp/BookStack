<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute ir jāapstiprina.',
    'active_url'           => ':attribute nav derīgs URL.',
    'after'                => ':attribute ir jābūt datumam pēc :date.',
    'alpha'                => ':attribute var saturēt tikai burtus.',
    'alpha_dash'           => ':attribute var saturēt tikai burtus, ciparus, domuzīmes un apakš svītras.',
    'alpha_num'            => ':attribute var saturēt tikai burtus un ciparus.',
    'array'                => ':attribute ir jābūt masīvam.',
    'before'               => ':attribute jābūt datumam pirms :date.',
    'between'              => [
        'numeric' => ':attribute jābūt starp :min un :max.',
        'file'    => ':attribute jābūt starp :min un :max kilobaitiem.',
        'string'  => ':attribute jābūt starp :min un :max rakstzīmēm.',
        'array'   => 'Atribūtam jābūt starp: min un: max vienumiem.',
    ],
    'boolean'              => ':attribute jābūt True vai False.',
    'confirmed'            => ':attribute apstiprinājums nesakrīt.',
    'date'                 => ':attribute nav derīgs datums.',
    'date_format'          => ':attribute neatbilst formātam :format.',
    'different'            => ':attribute un :other jābūt atšķirīgiem.',
    'digits'               => ':attribute jābūt :digits cipariem.',
    'digits_between'       => ':attribute jābūt starp :min un :max cipariem.',
    'email'                => ':attribute jābūt derīgai e-pasta adresei.',
    'ends_with' => ':attribute jābeidzas ar vienu no :values',
    'filled'               => ':attribute lauks ir obligāts.',
    'gt'                   => [
        'numeric' => ':attribute jābūt lielākam kā :value.',
        'file'    => ':attribute jābūt lielākam kā :value kilobaitiem.',
        'string'  => ':attribute jābūt lielākam kā :value simboliem.',
        'array'   => ':attribute jāsatur vairāk kā :value vienības.',
    ],
    'gte'                  => [
        'numeric' => ':attribute jābūt lielākam vai vienādam ar :value.',
        'file'    => ':attribute jābūt lielākam vai vienādam ar :value kilobaitiem.',
        'string'  => 'The :attribute must be greater than or equal :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'exists'               => 'The selected :attribute is invalid.',
    'image'                => ':attribute jābūt attēlam.',
    'image_extension'      => 'The :attribute must have a valid & supported image extension.',
    'in'                   => 'Iezīmētais :attribute ir nederīgs.',
    'integer'              => ':attribute ir jābūt veselam skaitlim.',
    'ip'                   => ':attribute jābūt derīgai IP adresei.',
    'ipv4'                 => ':attribute jābūt derīgai IPv4 adresei.',
    'ipv6'                 => ':attribute jābūt derīgai IPv6 adresei.',
    'json'                 => ':attribute jābūt derīgai JSON virknei.',
    'lt'                   => [
        'numeric' => 'The :attribute must be less than :value.',
        'file'    => ':attribute jābūt mazāk kā :value kilobaitiem.',
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
        'numeric' => ':attribute nevar būt lielāks kā :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => ':attribute ir jābūt vismaz :min.',
        'file'    => ':attribute jābūt vismaz :min kilobaitiem.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => ':attribute ir jābūt vismaz :min vienībām.',
    ],
    'no_double_extension'  => ':attribute drīkst būt tikai viens faila paplašinājums.',
    'not_in'               => 'Izvēlētais: atribūts ir nederīgs.',
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
