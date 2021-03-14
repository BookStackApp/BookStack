<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute ir jāapstiprina.',
    'active_url'           => ':attribute nav derīgs URL.',
    'after'                => ':attribute ir jābūt datumam pēc :date.',
    'alpha'                => ':attribute var saturēt tikai burtus.',
    'alpha_dash'           => ':attribute var saturēt tikai burtus, ciparus, domuzīmes un apakš svītras.',
    'alpha_num'            => ':attribute var saturēt tikai burtus un ciparus.',
    'array'                => ':attribute ir jābūt masīvam.',
    'before'               => ':attribute jābūt datumam pirms :date.',
    'between'              => [
        'numeric' => ':attribute jābūt starp :min un :max.',
        'file'    => ':attribute jābūt starp :min un :max kilobaitiem.',
        'string'  => ':attribute jābūt starp :min un :max rakstzīmēm.',
        'array'   => 'Atribūtam jābūt starp: min un: max vienumiem.',
    ],
    'boolean'              => ':attribute jābūt True vai False.',
    'confirmed'            => ':attribute apstiprinājums nesakrīt.',
    'date'                 => ':attribute nav derīgs datums.',
    'date_format'          => ':attribute neatbilst formātam :format.',
    'different'            => ':attribute un :other jābūt atšķirīgiem.',
    'digits'               => ':attribute jābūt :digits cipariem.',
    'digits_between'       => ':attribute jābūt starp :min un :max cipariem.',
    'email'                => ':attribute jābūt derīgai e-pasta adresei.',
    'ends_with' => ':attribute jābeidzas ar vienu no :values',
    'filled'               => ':attribute lauks ir obligāts.',
    'gt'                   => [
        'numeric' => ':attribute jābūt lielākam kā :value.',
        'file'    => ':attribute jābūt lielākam kā :value kilobaitiem.',
        'string'  => ':attribute jābūt lielākam kā :value rakstzīmēm.',
        'array'   => ':attribute jāsatur vairāk kā :value vienības.',
    ],
    'gte'                  => [
        'numeric' => ':attribute jābūt lielākam vai vienādam ar :value.',
        'file'    => ':attribute jābūt lielākam vai vienādam ar :value kilobaitiem.',
        'string'  => ':attribute jābūt lielākam vai vienādam ar :value rakstzīmēm.',
        'array'   => ':attribute jāsatur :value vai vairāk vienumus.',
    ],
    'exists'               => 'Izvēlētais :attribute ir nederīgs.',
    'image'                => ':attribute jābūt attēlam.',
    'image_extension'      => ':attribute jābūt derīgam un atbalstītam bildes paplašinājumam.',
    'in'                   => 'Iezīmētais :attribute ir nederīgs.',
    'integer'              => ':attribute ir jābūt veselam skaitlim.',
    'ip'                   => ':attribute jābūt derīgai IP adresei.',
    'ipv4'                 => ':attribute jābūt derīgai IPv4 adresei.',
    'ipv6'                 => ':attribute jābūt derīgai IPv6 adresei.',
    'json'                 => ':attribute jābūt derīgai JSON virknei.',
    'lt'                   => [
        'numeric' => ':attribute jābūt mazākam par :value.',
        'file'    => ':attribute jābūt mazāk kā :value kilobaitiem.',
        'string'  => ':attribute jābūt mazāk kā :value rakstzīmēm.',
        'array'   => ':attribute jāsatur mazāk kā :value vienības.',
    ],
    'lte'                  => [
        'numeric' => ':attribute jābūt mazākam vai vienādam ar :value.',
        'file'    => ':attribute jābūt mazākam vai vienādam ar :value kilobaitiem.',
        'string'  => ':attribute jābūt mazākam vai vienādam ar :value rakstzīmēm.',
        'array'   => ':attribute nedrīkst pārsniegt :value vienības.',
    ],
    'max'                  => [
        'numeric' => ':attribute nevar būt lielāks kā :max.',
        'file'    => ':attribute nedrīkst būt lielāks kā :max kilobaiti.',
        'string'  => ':attribute nedrīkst būt lielāks kā :max rakstzīmēm.',
        'array'   => ':attribute nedrīkst būt lielāks kā :max vienumi.',
    ],
    'mimes'                => ':attribute jābūt faila tipam: :values.',
    'min'                  => [
        'numeric' => ':attribute ir jābūt vismaz :min.',
        'file'    => ':attribute jābūt vismaz :min kilobaitiem.',
        'string'  => ':attribute ir jābūt vismaz :min rakstzīmēm.',
        'array'   => ':attribute ir jābūt vismaz :min vienībām.',
    ],
    'not_in'               => 'Izvēlētais: atribūts ir nederīgs.',
    'not_regex'            => ':attribute formāts nav derīgs.',
    'numeric'              => ':attribute ir jābūt skaitlim.',
    'regex'                => ':attribute formāts nav derīgs.',
    'required'             => ':attribute lauks ir obligāts.',
    'required_if'          => ':attribute lauks ir nepieciešams, kad :other ir :value.',
    'required_with'        => ':attribute lauks ir obligāts, ja ir :values.',
    'required_with_all'    => ':attribute lauks ir obligāts, ja ir :values.',
    'required_without'     => ':attribute lauks ir obligāts, ja nav :values.',
    'required_without_all' => ':attribute lauks ir obligāts, ja nav neviena no :values.',
    'same'                 => ':attribute un :other jāsakrīt.',
    'safe_url'             => 'Norādītā saite var būt nedroša.',
    'size'                 => [
        'numeric' => ':attribute ir jābūt :size.',
        'file'    => ':attribute jābūt :size kilobaiti.',
        'string'  => ':attribute jābūt :size rakstzīmēm.',
        'array'   => ':attribute jāsatur :size vienības.',
    ],
    'string'               => ':attribute jābūt teksta virknei.',
    'timezone'             => ':attribute jābūt derīgai zonai.',
    'unique'               => ':attribute jau ir aizņemts.',
    'url'                  => ':attribute formāts nav derīgs.',
    'uploaded'             => 'Fails netika ielādēts. Serveris nevar pieņemt šāda izmēra failus.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Nepieciešams paroles apstiprinājums',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
