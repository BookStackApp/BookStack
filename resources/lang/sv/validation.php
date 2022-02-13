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
    'after'                => ':attributet måste vara ett datum efter :date.',
    'alpha'                => ':attributet får bara innehålla bokstäver.',
    'alpha_dash'           => ':attributet får bara innehålla bokstäver, siffror och bindestreck.',
    'alpha_num'            => ':attributet får bara innehålla bokstäver och siffror.',
    'array'                => ':attributet måste vara en array.',
    'backup_codes'         => 'Den angivna koden är inte giltig eller har redan använts.',
    'before'               => ':attributet måste vara att datum före :date.',
    'between'              => [
        'numeric' => ':attributet måste vara mellan :min och :max.',
        'file'    => ':attributet måste vara mellan :min och :max kilobyte.',
        'string'  => ':attributet måste vara mellan :min och :max tecken.',
        'array'   => ':attributet måste innehålla mellan :min och :max poster.',
    ],
    'boolean'              => ':attributet måste vara sant eller falskt.',
    'confirmed'            => 'Bekräftelsen av :attributet stämmer inte.',
    'date'                 => ':attributet är inte ett giltigt datum.',
    'date_format'          => ':attributet matchar inte formatet :format.',
    'different'            => ':attributet och :other måste vara olika.',
    'digits'               => ':attributet måste vara :digits siffror.',
    'digits_between'       => ':attributet måste vara mellan :min och :max siffror.',
    'email'                => ':attributet måste vara en giltig e-postadress.',
    'ends_with' => ':attributet måste sluta med något av följande: :values',
    'file'                 => ':attributet måste anges som en giltig fil.',
    'filled'               => ':attributet är obligatoriskt.',
    'gt'                   => [
        'numeric' => ':attributet måste vara större än :value.',
        'file'    => ':attributet måste vara större än :value kilobyte.',
        'string'  => ':attributet måste vara större än :value tecken.',
        'array'   => ':attributet måste ha mer än :value poster.',
    ],
    'gte'                  => [
        'numeric' => ':attributet måste vara större än eller lika med :value.',
        'file'    => ':attributet måste vara större än eller lika med :value kilobyte.',
        'string'  => ':attributet måste vara större än eller lika med :value tecken.',
        'array'   => ':attributet måste ha :value objekt eller mer.',
    ],
    'exists'               => 'Valt :attribute är ogiltigt.',
    'image'                => ':attributet måste vara en bild.',
    'image_extension'      => ':attributet måste ha ett giltigt och supporterat bildformat.',
    'in'                   => 'Valt :attributet är ogiltigt.',
    'integer'              => ':attributet måste vara ett heltal.',
    'ip'                   => ':attributet måste vara en giltig IP-adress.',
    'ipv4'                 => ':attributet måste vara en giltig IPv4-adress.',
    'ipv6'                 => ':attributet måste vara en giltig IPv6-adress.',
    'json'                 => ':attributet måste vara en giltig JSON-sträng.',
    'lt'                   => [
        'numeric' => ':attributet måste vara mindre än :value.',
        'file'    => ':attributet måste vara mindre än :value kilobyte.',
        'string'  => ':attributet måste vara mindre än :value tecken.',
        'array'   => ':attributet måste ha mindre än :value poster.',
    ],
    'lte'                  => [
        'numeric' => ':attributet måste vara mindre än eller lika med :value.',
        'file'    => ':attributet måste vara mindre än eller lika med :value kilobyte.',
        'string'  => ':attributet måste vara mindre än eller lika med :value tecken.',
        'array'   => ':attributet får inte innehålla mer än :max poster.',
    ],
    'max'                  => [
        'numeric' => ':attribute får inte vara större än :max.',
        'file'    => ':attributet får inte vara större än :max kilobyte.',
        'string'  => ':attributet får inte vara längre än :max tecken.',
        'array'   => ':attributet får inte ha fler än :max poster.',
    ],
    'mimes'                => ':attributet måste vara en fil av typen: :values.',
    'min'                  => [
        'numeric' => ':attributet måste vara minst :min.',
        'file'    => ':attributet måste vara minst :min kilobyte.',
        'string'  => ':attributet måste vara minst :min tecken.',
        'array'   => ':attributet måste ha minst :min poster.',
    ],
    'not_in'               => 'Valt :attribut är ogiltig.',
    'not_regex'            => ':attributets format är ogiltigt.',
    'numeric'              => ':attributet måste vara ett nummer.',
    'regex'                => ':attributets format är ogiltigt.',
    'required'             => ':attributet är obligatoriskt.',
    'required_if'          => ':attributet är obligatoriskt när :other är :value.',
    'required_with'        => ':attributet är obligatoriskt när :values finns.',
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
