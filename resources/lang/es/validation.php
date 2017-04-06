<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'El :attribute debe ser aceptado.',
    'active_url'           => 'El :attribute no es una URl vÃlida.',
    'after'                => 'El :attribute debe ser una fecha posterior :date.',
    'alpha'                => 'El :attribute solo puede contener letras.',
    'alpha_dash'           => 'El :attribute sÃlo puede contener letras, nÃmeros y guiones.',
    'alpha_num'            => 'El :attribute sÃlo puede contener letras y nÃmerosº.',
    'array'                => 'El :attribute debe de ser un array.',
    'before'               => 'El :attribute debe ser una fecha anterior a  :date.',
    'between'              => [
        'numeric' => 'El :attribute debe estar entre :min y :max.',
        'file'    => 'El :attribute debe estar entre :min y :max kilobytes.',
        'string'  => 'El :attribute debe estar entre :min y :max carÃ¡cteres.',
        'array'   => 'El :attribute debe estar entre :min y :max items.',
    ],
    'boolean'              => 'El campo :attribute debe ser true o false.',
    'confirmed'            => 'La confirmaciÃn de :attribute no concuerda.',
    'date'                 => 'El :attribute no es una fecha vÃlida.',
    'date_format'          => 'El :attribute no coincide con el formato :format.',
    'different'            => ':attribute y :other deben ser diferentes.',
    'digits'               => ':attribute debe ser de :digits dÃ­gitos.',
    'digits_between'       => ':attribute debe ser un valor entre :min y :max dÃ­gios.',
    'email'                => ':attribute debe ser una direcciÃn vÃlida.',
    'filled'               => 'El campo :attribute es requerido.',
    'exists'               => 'El :attribute seleccionado es invÃ¡lido.',
    'image'                => 'El :attribute debe ser una imagen.',
    'in'                   => 'El selected :attribute es invÃ¡lio.',
    'integer'              => 'El :attribute debe ser un entero.',
    'ip'                   => 'El :attribute debe ser una direcciÃn IP vÃlida.',
    'max'                  => [
        'numeric' => ':attribute no puede ser mayor que :max.',
        'file'    => ':attribute no puede ser mayor que :max kilobytes.',
        'string'  => ':attribute no puede ser mayor que :max carÃ¡cteres.',
        'array'   => ':attribute no puede contener mÃs de :max items.',
    ],
    'mimes'                => ':attribute debe ser un fichero de tipo: :values.',
    'min'                  => [
        'numeric' => ':attribute debe ser al menos de :min.',
        'file'    => ':attribute debe ser al menos :min kilobytes.',
        'string'  => ':attribute debe ser al menos :min caracteres.',
        'array'   => ':attribute debe tener como mÃnimo :min items.',
    ],
    'not_in'               => ':attribute seleccionado es invÃ¡lio.',
    'numeric'              => ':attribute debe ser numÃ©rico.',
    'regex'                => ':attribute con formato invÃlido',
    'required'             => ':attribute es requerido.',
    'required_if'          => ':attribute es requerido cuando :other vale :value.',
    'required_with'        => 'El campo :attribute es requerido cuando se encuentre entre los valores :values.',
    'required_with_all'    => 'El campo :attribute es requerido cuando los valores sean :values.',
    'required_without'     => ':attribute es requerido cuando no se encuentre entre los valores :values.',
    'required_without_all' => ':attribute es requerido cuando ninguno de los valores :values estÃn presentes.',
    'same'                 => ':attribute y :other deben coincidir.',
    'size'                 => [
        'numeric' => ':attribute debe ser :size.',
        'file'    => ':attribute debe ser :size kilobytes.',
        'string'  => ':attribute debe ser :size caracteres.',
        'array'   => ':attribute debe contener :size items.',
    ],
    'string'               => 'El atributo :attribute debe ser una cadena.',
    'timezone'             => 'El atributo :attribute debe ser una zona vÃlida.',
    'unique'               => 'El atributo :attribute ya ha sido tomado.',
    'url'                  => 'El atributo :attribute tiene un formato invÃlido¡.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'password-confirm' => [
            'required_with' => 'ConfirmaciÃn de Password requerida',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
