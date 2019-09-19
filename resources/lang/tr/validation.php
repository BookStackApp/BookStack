<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute kabul edilmeli.',
    'active_url'           => ':attribute geçerli URL değil.',
    'after'                => ':attribute :date tarihinden sonraki bir tarih olmalı.',
    'alpha'                => ':attribute sadece harf içerebilir.',
    'alpha_dash'           => ':attribute sadece harfler, rakamlar, kısa çizgiler ve alt çizgiler içerebilir..',
    'alpha_num'            => ':attribute sadece harf ve rakam içerebilir.',
    'array'                => ':attribute bir dizi olmalıdır.',
    'before'               => ':attribute :date tarihinden önceki bir tarih olmalı.',
    'between'              => [
        'numeric' => ':attribute :min ile :max arasında olmalı.',
        'file'    => ':attribute :min ile :max kilobayt arasında olmalı.',
        'string'  => ':attribute :min ile :max karakter arasında olmalı.',
        'array'   => ':attribute :min and :max eleman arasında olmalı.',
    ],
    'boolean'              => ':attribute doğru (true) veya yanlış (false) olabilir.',
    'confirmed'            => ':attribute tekrar doğrulama uyuşmuyor.',
    'date'                 => ':attribute geçerli bir tarih değil.',
    'date_format'          => ':attribute :format formatı ile uyuşmuyor.',
    'different'            => ':attribute ve :other farklı olmalı.',
    'digits'               => ':attribute :digits basamak olmalı.',
    'digits_between'       => ':attribute :min ile :max basamak arasında olmalı.',
    'email'                => ':attribute geçerli bir e-posta adresi olmak zorunda.',
    'ends_with' => ':attribute şunlardan birisi ile bitmek zorunda: :values',
    'filled'               => ':attribute alanı zorunludur.',
    'gt'                   => [
        'numeric' => ':attribute :value değerinden büyük olmalıdır.',
        'file'    => ':attribute :value kilobayttan büyük olmalıdır.',
        'string'  => ':attribute :value karakterden büyük olmalıdır.',
        'array'   => ':attribute şu öğelerden daha fazla olmalı :value.',
    ],
    'gte'                  => [
        'numeric' => ':attribute :value değerinden daha büyük veya eşit olmalı.',
        'file'    => ':attribute :value kilobayttan daha büyük veya eşit olmalı.',
        'string'  => ':attribute :value karakterden daha büyük veya eşit olmalı.',
        'array'   => ':attribute şu öğeler kadar ya da daha fazla olmalı :value.',
    ],
    'exists'               => 'Seçili :attribute geçersiz.',
    'image'                => ':attribute bir resim olmalı.',
    'image_extension'      => ':attribute geçerli ve desteklenen bir resim uzantısı olmalı.',
    'in'                   => 'seçili :attribute geçersiz.',
    'integer'              => ':attribute  rakam olmalı.',
    'ip'                   => ':attribute  geçerli bir IP addresi olmalı.',
    'ipv4'                 => ':attribute  geçerli bir IPv4 addresi olmalı.',
    'ipv6'                 => ':attribute  geçerli bir IPv6 addresi olmalı.',
    'json'                 => ':attribute  geçerli bir JSON verisi olmalı.',
    'lt'                   => [
        'numeric' => ':attribute :value değerinden küçük olmalı.',
        'file'    => ':attribute :value kilobayttan küçük olmalı.',
        'string'  => ':attribute :value karakterden küçük olmalı.',
        'array'   => ':attribute şu öğelerden küçük olmalı :value.',
    ],
    'lte'                  => [
        'numeric' => ':attribute :value değerinden küçük veya eşit olmalı.',
        'file'    => ':attribute :value kilobayttan küçük veya eşit olmalı.',
        'string'  => ':attribute :value karakterden küçük veya eşit olmalı.',
        'array'   => ':attribute şu öğeler kadar ya da daha az olmalı :value.',
    ],
    'max'                  => [
        'numeric' => ':attribute :max değerinden daha büyük olamaz.',
        'file'    => ':attribute :max kilobayttan daha büyük olamaz.',
        'string'  => ':attribute :max karakterden daha büyük olamaz.',
        'array'   => ':attribute şu öğelerden daha fazla olamaz :max items.',
    ],
    'mimes'                => ':attribute şu türde bir dosya olmalı: :values.',
    'min'                  => [
        'numeric' => ':attribute en az :min olmalı.',
        'file'    => ':attribute en az :min kilobayt olmalı.',
        'string'  => ':attribute en az :min karakter olmalı.',
        'array'   => ':attribute en az şu değerlerde olmalı :min.',
    ],
    'no_double_extension'  => ':attribute sadece bir uzantıya sahip olabilir.',
    'not_in'               => 'seçili :attribute değeri geçersiz.',
    'not_regex'            => ':attribute biçimi geçersiz.',
    'numeric'              => ':attribute bir numara olmalı.',
    'regex'                => ':attribute biçimi geçersiz.',
    'required'             => ':attribute gerekli bir alandır.',
    'required_if'          => ':attribute alanı :other, :value olduğunda gereklidir.',
    'required_with'        => ':attribute alanı :values ile birlikte gereklidir.',
    'required_with_all'    => ':attribute alanı :values ile birlikte gereklidir.',
    'required_without'     => ':attribute alanı :values yok iken gereklidir.',
    'required_without_all' => ':attribute :values değerlerinden birisi yok iken gereklidir.',
    'same'                 => ':attribute ve :other uyuşmak zorunda.',
    'size'                 => [
        'numeric' => ':attribute :size olmalı.',
        'file'    => ':attribute must be :size kilobayt olmalı.',
        'string'  => ':attribute must be :size karakter olmalı.',
        'array'   => ':attribute şunlardan birini içermeli :size.',
    ],
    'string'               => ':attribute dize olmalı.',
    'timezone'             => ':attribute geçerli bir zaman dilimi olmalı.',
    'unique'               => ':attribute zaten alınmış.',
    'url'                  => ':attribute biçimi geçersiz.',
    'uploaded'             => 'dosya yüklenemedi. Sunucu bu boyutta dosyaları kabul etmeyebilir.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Şifre onayı gerekli',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
