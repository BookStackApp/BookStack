<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute باید پذیرفته شده باشد.',
    'active_url'           => 'آدرس :attribute معتبر نیست.',
    'after'                => ':attribute باید تاریخی بعد از :date باشد.',
    'alpha'                => ':attribute باید فقط حروف الفبا باشد.',
    'alpha_dash'           => ':attribute باید فقط حروف الفبا، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num'            => ':attribute باید فقط حروف الفبا و اعداد باشد.',
    'array'                => ':attribute باید آرایه باشد.',
    'backup_codes'         => 'کد ارائه شده معتبر نیست یا قبلا استفاده شده است.',
    'before'               => ':attribute باید تاریخی قبل از :date باشد.',
    'between'              => [
        'numeric' => ':attribute باید بین :min و :max باشد.',
        'file'    => ':attribute باید بین :min و :max کیلوبایت باشد.',
        'string'  => ':attribute باید بین :min و :max کاراکتر باشد.',
        'array'   => ':attribute باید بین :min و :max آیتم باشد.',
    ],
    'boolean'              => 'فیلد :attribute فقط می‌تواند true و یا false باشد.',
    'confirmed'            => ':attribute با فیلد تکرار مطابقت ندارد.',
    'date'                 => ':attribute یک تاریخ معتبر نیست.',
    'date_format'          => ':attribute با الگوی :format مطابقت ندارد.',
    'different'            => ':attribute و :other باید از یکدیگر متفاوت باشند.',
    'digits'               => ':attribute باید :digits رقم باشد.',
    'digits_between'       => ':attribute باید بین :min و :max رقم باشد.',
    'email'                => ':attribute باید یک ایمیل معتبر باشد.',
    'ends_with' => 'فیلد :attribute باید با یکی از مقادیر زیر خاتمه یابد: :values',
    'filled'               => 'فیلد :attribute باید مقدار داشته باشد.',
    'gt'                   => [
        'numeric' => ':attribute باید بزرگتر از :value باشد.',
        'file'    => ':attribute باید بزرگتر از :value کیلوبایت باشد.',
        'string'  => ':attribute باید بیشتر از :value کاراکتر داشته باشد.',
        'array'   => ':attribute باید بیشتر از :value آیتم داشته باشد.',
    ],
    'gte'                  => [
        'numeric' => ':attribute باید بزرگتر یا مساوی :value باشد.',
        'file'    => ':attribute باید بزرگتر یا مساوی :value کیلوبایت باشد.',
        'string'  => ':attribute باید بیشتر یا مساوی :value کاراکتر داشته باشد.',
        'array'   => ':attribute باید بیشتر یا مساوی :value آیتم داشته باشد.',
    ],
    'exists'               => ':attribute انتخاب شده، معتبر نیست.',
    'image'                => ':attribute باید یک تصویر معتبر باشد.',
    'image_extension'      => ':attribute باید یک تصویر با فرمت معتبر باشد.',
    'in'                   => ':attribute انتخاب شده، معتبر نیست.',
    'integer'              => ':attribute باید عدد صحیح باشد.',
    'ip'                   => ':attribute باید آدرس IP معتبر باشد.',
    'ipv4'                 => ':attribute باید یک آدرس معتبر از نوع IPv4 باشد.',
    'ipv6'                 => ':attribute باید یک آدرس معتبر از نوع IPv6 باشد.',
    'json'                 => 'فیلد :attribute باید یک رشته از نوع JSON باشد.',
    'lt'                   => [
        'numeric' => ':attribute باید کوچکتر از :value باشد.',
        'file'    => ':attribute باید کوچکتر از :value کیلوبایت باشد.',
        'string'  => ':attribute باید کمتر از :value کاراکتر داشته باشد.',
        'array'   => ':attribute باید کمتر از :value آیتم داشته باشد.',
    ],
    'lte'                  => [
        'numeric' => ':attribute باید کوچکتر یا مساوی :value باشد.',
        'file'    => ':attribute باید کوچکتر یا مساوی :value کیلوبایت باشد.',
        'string'  => ':attribute باید کمتر یا مساوی :value کاراکتر داشته باشد.',
        'array'   => ':attribute باید کمتر یا مساوی :value آیتم داشته باشد.',
    ],
    'max'                  => [
        'numeric' => ':attribute نباید بزرگتر از :max باشد.',
        'file'    => ':attribute نباید بزرگتر از :max کیلوبایت باشد.',
        'string'  => ':attribute نباید بیشتر از :max کاراکتر داشته باشد.',
        'array'   => ':attribute نباید بیشتر از :max آیتم داشته باشد.',
    ],
    'mimes'                => 'فرمت‌های معتبر فایل عبارتند از: :values.',
    'min'                  => [
        'numeric' => ':attribute نباید کوچکتر از :min باشد.',
        'file'    => ':attribute نباید کوچکتر از :min کیلوبایت باشد.',
        'string'  => ':attribute نباید کمتر از :min کاراکتر داشته باشد.',
        'array'   => ':attribute نباید کمتر از :min آیتم داشته باشد.',
    ],
    'not_in'               => ':attribute انتخاب شده، معتبر نیست.',
    'not_regex'            => 'فرمت :attribute معتبر نیست.',
    'numeric'              => ':attribute باید عدد یا رشته‌ای از اعداد باشد.',
    'regex'                => 'فرمت :attribute معتبر نیست.',
    'required'             => 'فیلد :attribute الزامی است.',
    'required_if'          => 'هنگامی که :other برابر با :value است، فیلد :attribute الزامی است.',
    'required_with'        => 'در صورت وجود فیلد :values، فیلد :attribute نیز الزامی است.',
    'required_with_all'    => 'در صورت وجود فیلدهای :values، فیلد :attribute نیز الزامی است.',
    'required_without'     => 'در صورت عدم وجود فیلد :values، فیلد :attribute الزامی است.',
    'required_without_all' => 'در صورت عدم وجود هر یک از فیلدهای :values، فیلد :attribute الزامی است.',
    'same'                 => ':attribute و :other باید همانند هم باشند.',
    'safe_url'             => ':attribute معتبر نمی‌باشد.',
    'size'                 => [
        'numeric' => ':attribute باید برابر با :size باشد.',
        'file'    => ':attribute باید برابر با :size کیلوبایت باشد.',
        'string'  => ':attribute باید برابر با :size کاراکتر باشد.',
        'array'   => ':attribute باید شامل :size آیتم باشد.',
    ],
    'string'               => 'فیلد :attribute باید متن باشد.',
    'timezone'             => 'فیلد :attribute باید یک منطقه زمانی معتبر باشد.',
    'totp'                 => 'کد ارائه شده معتبر نیست یا منقضی شده است.',
    'unique'               => ':attribute قبلا انتخاب شده است.',
    'url'                  => ':attribute معتبر نمی‌باشد.',
    'uploaded'             => 'بارگذاری فایل :attribute موفقیت آمیز نبود.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'تایید کلمه عبور اجباری می باشد',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
