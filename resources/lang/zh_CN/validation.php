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

    'accepted'             => ':attribute 需要被同意。',
    'active_url'           => ':attribute 并不是一个有效的网址',
    'after'                => ':attribute 必须是在 :date 后的日期。',
    'alpha'                => ':attribute 只能包含字母。',
    'alpha_dash'           => ':attribute 只能包含字母、数字和短横线。',
    'alpha_num'            => ':attribute 只能包含字母和数字。',
    'array'                => ':attribute 必须是一个数组。',
    'before'               => ':attribute 必须是在 :date 前的日期。',
    'between'              => [
        'numeric' => ':attribute 必须在:min到:max之间。',
        'file'    => ':attribute 必须为:min到:max KB。',
        'string'  => ':attribute 必须在:min到:max个字符之间。',
        'array'   => ':attribute 必须在:min到:max项之间.',
    ],
    'boolean'              => ':attribute 字段必须为真或假。',
    'confirmed'            => ':attribute 确认不符。',
    'date'                 => ':attribute 不是一个有效的日期。',
    'date_format'          => ':attribute 不匹配格式 :format。',
    'different'            => ':attribute 和 :other 必须不同。',
    'digits'               => ':attribute 必须为:digits位数。',
    'digits_between'       => ':attribute 必须为:min到:max位数。',
    'email'                => ':attribute 必须是有效的电子邮件地址。',
    'filled'               => ':attribute 字段是必需的。',
    'exists'               => '选中的 :attribute 无效。',
    'image'                => ':attribute 必须是一个图片。',
    'in'                   => '选中的 :attribute 无效。',
    'integer'              => ':attribute 必须是一个整数。',
    'ip'                   => ':attribute 必须是一个有效的IP地址。',
    'max'                  => [
        'numeric' => ':attribute 不能超过:max。',
        'file'    => ':attribute 不能超过:max KB。',
        'string'  => ':attribute 不能超过:max个字符。',
        'array'   => ':attribute 不能有超过:max项。',
    ],
    'mimes'                => ':attribute 必须是 :values 类型的文件。',
    'min'                  => [
        'numeric' => ':attribute 至少为:min。',
        'file'    => ':attribute 至少为:min KB。',
        'string'  => ':attribute 至少为:min个字符。',
        'array'   => ':attribute 至少有:min项。',
    ],
    'not_in'               => '选中的 :attribute 无效。',
    'numeric'              => ':attribute 必须是一个数。',
    'regex'                => ':attribute 格式无效。',
    'required'             => ':attribute 字段是必需的。',
    'required_if'          => '当:other为:value时，:attribute 字段是必需的。',
    'required_with'        => '当:values存在时，:attribute 字段是必需的。',
    'required_with_all'    => '当:values存在时，:attribute 字段是必需的。',
    'required_without'     => '当:values不存在时，:attribute 字段是必需的。',
    'required_without_all' => '当:values均不存在时，:attribute 字段是必需的。',
    'same'                 => ':attribute 与 :other 必须匹配。',
    'size'                 => [
        'numeric' => ':attribute 必须为:size。',
        'file'    => ':attribute 必须为:size KB。',
        'string'  => ':attribute 必须为:size个字符。',
        'array'   => ':attribute 必须包含:size项。',
    ],
    'string'               => ':attribute 必须是字符串。',
    'timezone'             => ':attribute 必须是有效的区域。',
    'unique'               => ':attribute 已经被使用。',
    'url'                  => ':attribute 格式无效。',

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
            'required_with' => '需要确认密码',
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
