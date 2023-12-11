<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute trebuie să fie acceptat.',
    'active_url'           => ':attribute nu este un URL valid.',
    'after'                => ':attribute trebuie să fie o dată după :date.',
    'alpha'                => ':attribute poate conține doar litere.',
    'alpha_dash'           => ':attribute poate conține doar litere, numere, cratime și underscore.',
    'alpha_num'            => ':attribute poate conține doar litere și cifre.',
    'array'                => ':attribute trebuie să fie un array.',
    'backup_codes'         => 'Codul furnizat nu este valid sau a fost deja folosit.',
    'before'               => ':attribute trebuie să fie o dată înainte de :date.',
    'between'              => [
        'numeric' => ':attribute trebuie să fie între :min şi :max.',
        'file'    => ':attribute trebuie să aibă între :min şi :max kiloocteţi.',
        'string'  => ':attribute trebuie să aibă între :min şi :max caractere.',
        'array'   => ':attribute trebuie să aibă între :min şi :max elemente.',
    ],
    'boolean'              => 'Câmpul :attribute trebuie să fie adevărat sau fals.',
    'confirmed'            => 'Confirmarea :attribute nu se potrivește.',
    'date'                 => ':attribute nu este o dată validă.',
    'date_format'          => ':attribute nu se potrivește cu formatul :format.',
    'different'            => ':attribute și :other trebuie să fie diferite.',
    'digits'               => ':attribute trebuie să aibă :digits cifre.',
    'digits_between'       => ':attribute trebuie să aibă între :min şi :max cifre.',
    'email'                => ':attribute trebuie să fie o adresă de e-mail validă.',
    'ends_with' => ':attribute trebuie să se termine cu una dintre următoarele: :values',
    'file'                 => ':attribute trebuie să fie furnizat ca un fişier valid.',
    'filled'               => 'Câmpul :attribute este necesar.',
    'gt'                   => [
        'numeric' => ':attribute trebuie să fie mai mare decât :value.',
        'file'    => ':attribute trebuie să fie mai mare decât :value kilobytes.',
        'string'  => ':attribute trebuie să fie mai mare decât :value caractere.',
        'array'   => ':attribute trebuie să aibă mai mult de :value elemente.',
    ],
    'gte'                  => [
        'numeric' => ':attribute trebuie să fie mai mare sau egal cu :value.',
        'file'    => ':attribute trebuie să fie mai mare sau egal cu :value kilobytes.',
        'string'  => ':attribute trebuie să fie mai mare sau egal cu :value caractere.',
        'array'   => ':attribute trebuie să aibă :value elemente sau mai multe.',
    ],
    'exists'               => 'Atributul :attribute selectat nu este valid.',
    'image'                => ':attribute trebuie să fie o imagine.',
    'image_extension'      => ':attribute trebuie să aibă o extensie validă și suportată.',
    'in'                   => ':attribute selectat nu este valid.',
    'integer'              => ':attribute trebuie să fie un număr.',
    'ip'                   => ':attribute trebuie să fie o adresă IP validă.',
    'ipv4'                 => ':attribute trebuie să fie o adresă IPv4 validă.',
    'ipv6'                 => ':attribute trebuie să fie o adresă IPv6 validă.',
    'json'                 => ':attribute trebuie să fie un șir JSON valid.',
    'lt'                   => [
        'numeric' => ':attribute trebuie să fie mai mic decât :value.',
        'file'    => ':attribute trebuie să fie mai mic de :value kilobytes.',
        'string'  => ':attribute trebuie să aibă mai puţin de :value caractere.',
        'array'   => ':attribute trebuie să aibă mai puţin de :value elemente.',
    ],
    'lte'                  => [
        'numeric' => ':attribute trebuie să fie mai mic sau egal cu :value.',
        'file'    => ':attribute trebuie să fie mai mic sau egal cu :value kilobytes.',
        'string'  => ':attribute trebuie să fie mai mic sau egal cu :value caractere.',
        'array'   => ':attribute nu trebuie să aibă mai mult de :value elemente.',
    ],
    'max'                  => [
        'numeric' => ':attribute nu poate fi mai mare de :max.',
        'file'    => ':attribute nu poate fi mai mare de :max kilobytes.',
        'string'  => ':attribute nu poate avea mai mult de :max caractere.',
        'array'   => ':attribute nu poate avea mai mult de :max elemente.',
    ],
    'mimes'                => ':attribute trebuie să fie un fişier de tipul: :values.',
    'min'                  => [
        'numeric' => ':attribute trebuie să aibă cel puțin :min.',
        'file'    => ':attribute trebuie să aibă cel puțin :min kiloocteţi.',
        'string'  => ':attribute trebuie să aibă cel puțin :min caractere.',
        'array'   => ':attribute trebuie să aibă cel puțin :min elemente.',
    ],
    'not_in'               => 'Câmpul :attribute selectat nu este valid.',
    'not_regex'            => 'Câmpul :attribute nu este valid.',
    'numeric'              => ':attribute trebuie să fie un număr.',
    'regex'                => ':attribute nu este valid.',
    'required'             => ':attribute este necesar.',
    'required_if'          => 'Câmpul :attribute este obligatoriu atunci când :other este :value.',
    'required_with'        => 'Câmpul :attribute este necesar când :values este prezent.',
    'required_with_all'    => 'Câmpul :attribute este necesar când :values este prezent.',
    'required_without'     => 'Câmpul :attribute este obligatoriu atunci când :values nu este prezent.',
    'required_without_all' => 'Câmpul :attribute este necesar când niciuna dintre :values nu este prezentă.',
    'same'                 => ':attribute și :other trebuie să se potrivească.',
    'safe_url'             => 'Este posibil ca link-ul furnizat să nu fie sigur.',
    'size'                 => [
        'numeric' => ':attribute trebuie să fie :size.',
        'file'    => ':attribute trebuie să aibă :size kilobiți.',
        'string'  => ':attribute trebuie să aibă :size caractere.',
        'array'   => 'Câmpul :attribute trebuie să aibă :size elemente.',
    ],
    'string'               => ':attribute trebuie să fie un șir de caractere.',
    'timezone'             => ':attribute trebuie să fie o zonă validă.',
    'totp'                 => 'Codul furnizat nu este valid sau a expirat.',
    'unique'               => ':attribute a fost deja folosit.',
    'url'                  => ':attribute nu este valid.',
    'uploaded'             => 'Fişierul nu a putut fi încărcat. Serverul nu poate accepta fişiere de această dimensiune.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Este necesară confirmarea parolei',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
