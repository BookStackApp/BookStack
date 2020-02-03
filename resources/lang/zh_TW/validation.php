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
    'ends_with' => '：attribute必須以下列之一結尾：：values',
    'filled'               => ':attribute 字段是必需的。',
    'gt'                   => [
        'numeric' => '：attribute必須大於：value。',
        'file'    => '：attribute必須大於：value千字節。',
        'string'  => '：attribute必須大於：value字符。',
        'array'   => '：attribute必須包含比：value多的項目。',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute必須大於或等於：value.',
        'file'    => 'The :attribute必須大於或等於：value千字節。',
        'string'  => 'The :attribute必須大於或等於：value字符。',
        'array'   => 'The :attribute必須具有：value項或更多。',
    ],
    'exists'               => '選中的 :attribute 無效。',
    'image'                => ':attribute 必須是一個圖片。',
    'image_extension'      => 'The :attribute必須具有有效且受支持的圖像擴展名.',
    'in'                   => '選中的 :attribute 無效。',
    'integer'              => ':attribute 必須是一個整數。',
    'ip'                   => ':attribute 必須是一個有效的IP位址。',
    'ipv4'                 => 'The :attribute必須是有效的IPv4地址。',
    'ipv6'                 => 'The :attribute必須是有效的IPv6地址。',
    'json'                 => 'The :attribute必須是有效的JSON字符串。',
    'lt'                   => [
        'numeric' => 'The :attribute必須小於：value。',
        'file'    => 'The :attribute必須小於：value千字節。',
        'string'  => 'The :attribute必須小於：value字符。',
        'array'   => 'The :attribute必須少於：value個項目。',
    ],
    'lte'                  => [
        'numeric' => 'The :attribute必須小於或等於：value。',
        'file'    => 'The :attribute必須小於或等於：value千字節。',
        'string'  => 'The :attribute必須小於或等於：value字符。',
        'array'   => 'The :attribute不得超過：value個項目。',
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
    'no_double_extension'  => 'The :attribute必須僅具有一個文件擴展名。',
    'not_in'               => '選中的 :attribute 無效。',
    'not_regex'            => 'The :attribute格式無效。',
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
    'uploaded'             => '無法上傳文件， 服務器可能不接受此大小的文件。',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => '需要確認密碼',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
