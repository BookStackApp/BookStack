<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute 需要被同意。',
    'active_url'           => ':attribute 並不是一個有效的網址',
    'after'                => ':attribute 必須是在 :date 後的日期。',
    'alpha'                => ':attribute 只能包含字母。',
    'alpha_dash'           => ':attribute 只能包含字母、數字和橫線。',
    'alpha_num'            => ':attribute 只能包含字母和數字。',
    'array'                => ':attribute 必須是陣列。',
    'before'               => ':attribute 必須是在 :date 前的日期。',
    'between'              => [
        'numeric' => ':attribute 必須在:min到:max之間。',
        'file'    => ':attribute 必須為:min到:max KB。',
        'string'  => ':attribute 必須在:min到:max個字元之間。',
        'array'   => ':attribute 必須在:min到:max項之間.',
    ],
    'boolean'              => ':attribute 字段必須為 true 或 false。',
    'confirmed'            => ':attribute 確認不符。',
    'date'                 => ':attribute 不是一個有效的日期。',
    'date_format'          => ':attribute 格式不符 :format。',
    'different'            => ':attribute 和 :other 必須不同。',
    'digits'               => ':attribute 必須為:digits位數。',
    'digits_between'       => ':attribute 必須為:min到:max位數。',
    'email'                => ':attribute 必須是有效的電子郵件位址。',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'filled'               => ':attribute 字段是必需的。',
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
    'exists'               => '選中的 :attribute 無效。',
    'image'                => ':attribute 必須是一個圖片。',
    'image_extension'      => 'The :attribute must have a valid & supported image extension.',
    'in'                   => '選中的 :attribute 無效。',
    'integer'              => ':attribute 必須是一個整數。',
    'ip'                   => ':attribute 必須是一個有效的IP位址。',
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
        'numeric' => ':attribute 不能超過:max。',
        'file'    => ':attribute 不能超過:max KB。',
        'string'  => ':attribute 不能超過:max個字元。',
        'array'   => ':attribute 不能有超過:max項。',
    ],
    'mimes'                => ':attribute 必須是 :values 類型的檔案。',
    'min'                  => [
        'numeric' => ':attribute 至少為:min。',
        'file'    => ':attribute 至少為:min KB。',
        'string'  => ':attribute 至少為:min個字元。',
        'array'   => ':attribute 至少有:min項。',
    ],
    'no_double_extension'  => 'The :attribute must only have a single file extension.',
    'not_in'               => '選中的 :attribute 無效。',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => ':attribute 必須是一個數。',
    'regex'                => ':attribute 格式無效。',
    'required'             => ':attribute 字段是必需的。',
    'required_if'          => '當:other為:value時，:attribute 字段是必需的。',
    'required_with'        => '當:values存在時，:attribute 字段是必需的。',
    'required_with_all'    => '當:values存在時，:attribute 字段是必需的。',
    'required_without'     => '當:values不存在時，:attribute 字段是必需的。',
    'required_without_all' => '當:values均不存在時，:attribute 字段是必需的。',
    'same'                 => ':attribute 與 :other 必須匹配。',
    'size'                 => [
        'numeric' => ':attribute 必須為:size。',
        'file'    => ':attribute 必須為:size KB。',
        'string'  => ':attribute 必須為:size個字元。',
        'array'   => ':attribute 必須包含:size項。',
    ],
    'string'               => ':attribute 必須是字元串。',
    'timezone'             => ':attribute 必須是有效的區域。',
    'unique'               => ':attribute 已經被使用。',
    'url'                  => ':attribute 格式無效。',
    'uploaded'             => 'The file could not be uploaded. The server may not accept files of this size.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => '需要確認密碼',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
