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

    'accepted'             => ':attribute должен быть принят.',
    'active_url'           => ':attribute не является корректным URL.',
    'after'                => ':attribute дата должна быть позже :date.',
    'alpha'                => ':attribute может содержать только буквы.',
    'alpha_dash'           => ':attribute может содержать только буквы, цифры и тире.',
    'alpha_num'            => ':attribute должен содержать только буквы и цифры.',
    'array'                => ':attribute должен быть массивом.',
    'before'               => ':attribute дата должна быть до :date.',
    'between'              => [
        'numeric' => ':attribute должен быть между :min и :max.',
        'file'    => ':attribute должен быть между :min и :max килобайт.',
        'string'  => 'длина :attribute должена быть между :min и :max символами.',
        'array'   => ':attribute должен содержать не менее :min и не более:max элементов.',
    ],
    'boolean'              => ':attribute поле может быть только true или false.',
    'confirmed'            => ':attribute подтверждение не совпадает.',
    'date'                 => ':attribute некорректные данные.',
    'date_format'          => ':attribute не соответствует формату :format.',
    'different'            => ':attribute и :other должны быть различны.',
    'digits'               => ':attribute должен состоять из :digits цифр.',
    'digits_between'       => ':attribute должен иметь от :min до :max цифр.',
    'email'                => ':attribute должен быть корректным email адресом.',
    'filled'               => ':attribute поле необходимо.',
    'exists'               => 'выделенный :attribute некорректен.',
    'image'                => ':attribute должен быть изображением.',
    'in'                   => 'выделенный :attribute некорректен.',
    'integer'              => ':attribute должно быть целое число.',
    'ip'                   => ':attribute должен быть корректным IP адресом.',
    'max'                  => [
        'numeric' => ':attribute не может быть больше чем :max.',
        'file'    => ':attribute не может быть больше чем :max килобайт.',
        'string'  => ':attribute не может быть больше чем :max символов.',
        'array'   => ':attribute не может содержать больше чем :max элементов.',
    ],
    'mimes'                => ':attribute должен быть файлом с типом: :values.',
    'min'                  => [
        'numeric' => ':attribute должен быть хотя бы :min.',
        'file'    => ':attribute должен быть минимум :min килобайт.',
        'string'  => ':attribute должен быть минимум :min символов.',
        'array'   => ':attribute должен содержать хотя бы :min элементов.',
    ],
    'not_in'               => 'Выбранный :attribute некорректен.',
    'numeric'              => ':attribute должен быть числом.',
    'regex'                => ':attribute неправильный формат.',
    'required'             => ':attribute обязательное поле.',
    'required_if'          => ':attribute обязательное поле когда :other со значением :value.',
    'required_with'        => ':attribute обязательное поле когда :values установлено.',
    'required_with_all'    => ':attribute обязательное поле когда :values установлены.',
    'required_without'     => ':attribute обязательное поле когда :values не установлены.',
    'required_without_all' => ':attribute обязательное поле когда ни одно из :values не установлены.',
    'same'                 => ':attribute и :other должны совпадать.',
    'size'                 => [
        'numeric' => ':attribute должен быть :size.',
        'file'    => ':attribute должен быть :size килобайт.',
        'string'  => ':attribute должен быть :size символов.',
        'array'   => ':attribute должен содержать :size элементов.',
    ],
    'string'               => ':attribute должен быть строкой.',
    'timezone'             => ':attribute должен быть корректным часовым поясом.',
    'unique'               => ':attribute уже есть.',
    'url'                  => ':attribute имеет неправильный формат.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention 'attribute.rule' to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'password-confirm' => [
            'required_with' => 'Требуется подтверждение пароля',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of 'email'. This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
