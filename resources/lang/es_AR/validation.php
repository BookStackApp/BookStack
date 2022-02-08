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
    'active_url'           => 'El :attribute no es una URl válida.',
    'after'                => 'El :attribute debe ser una fecha posterior :date.',
    'alpha'                => 'El :attribute solo puede contener letras.',
    'alpha_dash'           => 'El :attribute solo puede contener letras, números y guiones.',
    'alpha_num'            => 'El :attribute solo puede contener letras y número.',
    'array'                => 'El :attribute debe de ser un array.',
    'backup_codes'         => 'El código suministrado no es válido o ya fue utilizado.',
    'before'               => 'El :attribute debe ser una fecha anterior a  :date.',
    'between'              => [
        'numeric' => 'El :attribute debe estar entre :min y :max.',
        'file'    => 'El :attribute debe estar entre :min y :max kilobytes.',
        'string'  => 'El :attribute debe estar entre :min y :max carácteres.',
        'array'   => 'El :attribute debe estar entre :min y :max items.',
    ],
    'boolean'              => 'El campo :attribute debe ser true o false.',
    'confirmed'            => 'La confirmación de :attribute no concuerda.',
    'date'                 => 'El :attribute no es una fecha válida.',
    'date_format'          => 'El :attribute no coincide con el formato :format.',
    'different'            => ':attribute y :other deben ser diferentes.',
    'digits'               => ':attribute debe ser de :digits dígitos.',
    'digits_between'       => ':attribute debe ser un valor entre :min y :max dígios.',
    'email'                => ':attribute debe ser una dirección álida.',
    'ends_with' => 'El :attribute debe terminar con uno de los siguientes: :values',
    'file'                 => 'El :attribute debe ser proporcionado como un archivo válido.',
    'filled'               => 'El campo :attribute es requerido.',
    'gt'                   => [
        'numeric' => 'El :attribute debe ser mayor que :value.',
        'file'    => 'El :attribute debe ser mayor que :value kilobytes.',
        'string'  => 'El :attribute debe ser mayor que :value caracteres.',
        'array'   => 'El :attribute debe tener más de :value objetos.',
    ],
    'gte'                  => [
        'numeric' => 'El :attribute debe ser mayor o igual a :value.',
        'file'    => 'El :attribute debe ser mayor o igual a :value kilobytes.',
        'string'  => 'El :attribute debe ser mayor o igual a :value caracteres.',
        'array'   => 'El :attribute debe tener :value objetos o más.',
    ],
    'exists'               => 'El :attribute seleccionado es inválido.',
    'image'                => 'El :attribute debe ser una imagen.',
    'image_extension'      => 'El :attribute debe tener una extensión de imagen válida y soportada.',
    'in'                   => 'El selected :attribute es inválio.',
    'integer'              => 'El :attribute debe ser un entero.',
    'ip'                   => 'El :attribute debe ser una dirección IP álida.',
    'ipv4'                 => 'El :attribute debe ser una dirección IPv4 válida.',
    'ipv6'                 => 'El :attribute debe ser una dirección IPv6 válida.',
    'json'                 => 'El :attribute debe ser una cadena JSON válida.',
    'lt'                   => [
        'numeric' => 'El :attribute debe ser menor que :value.',
        'file'    => 'El :attribute debe ser menor que :value kilobytes.',
        'string'  => 'El :attribute debe ser menor que :value caracteres.',
        'array'   => 'El :attribute debe tener menos de :value objetos.',
    ],
    'lte'                  => [
        'numeric' => 'El :attribute debe ser menor o igual a :value.',
        'file'    => 'El :attribute debe ser menor o igual a :value kilobytes.',
        'string'  => 'El :attribute debe ser menor o igual a :value caracteres.',
        'array'   => 'El :attribute no debe tener más de :value objetos.',
    ],
    'max'                  => [
        'numeric' => ':attribute no puede ser mayor que :max.',
        'file'    => ':attribute no puede ser mayor que :max kilobytes.',
        'string'  => ':attribute no puede ser mayor que :max carácteres.',
        'array'   => ':attribute no puede contener más de :max items.',
    ],
    'mimes'                => ':attribute debe ser un fichero de tipo: :values.',
    'min'                  => [
        'numeric' => ':attribute debe ser al menos de :min.',
        'file'    => ':attribute debe ser al menos :min kilobytes.',
        'string'  => ':attribute debe ser al menos :min caracteres.',
        'array'   => ':attribute debe tener como mínimo :min items.',
    ],
    'not_in'               => ':attribute seleccionado es inválido.',
    'not_regex'            => 'El formato de :attribute es inválido.',
    'numeric'              => ':attribute debe ser numérico.',
    'regex'                => ':attribute con formato inválido',
    'required'             => ':attribute es requerido.',
    'required_if'          => ':attribute es requerido cuando :other vale :value.',
    'required_with'        => 'El campo :attribute es requerido cuando se encuentre entre los valores :values.',
    'required_with_all'    => 'El campo :attribute es requerido cuando los valores sean :values.',
    'required_without'     => ':attribute es requerido cuando no se encuentre entre los valores :values.',
    'required_without_all' => ':attribute es requerido cuando ninguno de los valores :values están presentes.',
    'same'                 => ':attribute y :other deben coincidir.',
    'safe_url'             => 'El enlace provisto puede ser inseguro.',
    'size'                 => [
        'numeric' => ':attribute debe ser :size.',
        'file'    => ':attribute debe ser :size kilobytes.',
        'string'  => ':attribute debe ser :size caracteres.',
        'array'   => ':attribute debe contener :size items.',
    ],
    'string'               => 'El atributo :attribute debe ser una cadena.',
    'timezone'             => 'El atributo :attribute debe ser una zona válida.',
    'totp'                 => 'El código suministrado no es válido o ya expiró.',
    'unique'               => 'El atributo :attribute ya ha sido tomado.',
    'url'                  => 'El atributo :attribute tiene un formato inválido.',
    'uploaded'             => 'El archivo no se pudo subir. Puede ser que el servidor no acepte archivos de este tamaño.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Confirmación de Password requerida',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
