<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'Ви повинні прийняти :attribute.',
    'active_url'           => 'Поле :attribute не є правильним URL.',
    'after'                => 'Поле :attribute має містити дату не раніше :date.',
    'alpha'                => 'Поле :attribute має містити лише літери.',
    'alpha_dash'           => 'Поле :attribute має містити лише літери, цифри, дефіси та підкреслення.',
    'alpha_num'            => 'Поле :attribute має містити лише літери та цифри.',
    'array'                => 'Поле :attribute має бути масивом.',
    'before'               => 'Поле :attribute має містити дату не пізніше :date.',
    'between'              => [
        'numeric' => 'Поле :attribute має бути між :min та :max.',
        'file'    => 'Розмір файлу в полі :attribute має бути не менше :min та не більше :max кілобайт.',
        'string'  => 'Текст в полі :attribute має бути не менше :min та не більше :max символів.',
        'array'   => 'Поле :attribute має містити від :min до :max елементів.',
    ],
    'boolean'              => 'Поле :attribute повинне містити true чи false.',
    'confirmed'            => 'Поле :attribute не збігається з підтвердженням.',
    'date'                 => 'Поле :attribute не є датою.',
    'date_format'          => 'Поле :attribute не відповідає формату :format.',
    'different'            => 'Поля :attribute та :other повинні бути різними.',
    'digits'               => 'Довжина цифрового поля :attribute повинна дорівнювати :digits.',
    'digits_between'       => 'Довжина цифрового поля :attribute повинна бути від :min до :max.',
    'email'                => 'Поле :attribute повинне містити коректну електронну адресу.',
    'ends_with' => 'Поле :attribute має закінчуватися одним з наступних значень: :values',
    'filled'               => 'Поле :attribute є обов\'язковим для заповнення.',
    'gt'                   => [
        'numeric' => 'Поле :attribute має бути більше ніж :value.',
        'file'    => 'Поле :attribute має бути більше ніж :value кілобайт.',
        'string'  => 'Поле :attribute має бути більше ніж :value символів.',
        'array'   => 'Поле :attribute має містити більше ніж :value елементів.',
    ],
    'gte'                  => [
        'numeric' => 'Поле :attribute має дорівнювати чи бути більше ніж :value.',
        'file'    => 'Поле :attribute має дорівнювати чи бути більше ніж :value кілобайт.',
        'string'  => 'Поле :attribute має дорівнювати чи бути більше ніж :value символів.',
        'array'   => 'Поле :attribute має містити :value чи більше елементів.',
    ],
    'exists'               => 'Вибране для :attribute значення не коректне.',
    'image'                => 'Поле :attribute має містити зображення.',
    'image_extension'      => 'Поле :attribute має містити дійсне та підтримуване розширення зображення.',
    'in'                   => 'Вибране для :attribute значення не коректне.',
    'integer'              => 'Поле :attribute має містити ціле число.',
    'ip'                   => 'Поле :attribute має містити IP адресу.',
    'ipv4'                 => 'Поле :attribute має містити IPv4 адресу.',
    'ipv6'                 => 'Поле :attribute має містити IPv6 адресу.',
    'json'                 => 'Дані поля :attribute мають бути в форматі JSON.',
    'lt'                   => [
        'numeric' => 'Поле :attribute має бути менше ніж :value.',
        'file'    => 'Поле :attribute має бути менше ніж :value кілобайт.',
        'string'  => 'Поле :attribute має бути менше ніж :value символів.',
        'array'   => 'Поле :attribute має містити менше ніж :value елементів.',
    ],
    'lte'                  => [
        'numeric' => 'Поле :attribute має дорівнювати чи бути менше ніж :value.',
        'file'    => 'Поле :attribute має дорівнювати чи бути менше ніж :value кілобайт.',
        'string'  => 'Поле :attribute має дорівнювати чи бути менше ніж :value символів.',
        'array'   => 'Поле :attribute має містити не більше ніж :value елементів.',
    ],
    'max'                  => [
        'numeric' => 'Поле :attribute має бути не більше :max.',
        'file'    => 'Файл в полі :attribute має бути не більше :max кілобайт.',
        'string'  => 'Текст в полі :attribute повинен мати довжину не більшу за :max.',
        'array'   => 'Поле :attribute повинне містити не більше :max елементів.',
    ],
    'mimes'                => 'Поле :attribute повинне містити файл одного з типів: :values.',
    'min'                  => [
        'numeric' => 'Поле :attribute повинне бути не менше :min.',
        'file'    => 'Розмір файлу в полі :attribute має бути не меншим :min кілобайт.',
        'string'  => 'Текст в полі :attribute повинен містити не менше :min символів.',
        'array'   => 'Поле :attribute повинне містити не менше :min елементів.',
    ],
    'not_in'               => 'Вибране для :attribute значення не коректне.',
    'not_regex'            => 'Формат поля :attribute не вірний.',
    'numeric'              => 'Поле :attribute повинно містити число.',
    'regex'                => 'Поле :attribute має хибний формат.',
    'required'             => 'Поле :attribute є обов\'язковим для заповнення.',
    'required_if'          => 'Поле :attribute є обов\'язковим для заповнення, коли :other є рівним :value.',
    'required_with'        => 'Поле :attribute є обов\'язковим для заповнення, коли :values вказано.',
    'required_with_all'    => 'Поле :attribute є обов\'язковим для заповнення, коли :values вказано.',
    'required_without'     => 'Поле :attribute є обов\'язковим для заповнення, коли :values не вказано.',
    'required_without_all' => 'Поле :attribute є обов\'язковим для заповнення, коли :values не вказано.',
    'same'                 => 'Поля :attribute та :other мають збігатися.',
    'safe_url'             => 'The provided link may not be safe.',
    'size'                 => [
        'numeric' => 'Поле :attribute має бути довжини :size.',
        'file'    => 'Файл в полі :attribute має бути розміром :size кілобайт.',
        'string'  => 'Текст в полі :attribute повинен містити :size символів.',
        'array'   => 'Поле :attribute повинне містити :size елементів.',
    ],
    'string'               => 'Поле :attribute повинне містити текст.',
    'timezone'             => 'Поле :attribute повинне містити коректну часову зону.',
    'unique'               => 'Вказане значення поля :attribute вже існує.',
    'url'                  => 'Формат поля :attribute неправильний.',
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
