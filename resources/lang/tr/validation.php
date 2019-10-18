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
    'active_url'           => ':attribute geçerli bir URL adresi değildir.',
    'after'                => ':attribute :date tarihinden sonra bir tarih olmalıdır.',
    'alpha'                => ':attribute sadece harflerden oluşabilir.',
    'alpha_dash'           => ':attribute sadece harf, rakam ve tirelerden oluşabilir.',
    'alpha_num'            => ':attribute sadece harf ve rakam oluşabilir.',
    'array'                => ':attribute array olmalıdır..',
    'before'               => ':attribute :date tarihinden önce bir tarih olmalıdır.',
    'between'              => [
        'numeric' => ':attribute, :min ve :max değerleri arasında olmalıdır.',
        'file'    => ':attribute, :min ve :max kilobyte boyutları arasında olmalıdır.',
        'string'  => ':attribute, :min ve :max karakter arasında olmalıdır.',
        'array'   => ':attribute :min ve :max öge arasında olmalıdır.',
    ],
    'boolean'              => ':attribute true veya false olmalıdır.',
    'confirmed'            => ':attribute doğrulaması eşleşmiyor.',
    'date'                 => ':attribute geçerli bir tarih değil.',
    'date_format'          => ':attribute formatı :format\'ına uymuyor.',
    'different'            => ':attribute be :other birbirinden farklı olmalıdır.',
    'digits'               => ':attribute :digits basamaklı olmalıdır.',
    'digits_between'       => ':attribute :min ve :max basamaklı olmalıdır.',
    'email'                => ':attribute geçerli bir e-mail adresi olmalıdır.',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'filled'               => ':attribute gerekli bir alandır.',
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
    'exists'               => 'Seçilen :attribute geçerli bir alan değildir.',
    'image'                => ':attribute bir görsel olmalıdır.',
    'image_extension'      => ':attribute geçerli ve desteklenen bir görsel uzantısı değildir.',
    'in'                   => 'Seçilen :attribute geçerli değildir.',
    'integer'              => ':attribute bir integer değeri olmalıdır.',
    'ip'                   => ':attribute geçerli bir IP adresi olmalıdır.',
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
        'numeric' => ':attribute, :max değerinden büyük olmamalıdır.',
        'file'    => ':attribute, :max kilobyte boyutundan büyük olmamalıdır.',
        'string'  => ':attribute, :max karakter boyutundan büyük olmamalıdır.',
        'array'   => ':attribute, en fazla :max öge içermelidir.',
    ],
    'mimes'                => ':attribute :values dosya tipinde olmalıdır.',
    'min'                  => [
        'numeric' => ':attribute, :min değerinden az olmamalıdır.',
        'file'    => ':attribute, :min kilobyte boyutundan küçük olmamalıdır.',
        'string'  => ':attribute, :min karakter boyutundan küçük olmamalıdır.',
        'array'   => ':attribute, en az :min öge içermelidir.',
    ],
    'no_double_extension'  => ':attribute sadece tek bir dosya tipinde olmalıdır.',
    'not_in'               => 'Seçili :attribute geçerli değildir.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => ':attribute rakam olmalıdır.',
    'regex'                => ':attribute formatı geçerli değildir.',
    'required'             => 'The :attribute field is required. :attribute alanı gereklidir.',
    'required_if'          => ':other alanı :value değerinde ise :attribute alanı gereklidir.',
    'required_with'        => 'Eğer :values değeri geçerli ise :attribute alanı gereklidir.',
    'required_with_all'    => 'Eğer :values değeri geçerli ise :attribute alanı gereklidir. ',
    'required_without'     => 'Eğer :values değeri geçerli değil ise :attribute alanı gereklidir.',
    'required_without_all' => 'Eğer :values değerlerinden hiçbiri geçerli değil ise :attribute alanı gereklidir.',
    'same'                 => ':attribute ve :other eşleşmelidir.',
    'size'                 => [
        'numeric' => ':attribute, :size boyutunda olmalıdır.',
        'file'    => ':attribute, :size kilobyte boyutunda olmalıdır.',
        'string'  => ':attribute, :size karakter uzunluğunda olmalıdır.',
        'array'   => ':attribute, :size sayıda öge içermelidir.',
    ],
    'string'               => ':attribute string olmalıdır.',
    'timezone'             => ':attribute geçerli bir alan olmalıdır.',
    'unique'               => ':attribute daha önce alınmış.',
    'url'                  => ':attribute formatı geçerli değil.',
    'uploaded'             => 'Dosya yüklemesi başarısız oldu. Server bu boyutta dosyaları kabul etmiyor olabilir.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Parola onayı gereklidir.',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
