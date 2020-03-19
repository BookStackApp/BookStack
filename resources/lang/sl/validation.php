<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute mora biti potrjen.',
    'active_url'           => ':attribute ni veljaven URL.',
    'after'                => ':attribute mora biti datum po :date.',
    'alpha'                => ':attribute lahko vsebuje samo črke.',
    'alpha_dash'           => ':attribute lahko vsebuje samo ?rke, ?tevilke in ?rtice.',
    'alpha_num'            => ':attribute lahko vsebuje samo črke in številke.',
    'array'                => ':attribute mora biti niz.',
    'before'               => ':attribute mora biti datum pred :date.',
    'between'              => [
        'numeric' => ':attribute mora biti med :min in :max.',
        'file'    => ':attribute mora biti med :min in :max kilobajti.',
        'string'  => ':attribute mora biti med :min in :max znaki.',
        'array'   => ':attribute mora imeti med :min in :max elementov.',
    ],
    'boolean'              => ':attribute polje mora biti pravilno ali napačno.',
    'confirmed'            => ':attribute potrditev se ne ujema.',
    'date'                 => ':attribute ni veljaven datum.',
    'date_format'          => ':attribute se ne ujema z obliko :format.',
    'different'            => ':attribute in :other morata biti različna.',
    'digits'               => 'Atribut mora biti: števnik.',
    'digits_between'       => ':attribute mora biti med :min in :max števkami.',
    'email'                => ':attribute mora biti veljaven e-naslov.',
    'ends_with' => 'The :attribute se mora končati z eno od določenih: :vrednost/values',
    'filled'               => 'Polje ne sme biti prazno.',
    'gt'                   => [
        'numeric' => ':attribute mora biti večji kot :vrednost.',
        'file'    => ':attribute mora biti večji kot :vrednost kilobytes',
        'string'  => ':attribute mora biti večji kot :vrednost znakov',
        'array'   => ':attribute mora biti večji kot :vrednost znakov',
    ],
    'gte'                  => [
        'numeric' => ':attribute mora biti večji kot ali enak :vrednost.',
        'file'    => ':attribute mora biti večji kot ali enak :vrednost kilobytes',
        'string'  => ':attribute mora biti večji kot ali enak :vrednost znakov',
        'array'   => ':attribute mora imeti :vrednost znakov ali več',
    ],
    'exists'               => 'Izbrani atribut je neveljaven.',
    'image'                => ':attribute mora biti slika.',
    'image_extension'      => 'The :attribute must have a valid & supported image extension.',
    'in'                   => 'izbran :attribute je neveljaven.',
    'integer'              => ':attribute mora biti celo število.',
    'ip'                   => ':attribute mora biti veljaven IP naslov.',
    'ipv4'                 => ':attribute mora biti veljaven IPv4 naslov.',
    'ipv6'                 => ':attribute mora biti veljaven IPv6 naslov.',
    'json'                 => ':attribute mora biti veljavna JSON povezava.',
    'lt'                   => [
        'numeric' => ':attribute mora biti manj kot :vrednost.',
        'file'    => ':attribute mora biti manj kot :vrednost kilobytes',
        'string'  => ':attribute mora biti manj kot :vrednost znakov',
        'array'   => ':attribute mora imeti manj kot :vrednost znakov',
    ],
    'lte'                  => [
        'numeric' => ':attribute mora biti manj kot ali enak :vrednost.',
        'file'    => ':attribute mora biti manj kot ali enak :vrednost kilobytes',
        'string'  => ':attribute mora biti manj kot ali enak :vrednost znakov',
        'array'   => ':attribute ne sme imeti več kot :vrednost elementov',
    ],
    'max'                  => [
        'numeric' => ':attribute ne sme biti večja od :max.',
        'file'    => ':attribute ne sme biti večja od :max kilobytes.',
        'string'  => 'Atribut naj ne bo večji od: max znakov.',
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
