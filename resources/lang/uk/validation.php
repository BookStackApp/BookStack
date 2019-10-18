<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute повинен бути прийнятий.',
    'active_url'           => ':attribute не є дійсною URL-адресою.',
    'after'                => ':attribute повинно бути датою після :date.',
    'alpha'                => ':attribute може містити лише літери.',
    'alpha_dash'           => ':attribute може містити лише літери, цифри та дефіси.',
    'alpha_num'            => ':attribute може містити лише літери та цифри.',
    'array'                => ':attribute повинен бути масивом.',
    'before'               => ':attribute повинен бути датою до :date.',
    'between'              => [
        'numeric' => ':attribute повинен бути між :min та :max.',
        'file'    => ':attribute повинен бути між :min та :max кілобайт.',
        'string'  => ':attribute повинен бути між :min та :max символів.',
        'array'   => ':attribute повинен бути між :min та :max елементів.',
    ],
    'boolean'              => ':attribute поле має бути true або false.',
    'confirmed'            => ':attribute підтвердження не збігається.',
    'date'                 => ':attribute не є дійсною датою.',
    'date_format'          => ':attribute не відповідає формату :format.',
    'different'            => ':attribute та :other повинні бути різними.',
    'digits'               => ':attribute повинні бути :digits цифрами.',
    'digits_between'       => ':attribute має бути між :min та :max цифр.',
    'email'                => ':attribute повинна бути дійсною електронною адресою.',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'filled'               => ':attribute поле обов\'язкове.',
    'gt'                   => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'string'  => 'The :attribute must be greater than :value characters.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'string'  => 'The :attribute must be greater than or equal :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'exists'               => 'Вибраний :attribute недійсний.',
    'image'                => ':attribute повинен бути зображенням.',
    'image_extension'      => ':attribute повинен мати дійсне та підтримуване розширення зображення.',
    'in'                   => 'Вибраний :attribute недійсний.',
    'integer'              => ':attribute повинен бути цілим числом.',
    'ip'                   => ':attribute повинна бути дійсною IP-адресою.',
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
        'numeric' => ':attribute не може бути більшим за :max.',
        'file'    => ':attribute не може бути більшим за :max кілобайт.',
        'string'  => ':attribute не може бути більшим за :max символів.',
        'array'   => ':attribute не може бути більше ніж :max елементів.',
    ],
    'mimes'                => ':attribute повинен бути файлом типу: :values.',
    'min'                  => [
        'numeric' => ':attribute повинен бути принаймні :min.',
        'file'    => ':attribute повинен бути принаймні :min кілобайт.',
        'string'  => ':attribute повинен бути принаймні :min символів.',
        'array'   => ':attribute повинен містити принаймні :min елементів.',
    ],
    'no_double_extension'  => ':attribute повинен мати тільки одне розширення файлу.',
    'not_in'               => 'Вибраний :attribute недійсний.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => ':attribute повинен бути числом.',
    'regex'                => ':attribute формат недійсний.',
    'required'             => ':attribute поле обов\'язкове.',
    'required_if'          => ':attribute поле бов\'язкове, коли :other з значенням :value.',
    'required_with'        => ':attribute поле бов\'язкове, коли :values встановлено.',
    'required_with_all'    => ':attribute поле бов\'язкове, коли :values встановлені.',
    'required_without'     => ':attribute поле бов\'язкове, коли :values не встановлені.',
    'required_without_all' => ':attribute поле бов\'язкове, коли жодне з :values не встановлене.',
    'same'                 => ':attribute та :other мають збігатись.',
    'size'                 => [
        'numeric' => ':attribute має бути :size.',
        'file'    => ':attribute має бути :size кілобайт.',
        'string'  => ':attribute має бути :size символів.',
        'array'   => ':attribute має містити :size елементів.',
    ],
    'string'               => ':attribute повинен бути рядком.',
    'timezone'             => ':attribute повинен бути дійсною зоною.',
    'unique'               => ':attribute вже є.',
    'url'                  => ':attribute формат недійсний.',
    'uploaded'             => 'Не вдалося завантажити файл. Сервер може не приймати файли такого розміру.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Необхідне підтвердження пароля',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
