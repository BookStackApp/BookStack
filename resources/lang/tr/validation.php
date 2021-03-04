<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute kabul edilmelidir.',
    'active_url'           => ':attribute, geçerli bir URL adresi değildir.',
    'after'                => ':attribute tarihi, :date tarihinden sonraki bir tarih olmalıdır.',
    'alpha'                => ':attribute sadece harflerden oluşabilir.',
    'alpha_dash'           => ':attribute sadece harf, rakam ve tirelerden oluşabilir.',
    'alpha_num'            => ':attribute sadece harflerden ve rakamlardan oluşabilir.',
    'array'                => ':attribute bir dizi olmalıdır.',
    'before'               => ':attribute tarihi, :date tarihinden önceki bir tarih olmalıdır.',
    'between'              => [
        'numeric' => ':attribute değeri, :min ve :max değerleri arasında olmalıdır.',
        'file'    => ':attribute, :min ve :max kilobyte boyutları arasında olmalıdır.',
        'string'  => ':attribute, :min ve :max karakter arasında olmalıdır.',
        'array'   => ':attribute, :min ve :max öge arasında olmalıdır.',
    ],
    'boolean'              => ':attribute değeri true veya false olmalıdır.',
    'confirmed'            => ':attribute doğrulaması eşleşmiyor.',
    'date'                 => ':attribute geçerli bir tarih değil.',
    'date_format'          => ':attribute formatı, :format formatına uymuyor.',
    'different'            => ':attribute ve :other birbirinden farklı olmalıdır.',
    'digits'               => ':attribute, :digits basamaklı olmalıdır.',
    'digits_between'       => ':attribute, en az :min ve en fazla :max basamaklı olmalıdır.',
    'email'                => ':attribute, geçerli bir e-posta adresi olmalıdır.',
    'ends_with' => ':attribute, şunlardan birisiyle bitmelidir: :values',
    'filled'               => ':attribute alanı zorunludur.',
    'gt'                   => [
        'numeric' => ':attribute, :max değerinden büyük olmalıdır.',
        'file'    => ':attribute, :value kilobayttan büyük olmalıdır.',
        'string'  => ':attribute, :value karakterden fazla olmalıdır.',
        'array'   => ':attribute, :value ögeden daha fazla öge içermelidir.',
    ],
    'gte'                  => [
        'numeric' => ':attribute, :value değerinden büyük veya bu değere eşit olmalıdır.',
        'file'    => ':attribute, en az :value kilobayt olmalıdır.',
        'string'  => ':attribute, en az :value karakter içermelidir.',
        'array'   => ':attribute, en az :value öge içermelidir.',
    ],
    'exists'               => 'Seçilen :attribute geçersiz.',
    'image'                => ':attribute, bir görsel olmalıdır.',
    'image_extension'      => ':attribute, geçerli ve desteklenen bir görsel uzantısına sahip olmalıdır.',
    'in'                   => 'Seçilen :attribute geçersizdir.',
    'integer'              => ':attribute, bir tam sayı olmalıdır.',
    'ip'                   => ':attribute, geçerli bir IP adresi olmalıdır.',
    'ipv4'                 => ':attribute, geçerli bir IPv4 adresi olmalıdır.',
    'ipv6'                 => ':attribute, geçerli bir IPv6 adresi olmalıdır.',
    'json'                 => ':attribute, geçerli bir JSON dizimi olmalıdır.',
    'lt'                   => [
        'numeric' => ':attribute, :value değerinden küçük olmalıdır.',
        'file'    => ':attribute, :value kilobayttan küçük olmalıdır.',
        'string'  => ':attribute, :value karakterden küçük olmalıdır.',
        'array'   => ':attribute, :value ögeden az olmalıdır.',
    ],
    'lte'                  => [
        'numeric' => ':attribute, en fazla :value değerine eşit olmalıdır.',
        'file'    => ':attribute, en fazla :value kilobayt olmalıdır.',
        'string'  => ':attribute, en fazla :value karakter içermelidir.',
        'array'   => ':attribute, en fazla :value öge içermelidir.',
    ],
    'max'                  => [
        'numeric' => ':attribute, :max değerinden büyük olmayabilir.',
        'file'    => ':attribute, :max kilobayttan büyük olmayabilir.',
        'string'  => ':attribute, :max karakterden daha fazla karakter içermiyor olabilir.',
        'array'   => ':attribute, :max ögeden daha fazla öge içermiyor olabilir.',
    ],
    'mimes'                => ':attribute, şu dosya tiplerinde olmalıdır: :values.',
    'min'                  => [
        'numeric' => ':attribute, :min değerinden az olmamalıdır.',
        'file'    => ':attribute, :min kilobayttan küçük olmamalıdır.',
        'string'  => ':attribute, en az :min karakter içermelidir.',
        'array'   => ':attribute, en az :min öge içermelidir.',
    ],
    'not_in'               => 'Seçili :attribute geçersiz.',
    'not_regex'            => ':attribute formatı geçersiz.',
    'numeric'              => ':attribute, bir sayı olmalıdır.',
    'regex'                => ':attribute formatı geçersiz.',
    'required'             => ':attribute alanı zorunludur.',
    'required_if'          => ':other alanının :value olması, :attribute alanını zorunlu kılar.',
    'required_with'        => ':values değerinin mevcudiyeti, :attribute alanını zorunlu kılar.',
    'required_with_all'    => ':values değerlerinin mevcudiyeti, :attribute alanını zorunlu kılar.',
    'required_without'     => ':values değerinin bulunmuyor olması, :attribute alanını zorunlu kılar.',
    'required_without_all' => ':values değerlerinden hiçbirinin bulunmuyor olması, :attribute alanını zorunlu kılar.',
    'same'                 => ':attribute ve :other eşleşmelidir.',
    'safe_url'             => 'Sağlanan bağlantı güvenli olmayabilir.',
    'size'                 => [
        'numeric' => ':attribute, :size boyutunda olmalıdır.',
        'file'    => ':attribute, :size kilobayt olmalıdır.',
        'string'  => ':attribute, :size karakter uzunluğunda olmalıdır.',
        'array'   => ':attribute, :size sayıda öge içermelidir.',
    ],
    'string'               => ':attribute, string olmalıdır.',
    'timezone'             => ':attribute, geçerli bir bölge olmalıdır.',
    'unique'               => ':attribute zaten alınmış.',
    'url'                  => ':attribute formatı geçersiz.',
    'uploaded'             => 'Dosya yüklemesi başarısız oldu. Sunucu, bu boyuttaki dosyaları kabul etmiyor olabilir.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Şifre onayı zorunludur',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
