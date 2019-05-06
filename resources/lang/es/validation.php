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
    'filled'               => 'El campo :attribute es requerido.',
    'exists'               => 'El :attribute seleccionado es inválido.',
    'image'                => 'El :attribute debe ser una imagen.',
    'in'                   => 'El selected :attribute es inválio.',
    'integer'              => 'El :attribute debe ser un entero.',
    'ip'                   => 'El :attribute debe ser una dirección IP válida.',
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
    'numeric'              => 'El :attribute debe ser numérico.',
    'regex'                => 'El formato de :attribute es inválido',
    'required'             => 'El :attribute es requerido.',
    'required_if'          => 'El :attribute es requerido cuando :other vale :value.',
    'required_with'        => 'El campo :attribute es requerido cuando se encuentre entre los valores :values.',
    'required_with_all'    => 'El campo :attribute es requerido cuando los valores sean :values.',
    'required_without'     => 'El :attribute es requerido cuando no se encuentre entre los valores :values.',
    'required_without_all' => 'El :attribute es requerido cuando ninguno de los valores :values están presentes.',
    'same'                 => 'El :attribute y :other deben coincidir.',
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

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Requerida confirmación de contraseña',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
