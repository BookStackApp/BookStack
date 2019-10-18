<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute musí byť akceptovaný.',
    'active_url'           => ':attribute nie je platná URL.',
    'after'                => ':attribute musí byť dátum po :date.',
    'alpha'                => ':attribute môže obsahovať iba písmená.',
    'alpha_dash'           => ':attribute môže obsahovať iba písmená, čísla a pomlčky.',
    'alpha_num'            => ':attribute môže obsahovať iba písmená a čísla.',
    'array'                => ':attribute musí byť pole.',
    'before'               => ':attribute musí byť dátum pred :date.',
    'between'              => [
        'numeric' => ':attribute musí byť medzi :min a :max.',
        'file'    => ':attribute musí byť medzi :min a :max kilobajtmi.',
        'string'  => ':attribute musí byť medzi :min a :max znakmi.',
        'array'   => ':attribute musí byť medzi :min a :max položkami.',
    ],
    'boolean'              => ':attribute pole musí byť true alebo false.',
    'confirmed'            => ':attribute potvrdenie nesedí.',
    'date'                 => ':attribute nie je platný dátum.',
    'date_format'          => ':attribute nesedí s formátom :format.',
    'different'            => ':attribute a :other musia byť rozdielne.',
    'digits'               => ':attribute musí mať :digits číslic.',
    'digits_between'       => ':attribute musí mať medzi :min a :max číslicami.',
    'email'                => ':attribute musí byť platná emailová adresa.',
    'ends_with' => '',
    'filled'               => 'Políčko :attribute je povinné.',
    'gt'                   => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'gte'                  => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'exists'               => 'Vybraný :attribute nie je platný.',
    'image'                => ':attribute musí byť obrázok.',
    'image_extension'      => '',
    'in'                   => 'Vybraný :attribute je neplatný.',
    'integer'              => ':attribute musí byť celé číslo.',
    'ip'                   => ':attribute musí byť platná IP adresa.',
    'ipv4'                 => '',
    'ipv6'                 => '',
    'json'                 => '',
    'lt'                   => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'lte'                  => [
        'numeric' => '',
        'file'    => '',
        'string'  => '',
        'array'   => '',
    ],
    'max'                  => [
        'numeric' => ':attribute nesmie byť väčší ako :max.',
        'file'    => ':attribute nesmie byť väčší ako :max kilobajtov.',
        'string'  => ':attribute nesmie byť dlhší ako :max znakov.',
        'array'   => ':attribute nesmie mať viac ako :max položiek.',
    ],
    'mimes'                => ':attribute musí byť súbor typu: :values.',
    'min'                  => [
        'numeric' => ':attribute musí byť aspoň :min.',
        'file'    => ':attribute musí mať aspoň :min kilobajtov.',
        'string'  => ':attribute musí mať aspoň :min znakov.',
        'array'   => ':attribute musí mať aspoň :min položiek.',
    ],
    'no_double_extension'  => '',
    'not_in'               => 'Vybraný :attribute je neplatný.',
    'not_regex'            => '',
    'numeric'              => ':attribute musí byť číslo.',
    'regex'                => ':attribute formát je neplatný.',
    'required'             => 'Políčko :attribute je povinné.',
    'required_if'          => 'Políčko :attribute je povinné ak :other je :value.',
    'required_with'        => 'Políčko :attribute je povinné ak :values existuje.',
    'required_with_all'    => 'Políčko :attribute je povinné ak :values existuje.',
    'required_without'     => 'Políčko :attribute je povinné aj :values neexistuje.',
    'required_without_all' => 'Políčko :attribute je povinné ak ani jedno z :values neexistuje.',
    'same'                 => ':attribute a :other musia byť rovnaké.',
    'size'                 => [
        'numeric' => ':attribute musí byť :size.',
        'file'    => ':attribute musí mať :size kilobajtov.',
        'string'  => ':attribute musí mať :size znakov.',
        'array'   => ':attribute musí obsahovať :size položiek.',
    ],
    'string'               => ':attribute musí byť reťazec.',
    'timezone'             => ':attribute musí byť plantá časová zóna.',
    'unique'               => ':attribute je už použité.',
    'url'                  => ':attribute formát je neplatný.',
    'uploaded'             => '',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Vyžaduje sa potvrdenie hesla',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
