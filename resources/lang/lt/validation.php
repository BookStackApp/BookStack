<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute turi būti priimtas.',
    'active_url'           => ':attribute nėra tinkamas URL.',
    'after'                => ':attribute turi būti data po :date.',
    'alpha'                => ':attribute turi būti sudarytis tik iš raidžių.',
    'alpha_dash'           => ':attribute turi būti sudarytas tik iš raidžių, skaičių, brūkšnelių ir pabraukimų.',
    'alpha_num'            => ':attribute turi būti sudarytas tik iš raidžių ir skaičių.',
    'array'                => ':attribute turi būti masyvas.',
    'backup_codes'         => 'The provided code is not valid or has already been used.',
    'before'               => ':attribute turi būti data anksčiau negu :date.',
    'between'              => [
        'numeric' => ':attribute turi būti tarp :min ir :max.',
        'file'    => ':attribute turi būti tarp :min ir :max kilobaitų.',
        'string'  => ':attribute turi būti tarp :min ir :max simbolių.',
        'array'   => ':attribute turi turėti tarp :min ir :max elementų.',
    ],
    'boolean'              => ':attribute laukas turi būti tiesa arba melas.',
    'confirmed'            => ':attribute patvirtinimas nesutampa.',
    'date'                 => ':attribute nėra tinkama data.',
    'date_format'          => ':attribute neatitinka formato :format.',
    'different'            => ':attribute ir :other turi būti skirtingi.',
    'digits'               => ':attribute turi būti :digits skaitmenų.',
    'digits_between'       => ':attribute turi būti tarp :min ir :max skaitmenų.',
    'email'                => ':attribute turi būti tinkamas elektroninio pašto adresas.',
    'ends_with' => ':attribute turi pasibaigti vienu iš šių: :values',
    'filled'               => ':attribute laukas yra privalomas.',
    'gt'                   => [
        'numeric' => ':attribute turi būti didesnis negu :value.',
        'file'    => ':attribute turi būti didesnis negu :value kilobaitai.',
        'string'  => ':attribute turi būti didesnis negu :value simboliai.',
        'array'   => ':attribute turi turėti daugiau negu :value elementus.',
    ],
    'gte'                  => [
        'numeric' => ':attribute turi būti didesnis negu arba lygus :value.',
        'file'    => ':attribute turi būti didesnis negu arba lygus :value kilobaitams.',
        'string'  => ':attribute turi būti didesnis negu arba lygus :value simboliams.',
        'array'   => ':attribute turi turėti :value elementus arba daugiau.',
    ],
    'exists'               => 'Pasirinktas :attribute yra klaidingas.',
    'image'                => ':attribute turi būti paveikslėlis.',
    'image_extension'      => ':attribute turi būti tinkamas ir palaikomas vaizdo plėtinys.',
    'in'                   => 'Pasirinktas :attribute yra klaidingas.',
    'integer'              => ':attribute turi būti sveikasis skaičius.',
    'ip'                   => ':attribute turi būti tinkamas IP adresas.',
    'ipv4'                 => ':attribute turi būti tinkamas IPv4 adresas.',
    'ipv6'                 => ':attribute turi būti tinkamas IPv6 adresas.',
    'json'                 => ':attribute turi būti tinkama JSON eilutė.',
    'lt'                   => [
        'numeric' => ':attribute turi būti mažiau negu :value.',
        'file'    => ':attribute turi būti mažiau negu :value kilobaitai.',
        'string'  => ':attribute turi būti mažiau negu :value simboliai.',
        'array'   => ':attribute turi turėti mažiau negu :value elementus.',
    ],
    'lte'                  => [
        'numeric' => ':attribute turi būti mažiau arba lygus :value.',
        'file'    => ':attribute turi būti mažiau arba lygus :value kilobaitams.',
        'string'  => ':attribute turi būti mažiau arba lygus :value simboliams.',
        'array'   => ':attribute negali turėti daugiau negu :value elementų.',
    ],
    'max'                  => [
        'numeric' => ':attribute negali būti didesnis negu :max.',
        'file'    => ':attribute negali būti didesnis negu :max kilobaitai.',
        'string'  => ':attribute negali būti didesnis negu :max simboliai.',
        'array'   => ':attribute negali turėti daugiau negu :max elementų.',
    ],
    'mimes'                => ':attribute turi būti tipo failas: :values.',
    'min'                  => [
        'numeric' => ':attribute turi būti mažiausiai :min.',
        'file'    => ':attribute turi būti mažiausiai :min kilobaitų.',
        'string'  => ':attribute turi būti mažiausiai :min simbolių.',
        'array'   => ':attribute turi turėti mažiausiai :min elementus.',
    ],
    'not_in'               => 'Pasirinktas :attribute yra klaidingas.',
    'not_regex'            => ':attribute formatas yra klaidingas.',
    'numeric'              => ':attribute turi būti skaičius.',
    'regex'                => ':attribute formatas yra klaidingas.',
    'required'             => ':attribute laukas yra privalomas.',
    'required_if'          => ':attribute laukas yra privalomas kai :other yra :value.',
    'required_with'        => ':attribute laukas yra privalomas kai :values yra.',
    'required_with_all'    => ':attribute laukas yra privalomas kai :values yra.',
    'required_without'     => ':attribute laukas yra privalomas kai nėra :values.',
    'required_without_all' => ':attribute laukas yra privalomas kai nėra nei vienos :values.',
    'same'                 => ':attribute ir :other turi sutapti.',
    'safe_url'             => 'Pateikta nuoroda gali būti nesaugi.',
    'size'                 => [
        'numeric' => ':attribute turi būti :size.',
        'file'    => ':attribute turi būti :size kilobaitų.',
        'string'  => ':attribute turi būti :size simbolių.',
        'array'   => ':attribute turi turėti :size elementus.',
    ],
    'string'               => ':attribute turi būti eilutė.',
    'timezone'             => ':attribute turi būti tinkama zona.',
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => ':attribute jau yra paimtas.',
    'url'                  => ':attribute formatas yra klaidingas.',
    'uploaded'             => 'Šis failas negali būti įkeltas. Serveris gali nepriimti tokio dydžio failų.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Reikalingas slaptažodžio patvirtinimas',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
