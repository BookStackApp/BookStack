<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute qabul qilinishi kerak.',
    'active_url'           => ':attribute qiymati to‘g‘ri URL emas.',
    'after'                => ':attribute qiymati :date sanadan kegingi sana bo‘lishi kerak.',
    'alpha'                => ':attribute qiymati faqat harflardan iborat bo‘lishi kerak.',
    'alpha_dash'           => ':attribute qiymati faqat harflar, raqamlar chiziqcha va ostki chiziqdan iborat bo‘lishi kerak.',
    'alpha_num'            => ':attribute qiymati faqat harflar va raqamlardan iborat bo‘lishi kerak.',
    'array'                => ':attribute qiymati massiv bo‘lishi kerak.',
    'backup_codes'         => 'Kiritilgan kod to‘g‘ri emas yoki ishlatib bo‘lingan.',
    'before'               => ':attribute qiymati :date sanadan oldingi sana bo‘lishi kerak.',
    'between'              => [
        'numeric' => ':attribute qiymati :min va :max orasida bo‘lishi kerak.',
        'file'    => ':attribute hajmi :min va :max kilobayt orasida bo‘lishi kerak.',
        'string'  => ':attribute uzunligi soni :min va :max orasida bo‘lishi kerak.',
        'array'   => ':attribute soni :min va :max orasida bo‘lishi kerak.',
    ],
    'boolean'              => ':attribute qiymati faqat «true» yoki «false» bo`lishi kerak.',
    'confirmed'            => ':attribute tasdiqlash qiymati mos emas.',
    'date'                 => ':attribute qiymati sana emas.',
    'date_format'          => ':attribute qiymati :format formatdagi sana emas.',
    'different'            => ':attribute va :other qiymatlari har xil bo‘lishi kerak.',
    'digits'               => ':attribute qiymati :digits raqamlarda iborat bo‘lishi kerak.',
    'digits_between'       => ':attribute qiymati :min va :max orasidagi raqamlarda iborat bo‘lishi kerak.',
    'email'                => ':attribute qiymati email bo‘lishi kerak.',
    'ends_with' => ':attribute qiymati quyidagilarda biri bo‘lishi kerak: :values ',
    'file'                 => ':attribute fayl bo‘lishi kerak.',
    'filled'               => ':attribute qiymatini kiritish majburiy.',
    'gt'                   => [
        'numeric' => ':attribute qiymati :value\'dan katta bo‘lishi kerak.',
        'file'    => ':attribute hajmi :value kilobaytdan katta bo‘lishi kerak.',
        'string'  => ':attribute uzunligi :value\'dan katta bo‘lishi kerak.',
        'array'   => ':attribute soni :value\'dan katta bo‘lishi kerak.',
    ],
    'gte'                  => [
        'numeric' => ':attribute qiymati :value\'dan katta yoki teng bo‘lishi kerak.',
        'file'    => ':attribute hajmi :value kilobaytdan katta yoki teng bo‘lishi kerak.',
        'string'  => ':attribute uzunligi :value\'dan katta yoki teng bo‘lishi kerak.',
        'array'   => ':attribute soni :value\'dan katta yoki teng bo‘lishi kerak.',
    ],
    'exists'               => ':attribute\'ning tanlangan qiymati to‘g‘ri emas.',
    'image'                => ':attribute rasm bo‘lishi kerak.',
    'image_extension'      => ':attribute rasm bo‘lishi va to‘g‘ri formatda bo‘lishi kerak.',
    'in'                   => ':attribute qiymati noto‘g‘ri.',
    'integer'              => ':attribute qiymati butun son bo‘lishi kerak.',
    'ip'                   => ':attribute qiymati IP manzil bo‘lishi kerak.',
    'ipv4'                 => ':attribute qiymati IPv4 manzil bo‘lishi kerak.',
    'ipv6'                 => ':attribute qiymati IPv6 manzil bo‘lishi kerak.',
    'json'                 => ':attribute qiymati JSON formatida bo‘lishi kerak.',
    'lt'                   => [
        'numeric' => ':attribute qiymati :value\'dan kichik bo‘lishi kerak.',
        'file'    => ':attribute hajmi :value kilobaytdan kichik bo‘lishi kerak.',
        'string'  => ':attribute uzunligi :value\'dan kichik bo‘lishi kerak.',
        'array'   => ':attribute soni :value\'dan kichik bo‘lishi kerak.',
    ],
    'lte'                  => [
        'numeric' => ':attribute qiymati :value\'dan kichik yoki teng bo‘lishi kerak.',
        'file'    => ':attribute hajmi :value kilobaytdan kichik yoki teng bo‘lishi kerak.',
        'string'  => ':attribute uzunligi :value\'dan kichik yoki teng bo‘lishi kerak.',
        'array'   => ':attribute soni :value\'dan kichik yoki teng bo‘lishi kerak.',
    ],
    'max'                  => [
        'numeric' => ':attribute qiymati maksimum :value bo‘lishi kerak.',
        'file'    => ':attribute hajmi maksimum :value kilobayt bo‘lishi kerak.',
        'string'  => ':attribute uzunligi maksimum :value bo‘lishi kerak.',
        'array'   => ':attribute soni maksimum :value bo‘lishi kerak.',
    ],
    'mimes'                => ':attribute fayl mime turi quyidagilardan biri bo‘lishi kerak: :values.',
    'min'                  => [
        'numeric' => ':attribute qiymati minimum :value bo‘lishi kerak.',
        'file'    => ':attribute hajmi minimum :value kilobayt bo‘lishi kerak.',
        'string'  => ':attribute uzunligi minimum :value bo‘lishi kerak.',
        'array'   => ':attribute soni minimum :value bo‘lishi kerak.',
    ],
    'not_in'               => 'selected :attribute qiymati noto‘g‘ri.',
    'not_regex'            => ':attribute formati noto‘g‘ri.',
    'numeric'              => ':attribute qiymati raqam bo‘lishi kerak.',
    'regex'                => ':attribute formati noto‘g‘ri.',
    'required'             => ':attribute\'ni kiritish majburiy.',
    'required_if'          => ':other qiymati :value bo‘lganda :attribute\'ni kiritish majburiy.',
    'required_with'        => ':values kiritilgan holatlarda :attribute\'ni kiritish majburiy.',
    'required_with_all'    => ':values kiritilgan holatlarda :attribute\'ni kiritish majburiy.',
    'required_without'     => ':values kiritilmagan holatlarda :attribute\'ni kiritish majburiy.',
    'required_without_all' => ':values kiritilmagan holatlarda :attribute\'ni kiritish majburiy.',
    'same'                 => ':attribute va :other qiymatlari teng bo‘lishi shart.',
    'safe_url'             => 'Kiritilgan manzil xavsiz emas.',
    'size'                 => [
        'numeric' => ':attribute qiymati :value bo‘lishi kerak.',
        'file'    => ':attribute hajmi :value kilobayt bo‘lishi kerak.',
        'string'  => ':attribute uzunligi :value bo‘lishi kerak.',
        'array'   => ':attribute soni :value bo‘lishi kerak.',
    ],
    'string'               => ':attribute qiymati matn bo‘lishi kerak.',
    'timezone'             => ':attribute qiymati to‘g‘ri vaqt zonasi bo‘lishi kerak.',
    'totp'                 => 'Kiritilgan xavsizlik kodi notpo‘g‘ri yoki eskirgan.',
    'unique'               => ':attribute qiymati allaqachon mavjud.',
    'url'                  => ':attribute URL formatida emas.',
    'uploaded'             => 'Faylni yuklashda xatolik. Server bunday hajmdagi faylllarni yuklamasligi mumkin.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Takroriy parolni to‘ldirish majburiy',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
