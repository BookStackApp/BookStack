<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => '必須同意 :attribute。',
    'active_url'           => ':attribute 並非有效的網址。',
    'after'                => ':attribute 必須是在 :date 後的日期。',
    'alpha'                => ':attribute 只能包含字母。',
    'alpha_dash'           => ':attribute 只能包含字母、數字、破折號與底線。',
    'alpha_num'            => ':attribute 只能包含字母和數字。',
    'array'                => ':attribute 必須是陣列。',
    'backup_codes'         => '提供的代碼無效或已被使用。',
    'before'               => ':attribute 必須是在 :date 前的日期。',
    'between'              => [
        'numeric' => ':attribute 必須在 :min 到 :max 之間。',
        'file'    => ':attribute 必須為 :min 到 :max KB。',
        'string'  => ':attribute 必須在 :min 到 :max 個字元之間。',
        'array'   => ':attribute 必須在 :min 到 :max 項之間。',
    ],
    'boolean'              => ':attribute 欄位必須為 true 或 false。',
    'confirmed'            => ':attribute 確認不符。',
    'date'                 => ':attribute 並非有效的日期。',
    'date_format'          => ':attribute 與 :format 格式不相符。',
    'different'            => ':attribute 和 :other 必須不同。',
    'digits'               => ':attribute 必須為 :digits 位數。',
    'digits_between'       => ':attribute 必須為 :min 到 :max 位數。',
    'email'                => ':attribute 必須是有效的電子郵件地址。',
    'ends_with' => ':attribute必須以下列之一結尾：:values',
    'file'                 => ':attribute 必須作為有效檔案提供。',
    'filled'               => ':attribute 欄位必填。',
    'gt'                   => [
        'numeric' => ':attribute 必須大於 :value。',
        'file'    => ':attribute 必須大於 :value KB。',
        'string'  => ':attribute 必須多於 :value 個字元。',
        'array'   => ':attribute 必須包含多於 :value 個項目。',
    ],
    'gte'                  => [
        'numeric' => ':attribute 必須大於或等於 :value。',
        'file'    => ':attribute 必須大於或等於 :value KB。',
        'string'  => ':attribute 必須多於或等於 :value 個字元。',
        'array'   => ':attribute 必須有 :value 或更多項。',
    ],
    'exists'               => '選定的 :attribute 無效。',
    'image'                => ':attribute 必須為圖片。',
    'image_extension'      => ':attribute 必須包含有效且受支援的圖片副檔名。',
    'in'                   => '選定的 :attribute 無效。',
    'integer'              => ':attribute 必須是整數。',
    'ip'                   => ':attribute 必須是有效的 IP 位置。',
    'ipv4'                 => ':attribute 必須是有效的 IPv4 位置。',
    'ipv6'                 => ':attribute 必須是有效的 IPv6 位置。',
    'json'                 => ':attribute 必須是有效的 JSON 字串。',
    'lt'                   => [
        'numeric' => ':attribute 必須小於 :value。',
        'file'    => ':attribute 必須小於 :value KB。',
        'string'  => ':attribute 必須少於 :value 個字元。',
        'array'   => ':attribute 必須少於 :value 個項目。',
    ],
    'lte'                  => [
        'numeric' => ':attribute 必須小於或等於 :value。',
        'file'    => ':attribute 必須小於或等於 :value KB。',
        'string'  => ':attribute 必須少於或等於 :value 個字元。',
        'array'   => ':attribute 不能超過 :value 個項目。',
    ],
    'max'                  => [
        'numeric' => ':attribute 不能超過 :max。',
        'file'    => ':attribute 不能超過 :max KB。',
        'string'  => ':attribute 不能超過 :max 個字元。',
        'array'   => ':attribute 不能有超過:max項。',
    ],
    'mimes'                => ':attribute 必須是 :values 類型的檔案。',
    'min'                  => [
        'numeric' => ':attribute 至少為:min。',
        'file'    => ':attribute 必須至少為:min KB。',
        'string'  => ':attribute 至少為:min個字元。',
        'array'   => ':attribute 至少有:min項。',
    ],
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
    'safe_url'             => '提供的連結可能不安全。',
    'size'                 => [
        'numeric' => ':attribute 必須為:size。',
        'file'    => ':attribute 必須為:size KB。',
        'string'  => ':attribute 必須為:size個字元。',
        'array'   => ':attribute 必須包含:size項。',
    ],
    'string'               => ':attribute 必須是字元串。',
    'timezone'             => ':attribute 必須是有效的區域。',
    'totp'                 => '提供的代碼無效或已過期。',
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
