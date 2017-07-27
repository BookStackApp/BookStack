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

    'accepted'             => ':attribute musi zostać zaakceptowany.',
    'active_url'           => ':attribute nie jest prawidłowym adresem URL.',
    'after'                => ':attribute musi być datą następującą po :date.',
    'alpha'                => ':attribute może zawierać wyłącznie litery.',
    'alpha_dash'           => ':attribute może zawierać wyłącznie litery, cyfry i myślniki.',
    'alpha_num'            => ':attribute może zawierać wyłącznie litery i cyfry.',
    'array'                => ':attribute musi być tablicą.',
    'before'               => ':attribute musi być datą poprzedzającą :date.',
    'between'              => [
        'numeric' => ':attribute musi zawierać się w przedziale od :min do :max.',
        'file'    => 'Waga :attribute musi zawierać się pomiędzy :min i :max kilobajtów.',
        'string'  => 'Długość :attribute musi zawierać się pomiędzy :min i :max.',
        'array'   => ':attribute musi mieć od :min do :max elementów.',
    ],
    'boolean'              => ':attribute musi być wartością prawda/fałsz.',
    'confirmed'            => ':attribute i potwierdzenie muszą być zgodne.',
    'date'                 => ':attribute nie jest prawidłową datą.',
    'date_format'          => ':attribute musi mieć format :format.',
    'different'            => ':attribute i :other muszą się różnić.',
    'digits'               => ':attribute musi mieć :digits cyfr.',
    'digits_between'       => ':attribute musi mieć od :min do :max cyfr.',
    'email'                => ':attribute musi być prawidłowym adresem e-mail.',
    'filled'               => ':attribute jest wymagany.',
    'exists'               => 'Wybrana wartość :attribute jest nieprawidłowa.',
    'image'                => ':attribute musi być obrazkiem.',
    'in'                   => 'Wybrana wartość :attribute jest nieprawidłowa.',
    'integer'              => ':attribute musi być liczbą całkowitą.',
    'ip'                   => ':attribute musi być prawidłowym adresem IP.',
    'max'                  => [
        'numeric' => 'Wartość :attribute nie może być większa niż :max.',
        'file'    => 'Wielkość :attribute nie może być większa niż :max kilobajtów.',
        'string'  => 'Długość :attribute nie może być większa niż :max znaków.',
        'array'   => 'Rozmiar :attribute nie może być większy niż :max elementów.',
    ],
    'mimes'                => ':attribute musi być plikiem typu: :values.',
    'min'                  => [
        'numeric' => 'Wartość :attribute nie może być mniejsza od :min.',
        'file'    => 'Wielkość :attribute nie może być mniejsza niż :min kilobajtów.',
        'string'  => 'Długość :attribute nie może być mniejsza niż :min znaków.',
        'array'   => 'Rozmiar :attribute musi posiadać co najmniej :min elementy.',
    ],
    'not_in'               => 'Wartość :attribute jest nieprawidłowa.',
    'numeric'              => ':attribute musi być liczbą.',
    'regex'                => 'Format :attribute jest nieprawidłowy.',
    'required'             => 'Pole :attribute jest wymagane.',
    'required_if'          => 'Pole :attribute jest wymagane jeśli :other ma wartość :value.',
    'required_with'        => 'Pole :attribute jest wymagane jeśli :values zostało wprowadzone.',
    'required_with_all'    => 'Pole :attribute jest wymagane jeśli :values są obecne.',
    'required_without'     => 'Pole :attribute jest wymagane jeśli :values nie zostało wprowadzone.',
    'required_without_all' => 'Pole :attribute jest wymagane jeśli żadna z wartości :values nie została podana.',
    'same'                 => 'Pole :attribute i :other muszą być takie same.',
    'size'                 => [
        'numeric' => ':attribute musi mieć długość :size.',
        'file'    => ':attribute musi mieć :size kilobajtów.',
        'string'  => ':attribute mmusi mieć długość :size znaków.',
        'array'   => ':attribute musi posiadać :size elementów.',
    ],
    'string'               => ':attribute musi być ciągiem znaków.',
    'timezone'             => ':attribute musi być prawidłową strefą czasową.',
    'unique'               => ':attribute zostało już zajęte.',
    'url'                  => 'Format :attribute jest nieprawidłowy.',

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
            'required_with' => 'Potwierdzenie hasła jest wymagane.',
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
