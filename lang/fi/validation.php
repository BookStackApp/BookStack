<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute tulee hyväksyä.',
    'active_url'           => ':attribute ei ole kelvollinen URL.',
    'after'                => ':attribute tulee olla päiväyksen :date jälkeinen päiväys.',
    'alpha'                => ':attribute voi sisältää vain kirjaimia.',
    'alpha_dash'           => ':attribute voi sisältää vain kirjaimia, numeroita, yhdys- ja alaviivoja.',
    'alpha_num'            => ':attribute voi sisältää vain kirjaimia ja numeroita.',
    'array'                => ':attribute tulee olla taulukkomuuttuja.',
    'backup_codes'         => 'Annettu koodi ei ole kelvollinen tai se on jo käytetty.',
    'before'               => ':attribute päiväyksen tulee olla ennen :date.',
    'between'              => [
        'numeric' => ':attribute tulee olla välillä :min ja :max.',
        'file'    => ':attribute tulee olla :min - :max kilotavua.',
        'string'  => ':attribute tulee olla :min - :max merkkiä pitkä.',
        'array'   => ':attribute tulee sisältää :min - :max kohdetta.',
    ],
    'boolean'              => ':attribute tulee olla tosi tai epätosi.',
    'confirmed'            => ':attribute vahvistus ei täsmää.',
    'date'                 => ':attribute ei ole kelvollinen päiväys.',
    'date_format'          => ':attribute ei täsmää muodon :format kanssa.',
    'different'            => ':attribute ja :other tulee erota toisistaan.',
    'digits'               => ':attribute tulee olla :digits numeroa pitkä.',
    'digits_between'       => ':attribute tulee olla :min - :max numeroa.',
    'email'                => ':attribute tulee olla kelvollinen sähköpostiosoite.',
    'ends_with' => ':attribute arvon tulee päättyä johonkin seuraavista: :values',
    'file'                 => ':attribute tulee olla kelvollinen tiedosto.',
    'filled'               => 'Kenttä :attribute vaaditaan.',
    'gt'                   => [
        'numeric' => ':attribute tulee olla suurempi kuin :value.',
        'file'    => ':attribute tulee olla suurempi kuin :value kilotavua.',
        'string'  => ':attribute tulee olla suurempi kuin :value merkkiä.',
        'array'   => ':attribute tulee sisältää vähintään :value kohdetta.',
    ],
    'gte'                  => [
        'numeric' => ':attribute on oltava suurempi tai samansuuruinen kuin :value.',
        'file'    => ':attribute on oltava suurempi tai samansuuruinen kuin :value kilotavua.',
        'string'  => ':attribute on oltava suurempi tai samansuuruinen kuin :value merkkiä.',
        'array'   => ':attribute tulee sisältää vähintään :value kohdetta tai enemmän.',
    ],
    'exists'               => 'Valittu :attribute ei ole kelvollinen.',
    'image'                => ':attribute on oltava kuva.',
    'image_extension'      => ':-attribute tulee sisältää kelvollisen ja tuetun kuvan tiedostopäätteen.',
    'in'                   => 'Valittu :attribute ei ole kelvollinen.',
    'integer'              => ':attribute tulee olla kokonaisluku.',
    'ip'                   => ':attribute tulee olla kelvollinen IP-osoite.',
    'ipv4'                 => ':attribute tulee olla kelvollinen IPv4-osoite.',
    'ipv6'                 => ':attribute tulee olla kelvollinen IPv6 -osoite.',
    'json'                 => ':attribute tulee olla kelvollinen JSON-merkkijono.',
    'lt'                   => [
        'numeric' => ':attribute tulee olla vähemmän kuin :value.',
        'file'    => ':attribute tulee olla vähemmän kuin :value kilotavua.',
        'string'  => ':attribute tulee olla vähemmän kuin :value merkkiä.',
        'array'   => ':attribute tulee sisältää vähemmän kuin :value kohdetta.',
    ],
    'lte'                  => [
        'numeric' => ':attribute tulee olla vähemmän tai yhtä suuri kuin :value.',
        'file'    => ':attribute tulee olla vähemmän tai yhtä suuri kuin :value kilotavua.',
        'string'  => ':attribute tulee olla vähemmän tai yhtä suuri kuin :value merkkiä.',
        'array'   => ':attribute ei tule sisältää enempää kuin :value kohdetta.',
    ],
    'max'                  => [
        'numeric' => ':attribute ei saa olla suurempi kuin :max.',
        'file'    => ':attribute ei saa olla suurempi kuin :max kilotavua.',
        'string'  => ':attribute ei saa olla suurempi kuin :max merkkiä.',
        'array'   => ':attribute ei saa sisältää enempää kuin :max kohdetta.',
    ],
    'mimes'                => ':attribute tulee olla tiedosto jonka tyyppi on :values.',
    'min'                  => [
        'numeric' => ':attribute tulee olla vähintään :min.',
        'file'    => ':attribute tulee olla vähintään :min kilotavua.',
        'string'  => ':attribute tulee olla vähintään :min merkkiä.',
        'array'   => ':attribute tulee sisältää vähintään :min kohdetta.',
    ],
    'not_in'               => 'Valittu :attribute ei ole kelvollinen.',
    'not_regex'            => ':attribute muoto ei ole kelvollinen.',
    'numeric'              => ':attribute tulee olla numero.',
    'regex'                => ':attribute muoto ei ole kelvollinen.',
    'required'             => 'Kenttä :attribute vaaditaan.',
    'required_if'          => 'Kenttä :attribute vaaditaan, kun :other on :value.',
    'required_with'        => 'Kenttä :attribute vaaditaan, kun :values on määritettynä.',
    'required_with_all'    => 'Kenttä :attribute vaaditaan, kun :values on määritettynä.',
    'required_without'     => 'Kenttä :attribute vaaditaan, kun :values ei ole määritettynä.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'safe_url'             => 'The provided link may not be safe.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => 'The :attribute has already been taken.',
    'url'                  => 'The :attribute format is invalid.',
    'uploaded'             => 'The file could not be uploaded. The server may not accept files of this size.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Password confirmation required',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
