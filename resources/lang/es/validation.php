<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'El :attribute debe ser aceptado.',
    'active_url'           => 'El :attribute no es una URL válida.',
    'after'                => 'El :attribute debe ser una fecha posterior :date.',
    'alpha'                => 'El :attribute solo puede contener letras.',
    'alpha_dash'           => 'El :attribute solo puede contener letras, números y guiones.',
    'alpha_num'            => 'El :attribute solo puede contener letras y números.',
    'array'                => 'El :attribute debe de ser un array.',
    'before'               => 'El :attribute debe ser una fecha anterior a  :date.',
    'between'              => [
        'numeric' => 'El :attribute debe estar entre :min y :max.',
        'file'    => 'El :attribute debe estar entre :min y :max kilobytes.',
        'string'  => 'El :attribute debe estar entre :min y :max caracteres.',
        'array'   => 'El :attribute debe estar entre :min y :max items.',
    ],
    'boolean'              => 'El campo :attribute debe ser true o false.',
    'confirmed'            => 'La confirmación de :attribute no concuerda.',
    'date'                 => 'El :attribute no es una fecha válida.',
    'date_format'          => 'El :attribute no coincide con el formato :format.',
    'different'            => ':attribute y :other deben ser diferentes.',
    'digits'               => ':attribute debe ser de :digits dígitos.',
    'digits_between'       => ':attribute debe ser un valor entre :min y :max dígios.',
    'email'                => ':attribute debe ser un correo electrónico válido.',
    'ends_with' => 'El :attribute debe terminar con uno de los siguientes: :values',
    'filled'               => 'El campo :attribute es requerido.',
    'gt'                   => [
        'numeric' => 'El :attribute debe ser mayor que :value.',
        'file'    => 'El :attribute debe ser mayor que :value kilobytes.',
        'string'  => 'El :attribute debe ser mayor que :value caracteres.',
        'array'   => 'El :attribute debe tener más de :value elementos.',
    ],
    'gte'                  => [
        'numeric' => 'El :attribute debe ser mayor o igual que :value.',
        'file'    => 'El :attribute debe ser mayor o igual que :value kilobytes.',
        'string'  => 'El :attribute debe ser mayor o igual que :value caracteres.',
        'array'   => 'El :attribute debe tener :value o más elementos.',
    ],
    'exists'               => 'El :attribute seleccionado es inválido.',
    'image'                => 'El :attribute debe ser una imagen.',
    'image_extension'      => 'El :attribute debe tener una extensión de imagen válida y soportada.',
    'in'                   => 'El selected :attribute es inválio.',
    'integer'              => 'El :attribute debe ser un entero.',
    'ip'                   => 'El :attribute debe ser una dirección IP válida.',
    'ipv4'                 => 'El :attribute debe ser una dirección IPv4 válida.',
    'ipv6'                 => 'El :attribute debe ser una dirección IPv6 válida.',
    'json'                 => 'El :attribute debe ser una cadena JSON válida.',
    'lt'                   => [
        'numeric' => 'El :attribute debe ser menor que :value.',
        'file'    => 'El :attribute debe ser menor que :value kilobytes.',
        'string'  => 'El :attribute debe ser menor que :value caracteres.',
        'array'   => 'El :attribute debe tener menos de :value elementos.',
    ],
    'lte'                  => [
        'numeric' => 'El :attribute debe ser menor o igual que :value.',
        'file'    => 'El :attribute debe ser menor o igual que :value kilobytes.',
        'string'  => 'El :attribute debe ser menor o igual que :value caracteres.',
        'array'   => 'El :attribute no debe tener más de :value elementos.',
    ],
    'max'                  => [
        'numeric' => 'El :attribute no puede ser mayor que :max.',
        'file'    => 'El :attribute no puede ser mayor que :max kilobytes.',
        'string'  => 'El :attribute no puede ser mayor que :max carácteres.',
        'array'   => 'El :attribute no puede contener más de :max items.',
    ],
    'mimes'                => 'El :attribute debe ser un fichero de tipo: :values.',
    'min'                  => [
        'numeric' => 'El :attribute debe ser al menos de :min.',
        'file'    => 'El :attribute debe ser al menos :min kilobytes.',
        'string'  => 'El :attribute debe ser al menos :min caracteres.',
        'array'   => 'El :attribute debe tener como mínimo :min items.',
    ],
    'not_in'               => 'El :attribute seleccionado es inválio.',
    'not_regex'            => 'El formato de :attribute es inválido.',
    'numeric'              => 'El :attribute debe ser numérico.',
    'regex'                => 'El formato de :attribute es inválido',
    'required'             => 'El :attribute es requerido.',
    'required_if'          => 'El :attribute es requerido cuando :other vale :value.',
    'required_with'        => 'El campo :attribute es requerido cuando se encuentre entre los valores :values.',
    'required_with_all'    => 'El campo :attribute es requerido cuando los valores sean :values.',
    'required_without'     => 'El :attribute es requerido cuando no se encuentre entre los valores :values.',
    'required_without_all' => 'El :attribute es requerido cuando ninguno de los valores :values están presentes.',
    'same'                 => 'El :attribute y :other deben coincidir.',
    'safe_url'             => 'El enlace proporcionado puede no ser seguro.',
    'size'                 => [
        'numeric' => ':attribute debe ser :size.',
        'file'    => ':attribute debe ser :size kilobytes.',
        'string'  => ':attribute debe ser :size caracteres.',
        'array'   => ':attribute debe contener :size items.',
    ],
    'string'               => 'El atributo :attribute debe ser una cadena de texto.',
    'timezone'             => 'El atributo :attribute debe ser una zona válida.',
    'unique'               => 'El atributo :attribute ya ha sido tomado.',
    'url'                  => 'El atributo :attribute tiene un formato inválido.',
    'uploaded'             => 'El archivo no ha podido subirse. Es posible que el servidor no acepte archivos de este tamaño.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Requerida confirmación de contraseña',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
