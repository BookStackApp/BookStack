<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute peab olema aktsepteeritud.',
    'active_url'           => ':attribute ei ole kehtiv URL.',
    'after'                => ':attribute peab olema kuupäev pärast :date.',
    'alpha'                => ':attribute võib sisaldada ainult tähti.',
    'alpha_dash'           => ':attribute võib sisaldada ainult tähti, numbreid, sidekriipse ja alakriipse.',
    'alpha_num'            => ':attribute võib sisaldada ainult tähti ja numbreid.',
    'array'                => ':attribute peab olema massiiv.',
    'backup_codes'         => 'Kood ei ole korrektne või on seda juba kasutatud.',
    'before'               => ':attribute peab olema kuupäev enne :date.',
    'between'              => [
        'numeric' => ':attribute peab jääma vahemikku :min ja :max.',
        'file'    => ':attribute peab olema :min ja :max kilobaidi vahel.',
        'string'  => ':attribute peab olema :min ja :max tähemärgi vahel.',
        'array'   => ':attribute peab olema :min ja :max elemendi vahel.',
    ],
    'boolean'              => ':attribute peab olema tõene või väär.',
    'confirmed'            => ':attribute kinnitus ei kattu.',
    'date'                 => ':attribute ei ole kehtiv kuupäev.',
    'date_format'          => ':attribute ei ühti formaadiga :format.',
    'different'            => ':attribute ja :other peavad olema erinevad.',
    'digits'               => ':attribute peab olema :digits-kohaline arv.',
    'digits_between'       => ':attribute peab olema :min ja :max numbri vahel.',
    'email'                => ':attribute peab olema kehtiv e-posti aadress.',
    'ends_with' => ':attribute lõpus peab olema üks järgmistest väärtustest: :values',
    'file'                 => ':attribute peab olema sobiv fail.',
    'filled'               => ':attribute väli on kohustuslik.',
    'gt'                   => [
        'numeric' => ':attribute peab olema suurem kui :value.',
        'file'    => ':attribute peab olema suurem kui :value kilobaiti.',
        'string'  => ':attribute peab sisaldama rohkem kui :value tähemärki.',
        'array'   => ':attribute peab sisaldama rohkem kui :value elementi.',
    ],
    'gte'                  => [
        'numeric' => ':attribute peab olema suurem kui või võrdne :value.',
        'file'    => ':attribute peab olema :value kilobaiti või rohkem.',
        'string'  => ':attribute peab sisaldama :value või rohkem tähemärki.',
        'array'   => ':attribute peab sisaldama :value või rohkem elementi.',
    ],
    'exists'               => 'Valitud :attribute on vigane.',
    'image'                => ':attribute peab olema pildifail.',
    'image_extension'      => ':attribute peab olema lubatud ja toetatud pildiformaadis.',
    'in'                   => 'Valitud :attribute on vigane.',
    'integer'              => ':attribute peab olema täisarv.',
    'ip'                   => ':attribute peab olema kehtiv IP-aadress.',
    'ipv4'                 => ':attribute peab olema kehtiv IPv4 aadress.',
    'ipv6'                 => ':attribute peab olema kehtiv IPv6 aadress.',
    'json'                 => ':attribute peab olema kehtiv JSON-vormingus tekst.',
    'lt'                   => [
        'numeric' => ':attribute peab olema väiksem kui :value.',
        'file'    => ':attribute peab olema väiksem kui :value kilobaiti.',
        'string'  => ':attribute peab sisaldama vähem kui :value tähemärki.',
        'array'   => ':attribute peab sisaldama vähem kui :value elementi.',
    ],
    'lte'                  => [
        'numeric' => ':attribute peab olema :value või vähem.',
        'file'    => ':attribute peab olema :value kilobaiti või vähem.',
        'string'  => ':attribute peab sisaldama :value või vähem tähemärki.',
        'array'   => ':attribute ei tohi sisaldada rohkem kui :value elementi.',
    ],
    'max'                  => [
        'numeric' => ':attribute ei tohi olla suurem kui :max.',
        'file'    => ':attribute ei tohi olla suurem kui :max kilobaiti.',
        'string'  => ':attribute ei tohi sisaldada rohkem kui :max tähemärki.',
        'array'   => ':attribute ei tohi sisaldada rohkem kui :max elementi.',
    ],
    'mimes'                => ':attribute peab olema seda tüüpi fail: :values.',
    'min'                  => [
        'numeric' => ':attribute peab olema vähemalt :min.',
        'file'    => ':attribute peab olema vähemalt :min kilobaiti.',
        'string'  => ':attribute peab sisaldama vähemalt :min tähemärki.',
        'array'   => ':attribute peab sisaldama vähemalt :min elementi.',
    ],
    'not_in'               => 'Valitud :attribute on vigane.',
    'not_regex'            => ':attribute on vigases formaadis.',
    'numeric'              => ':attribute peab olema arv.',
    'regex'                => ':attribute on vigases formaadis.',
    'required'             => ':attribute on kohustuslik.',
    'required_if'          => ':attribute on kohustuslik, kui :other on :value.',
    'required_with'        => ':attribute on kohustuslik, kui :values on olemas.',
    'required_with_all'    => ':attribute on kohustuslik, kui :values on olemas.',
    'required_without'     => ':attribute on kohustuslik, kui :values ei ole olemas.',
    'required_without_all' => ':attribute on kohustuslik, kui :values on valimata.',
    'same'                 => ':attribute ja :other peavad klappima.',
    'safe_url'             => 'Link ei pruugi olla turvaline.',
    'size'                 => [
        'numeric' => ':attribute peab olema :size.',
        'file'    => ':attribute peab olema :size kilobaiti.',
        'string'  => ':attribute peab sisaldama :size tähemärki.',
        'array'   => ':attribute peab sisaldama :size elemente.',
    ],
    'string'               => ':attribute peab olema string.',
    'timezone'             => ':attribute peab olema kehtiv ajavöönd.',
    'totp'                 => 'Kood ei ole korrektne või on aegunud.',
    'unique'               => ':attribute on juba võetud.',
    'url'                  => ':attribute on vigases formaadis.',
    'uploaded'             => 'Faili üleslaadimine ebaõnnestus. Server ei pruugi sellise suurusega faile vastu võtta.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Parooli kinnitus on nõutud',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
