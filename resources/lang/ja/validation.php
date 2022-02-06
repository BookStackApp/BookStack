<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attributeに同意する必要があります。',
    'active_url'           => ':attributeは正しいURLではありません。',
    'after'                => ':attributeは:date以降である必要があります。',
    'alpha'                => ':attributeは文字のみが含められます。',
    'alpha_dash'           => ':attributeは文字, 数値, ハイフンのみが含められます。',
    'alpha_num'            => ':attributeは文字と数値のみが含められます。',
    'array'                => ':attributeは配列である必要があります。',
    'backup_codes'         => '提供されたコードは無効か、またはすでに使用されています。',
    'before'               => ':attributeは:date以前である必要があります。',
    'between'              => [
        'numeric' => ':attributeは:min〜:maxである必要があります。',
        'file'    => ':attributeは:min〜:maxキロバイトである必要があります。',
        'string'  => ':attributeは:min〜:max文字である必要があります。',
        'array'   => ':attributeは:min〜:max個である必要があります。',
    ],
    'boolean'              => ':attributeはtrueまたはfalseである必要があります。',
    'confirmed'            => ':attributeの確認が一致しません。',
    'date'                 => ':attributeは正しい日時ではありません。',
    'date_format'          => ':attributeが:formatのフォーマットと一致しません。',
    'different'            => ':attributeと:otherは異なる必要があります。',
    'digits'               => ':attributeは:digitsデジットである必要があります',
    'digits_between'       => ':attributeは:min〜:maxである必要があります。',
    'email'                => ':attributeは正しいEメールアドレスである必要があります。',
    'ends_with' => ':attributeは:valuesのいずれかで終わる必要があります。',
    'file'                 => 'The :attribute must be provided as a valid file.',
    'filled'               => ':attributeは必須です。',
    'gt'                   => [
        'numeric' => ':attributeは:valueより大きな値である必要があります。',
        'file'    => ':attributeは:valueキロバイトより大きなファイルである必要があります。',
        'string'  => ':attributeは:value文字より長い必要があります。',
        'array'   => ':attributeには:value個より多くのアイテムを指定する必要があります。',
    ],
    'gte'                  => [
        'numeric' => ':attributeは:value以上の値である必要があります。',
        'file'    => ':attributeは:valueキロバイト以上のファイルである必要があります。',
        'string'  => ':attributeは:value文字以上である必要があります。',
        'array'   => ':attributeには:value個以上のアイテムを指定する必要があります。',
    ],
    'exists'               => '選択された:attributeは不正です。',
    'image'                => ':attributeは画像である必要があります。',
    'image_extension'      => ':attributeは有効かつサポートされている拡張子の画像である必要があります。',
    'in'                   => '選択された:attributeは不正です。',
    'integer'              => ':attributeは数値である必要があります。',
    'ip'                   => ':attributeは正しいIPアドレスである必要があります。',
    'ipv4'                 => ':attributeは有効なIPv4アドレスである必要があります。',
    'ipv6'                 => ':attributeは有効なIPv6アドレスである必要があります。',
    'json'                 => ':attributeは有効なJSON文字列である必要があります。',
    'lt'                   => [
        'numeric' => ':attributeは:valueより小さな値である必要があります。',
        'file'    => ':attributeは:valueキロバイトより小さなファイルである必要があります。',
        'string'  => ':attributeは:value文字より短い必要があります。',
        'array'   => ':attributeには:value個より少ないアイテムを指定する必要があります。',
    ],
    'lte'                  => [
        'numeric' => ':attributeは:value以下の値である必要があります。',
        'file'    => ':attributeは:valueキロバイト以下のファイルである必要があります。',
        'string'  => ':attributeは:value文字以下である必要があります。',
        'array'   => ':attributeには:value個以下のアイテムを指定する必要があります。',
    ],
    'max'                  => [
        'numeric' => ':attributeは:maxを越えることができません。',
        'file'    => ':attributeは:maxキロバイトを越えることができません。',
        'string'  => ':attributeは:max文字をこえることができません。',
        'array'   => ':attributeは:max個を越えることができません。',
    ],
    'mimes'                => ':attributeのファイルタイプは以下のみが許可されています: :values.',
    'min'                  => [
        'numeric' => ':attributeは:min以上である必要があります。',
        'file'    => ':attributeは:minキロバイト以上である必要があります。',
        'string'  => ':attributeは:min文字以上である必要があります。',
        'array'   => ':attributeは:min個以上である必要があります。',
    ],
    'not_in'               => '選択された:attributeは不正です。',
    'not_regex'            => ':attributeの形式は不正です。',
    'numeric'              => ':attributeは数値である必要があります。',
    'regex'                => ':attributeのフォーマットは不正です。',
    'required'             => ':attributeは必須です。',
    'required_if'          => ':otherが:valueである場合、:attributeは必須です。',
    'required_with'        => ':valuesが設定されている場合、:attributeは必須です。',
    'required_with_all'    => ':valuesが設定されている場合、:attributeは必須です。',
    'required_without'     => ':valuesが設定されていない場合、:attributeは必須です。',
    'required_without_all' => ':valuesが設定されていない場合、:attributeは必須です。',
    'same'                 => ':attributeと:otherは一致している必要があります。',
    'safe_url'             => '提供されたリンクは安全ではない可能性があります。',
    'size'                 => [
        'numeric' => ':attributeは:sizeである必要があります。',
        'file'    => ':attributeは:sizeキロバイトである必要があります。',
        'string'  => ':attributeは:size文字である必要があります。',
        'array'   => ':attributeは:size個である必要があります。',
    ],
    'string'               => ':attributeは文字列である必要があります。',
    'timezone'             => ':attributeは正しいタイムゾーンである必要があります。',
    'totp'                 => '提供されたコードが無効または期限切れです。',
    'unique'               => ':attributeは既に使用されています。',
    'url'                  => ':attributeのフォーマットは不正です。',
    'uploaded'             => 'ファイルをアップロードできませんでした。サーバーがこのサイズのファイルを受け付けていない可能性があります。',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'パスワードの確認は必須です。',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
