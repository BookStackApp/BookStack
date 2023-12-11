<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute mora biti potrjen.',
    'active_url'           => ':attribute ni veljaven URL.',
    'after'                => ':attribute mora biti datum po :date.',
    'alpha'                => ':attribute lahko vsebuje samo črke.',
    'alpha_dash'           => ':attribute lahko vsebuje samo ?rke, ?tevilke in ?rtice.',
    'alpha_num'            => ':attribute lahko vsebuje samo črke in številke.',
    'array'                => ':attribute mora biti niz.',
    'backup_codes'         => 'The provided code is not valid or has already been used.',
    'before'               => ':attribute mora biti datum pred :date.',
    'between'              => [
        'numeric' => ':attribute mora biti med :min in :max.',
        'file'    => ':attribute mora biti med :min in :max kilobajti.',
        'string'  => ':attribute mora biti med :min in :max znaki.',
        'array'   => ':attribute mora imeti med :min in :max elementov.',
    ],
    'boolean'              => ':attribute polje mora biti pravilno ali napačno.',
    'confirmed'            => ':attribute potrditev se ne ujema.',
    'date'                 => ':attribute ni veljaven datum.',
    'date_format'          => ':attribute se ne ujema z obliko :format.',
    'different'            => ':attribute in :other morata biti različna.',
    'digits'               => 'Atribut mora biti: števnik.',
    'digits_between'       => ':attribute mora biti med :min in :max števkami.',
    'email'                => ':attribute mora biti veljaven e-naslov.',
    'ends_with' => 'The :attribute se mora končati z eno od določenih: :vrednost/values',
    'file'                 => 'The :attribute must be provided as a valid file.',
    'filled'               => 'Polje ne sme biti prazno.',
    'gt'                   => [
        'numeric' => ':attribute mora biti večji kot :vrednost.',
        'file'    => ':attribute mora biti večji kot :vrednost kilobytes',
        'string'  => ':attribute mora biti večji kot :vrednost znakov',
        'array'   => ':attribute mora biti večji kot :vrednost znakov',
    ],
    'gte'                  => [
        'numeric' => ':attribute mora biti večji kot ali enak :vrednost.',
        'file'    => ':attribute mora biti večji kot ali enak :vrednost kilobytes',
        'string'  => ':attribute mora biti večji kot ali enak :vrednost znakov',
        'array'   => ':attribute mora imeti :vrednost znakov ali več',
    ],
    'exists'               => 'Izbrani atribut je neveljaven.',
    'image'                => ':attribute mora biti slika.',
    'image_extension'      => ':attribute mora imeti veljavno & podprto slikovno pripono',
    'in'                   => 'izbran :attribute je neveljaven.',
    'integer'              => ':attribute mora biti celo število.',
    'ip'                   => ':attribute mora biti veljaven IP naslov.',
    'ipv4'                 => ':attribute mora biti veljaven IPv4 naslov.',
    'ipv6'                 => ':attribute mora biti veljaven IPv6 naslov.',
    'json'                 => ':attribute mora biti veljavna JSON povezava.',
    'lt'                   => [
        'numeric' => ':attribute mora biti manj kot :vrednost.',
        'file'    => ':attribute mora biti manj kot :vrednost kilobytes',
        'string'  => ':attribute mora biti manj kot :vrednost znakov',
        'array'   => ':attribute mora imeti manj kot :vrednost znakov',
    ],
    'lte'                  => [
        'numeric' => ':attribute mora biti manj kot ali enak :vrednost.',
        'file'    => ':attribute mora biti manj kot ali enak :vrednost kilobytes',
        'string'  => ':attribute mora biti manj kot ali enak :vrednost znakov',
        'array'   => ':attribute ne sme imeti več kot :vrednost elementov',
    ],
    'max'                  => [
        'numeric' => ':attribute ne sme biti večja od :max.',
        'file'    => ':attribute ne sme biti večja od :max kilobytes.',
        'string'  => 'Atribut naj ne bo večji od: max znakov.',
        'array'   => ':attribute ne sme imeti več kot :max elementov.',
    ],
    'mimes'                => 'Atribut mora biti datoteka vrste:: vrednost.',
    'min'                  => [
        'numeric' => ':attribute mora biti najmanj :min.',
        'file'    => ':attribute mora biti najmanj :min KB.',
        'string'  => ':attribute mora biti najmanj :min znakov.',
        'array'   => ':attribute mora imeti vsaj :min elementov.',
    ],
    'not_in'               => 'Izbrani atribut je neveljaven.',
    'not_regex'            => ':attribute oblika ni veljavna.',
    'numeric'              => 'Atribut mora biti število.',
    'regex'                => ':attribute oblika ni veljavna.',
    'required'             => 'Polje :attribute je obvezno.',
    'required_if'          => 'Polje atributa je obvezno, če: drugo je: vrednost.',
    'required_with'        => 'Polje atributa je obvezno, ko: so prisotne vrednosti.',
    'required_with_all'    => 'Polje atributa je obvezno, ko: so prisotne vrednosti.',
    'required_without'     => 'Polje atributa je obvezno, če: vrednosti niso prisotne.',
    'required_without_all' => 'Polje atributa je obvezno, če nobena od: vrednosti ni prisotna.',
    'same'                 => 'Atribut in: drugi se morajo ujemati.',
    'safe_url'             => 'Podana povezava morda ni varna.',
    'size'                 => [
        'numeric' => ':attribute mora biti :velikost.',
        'file'    => ':attribute mora biti :velikost KB.',
        'string'  => 'Atribut mora biti: velikost znakov.',
        'array'   => ':attribute mora vsebovati :velikost elementov.',
    ],
    'string'               => ':attribute mora biti niz.',
    'timezone'             => ':attribute mora biti veljavna cona.',
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => ':attribute je že zaseden.',
    'url'                  => ':attribute oblika ni veljavna.',
    'uploaded'             => 'Datoteke ni bilo mogoče naložiti. Strežnik morda ne sprejema datotek te velikosti.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Potrditev gesla',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
