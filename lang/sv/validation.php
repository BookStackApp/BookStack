<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute måste godkännas.',
    'active_url'           => ':attribute är inte en giltig URL.',
    'after'                => ':attribute måste vara efter :date.',
    'alpha'                => ':attribute får bara innehålla bokstäver.',
    'alpha_dash'           => ':attribute får bara innehålla bokstäver, siffror och bindestreck.',
    'alpha_num'            => ':attribute får bara innehålla bokstäver och siffror.',
    'array'                => ':attribute måste vara en array.',
    'backup_codes'         => 'Den angivna koden är inte giltig eller har redan använts.',
    'before'               => ':attribute måste vara före :date.',
    'between'              => [
        'numeric' => ':attribute måste vara mellan :min och :max.',
        'file'    => ':attribute måste vara mellan :min och :max kilobyte stor.',
        'string'  => ':attribute måste vara mellan :min och :max tecken.',
        'array'   => ':attribute måste innehålla mellan :min och :max poster.',
    ],
    'boolean'              => ':attribute måste vara sant eller falskt.',
    'confirmed'            => 'Bekräftelsen av :attribute stämmer inte.',
    'date'                 => ':attribute är inte ett giltigt datum.',
    'date_format'          => ':attribute matchar inte formatet :format.',
    'different'            => ':attribute och :other måste vara olika.',
    'digits'               => ':attribute måste vara :digits siffror.',
    'digits_between'       => ':attribute måste vara mellan :min och :max siffror.',
    'email'                => ':attribute måste vara en giltig e-postadress.',
    'ends_with' => ':attribute måste sluta med något av följande: :values',
    'file'                 => ':attribute måste anges som en giltig fil.',
    'filled'               => ':attribute är obligatoriskt.',
    'gt'                   => [
        'numeric' => ':attribute måste vara större än :value.',
        'file'    => ':attribute måste vara större än :value kilobytes.',
        'string'  => ':attribute måste vara större än :value tecken.',
        'array'   => ':attribute måste ha mer än :value objekt.',
    ],
    'gte'                  => [
        'numeric' => ':attribute måste vara större än eller likamed :value.',
        'file'    => ':attribute måste vara större än eller lika med :value kilobytes.',
        'string'  => ':attribute måste vara större än eller lika med :value tecken.',
        'array'   => ':attribute måste ha :value objekt eller mer.',
    ],
    'exists'               => 'Valt värde för :attribute är ogiltigt.',
    'image'                => ':attribute måste vara en bild.',
    'image_extension'      => ':attribute måste ha ett giltigt filtillägg.',
    'in'                   => 'Vald :attribute är ogiltigt.',
    'integer'              => ':attribute måste vara en integer.',
    'ip'                   => ':attribute måste vara en giltig IP-adress.',
    'ipv4'                 => ':attribute måste vara en giltig IPv4-adress.',
    'ipv6'                 => ':attribute måste vara en giltig IPv6-adress.',
    'json'                 => ':attribute måste vara en giltig JSON-sträng.',
    'lt'                   => [
        'numeric' => ':attribute måste vara mindre än :value.',
        'file'    => ':attribute måste vara mindre än :value kilobytes.',
        'string'  => ':attribute måste vara mindre än :value tecken.',
        'array'   => ':attribute måste ha mindre än :value objekt.',
    ],
    'lte'                  => [
        'numeric' => ':attribute måste vara mindre än eller lika :value.',
        'file'    => ':attribute måste vara mindre än eller lika med :value kilobytes.',
        'string'  => ':attribute måste vara mindre än eller lika med :value tecken.',
        'array'   => ':attribute får inte innehålla mer än :max objekt.',
    ],
    'max'                  => [
        'numeric' => ':attribute får inte vara större än :max.',
        'file'    => ':attribute får inte vara större än :max kilobyte.',
        'string'  => ':attribute får inte vara längre än :max tecken.',
        'array'   => ':attribute får inte ha fler än :max poster.',
    ],
    'mimes'                => ':attribute måste vara en fil av typen: :values.',
    'min'                  => [
        'numeric' => ':attribute måste vara minst :min.',
        'file'    => ':attribute måste vara minst :min kilobyte stor.',
        'string'  => ':attribute måste vara minst :min tecken.',
        'array'   => ':attribute måste ha minst :min poster.',
    ],
    'not_in'               => 'Vald :attribute är inte giltig',
    'not_regex'            => 'Formatet på :attribute är ogiltigt.',
    'numeric'              => ':attribute måste vara ett nummer.',
    'regex'                => ':attribute har ett ogiltigt format.',
    'required'             => ':attribute är obligatoriskt.',
    'required_if'          => ':attribute är obligatoriskt när :other är :value.',
    'required_with'        => ':attribute är obligatoriskt när :values finns.',
    'required_with_all'    => ':attribute är obligatoriskt när :values finns.',
    'required_without'     => ':attribute är obligatoriskt när :values inte finns.',
    'required_without_all' => ':attribute är obligatirskt när ingen av :values finns.',
    'same'                 => ':attribute och :other måste stämma överens.',
    'safe_url'             => 'Den angivna länken kanske inte är säker.',
    'size'                 => [
        'numeric' => ':attribute måste vara :size.',
        'file'    => ':attribute måste vara :size kilobyte.',
        'string'  => ':attribute måste vara :size tecken.',
        'array'   => ':attribute måste innehålla :size poster.',
    ],
    'string'               => ':attribute måste vara en sträng.',
    'timezone'             => ':attribute måste vara en giltig tidszon.',
    'totp'                 => 'Den angivna koden är inte giltig eller har löpt ut.',
    'unique'               => ':attribute är upptaget',
    'url'                  => 'Formatet på :attribute är ogiltigt.',
    'uploaded'             => 'Filen kunde inte laddas upp. Servern kanske inte tillåter filer med denna storlek.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Lösenordet måste bekräftas',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
