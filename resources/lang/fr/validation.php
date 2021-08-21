<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute doit être accepté.',
    'active_url'           => ':attribute n\'est pas une URL valide.',
    'after'                => ':attribute doit être supérieur à :date.',
    'alpha'                => ':attribute ne doit contenir que des lettres.',
    'alpha_dash'           => ':attribute doit contenir uniquement des lettres, chiffres et traits d\'union.',
    'alpha_num'            => ':attribute doit contenir uniquement des chiffres et des lettres.',
    'array'                => ':attribute doit être un tableau.',
    'backup_codes'         => 'The provided code is not valid or has already been used.',
    'before'               => ':attribute doit être inférieur à :date.',
    'between'              => [
        'numeric' => ':attribute doit être compris entre :min et :max.',
        'file'    => ':attribute doit être compris entre :min et :max kilobytes.',
        'string'  => ':attribute doit être compris entre :min et :max caractères.',
        'array'   => ':attribute doit être compris entre :min et :max éléments.',
    ],
    'boolean'              => ':attribute doit être vrai ou faux.',
    'confirmed'            => ':attribute la confirmation n\'est pas valide.',
    'date'                 => ':attribute n\'est pas une date valide.',
    'date_format'          => ':attribute ne correspond pas au format :format.',
    'different'            => ':attribute et :other doivent être différents l\'un de l\'autre.',
    'digits'               => ':attribute doit être de longueur :digits.',
    'digits_between'       => ':attribute doit avoir une longueur entre :min et :max.',
    'email'                => ':attribute doit être une adresse e-mail valide.',
    'ends_with' => ':attribute doit se terminer par une des valeurs suivantes : :values',
    'filled'               => ':attribute est un champ requis.',
    'gt'                   => [
        'numeric' => ':attribute doit être plus grand que :value.',
        'file'    => ':attribute doit être plus grand que :value kilobytes.',
        'string'  => ':attribute doit être plus grand que :value caractères.',
        'array'   => ':attribute doit avoir plus que :value éléments.',
    ],
    'gte'                  => [
        'numeric' => ':attribute doit être plus grand ou égal à :value.',
        'file'    => ':attribute doit être plus grand ou égal à :value kilobytes.',
        'string'  => ':attribute doit être plus grand ou égal à :value caractères.',
        'array'   => ':attribute doit avoir :value éléments ou plus.',
    ],
    'exists'               => 'L\'attribut :attribute est invalide.',
    'image'                => ':attribute doit être une image.',
    'image_extension'      => ':attribute doit avoir une extension d\'image valide et supportée.',
    'in'                   => 'L\'attribut :attribute est invalide.',
    'integer'              => ':attribute doit être un chiffre entier.',
    'ip'                   => ':attribute doit être une adresse IP valide.',
    'ipv4'                 => ':attribute doit être une adresse IPv4 valide.',
    'ipv6'                 => ':attribute doit être une adresse IPv6 valide.',
    'json'                 => ':attribute doit être une chaine JSON valide.',
    'lt'                   => [
        'numeric' => ':attribute doit être plus petit que :value.',
        'file'    => ':attribute doit être plus petit que :value kilobytes.',
        'string'  => ':attribute doit être plus petit que :value caractères.',
        'array'   => ':attribute doit avoir moins de :value éléments.',
    ],
    'lte'                  => [
        'numeric' => ':attribute doit être plus petit ou égal à :value.',
        'file'    => ':attribute doit être plus petit ou égal à :value kilobytes.',
        'string'  => ':attribute doit être plus petit ou égal à :value caractères.',
        'array'   => ':attribute ne doit pas avoir plus de :value éléments.',
    ],
    'max'                  => [
        'numeric' => ':attribute ne doit pas excéder :max.',
        'file'    => ':attribute ne doit pas excéder :max kilobytes.',
        'string'  => ':attribute ne doit pas excéder :max caractères.',
        'array'   => ':attribute ne doit pas contenir plus de :max éléments.',
    ],
    'mimes'                => ':attribute doit être un fichier de type :values.',
    'min'                  => [
        'numeric' => ':attribute doit être au moins :min.',
        'file'    => ':attribute doit faire au moins :min kilobytes.',
        'string'  => ':attribute doit contenir au moins :min caractères.',
        'array'   => ':attribute doit contenir au moins :min éléments.',
    ],
    'not_in'               => 'L\'attribut sélectionné :attribute est invalide.',
    'not_regex'            => ':attribute a un format invalide.',
    'numeric'              => ':attribute doit être un nombre.',
    'regex'                => ':attribute a un format invalide.',
    'required'             => ':attribute est un champ requis.',
    'required_if'          => ':attribute est requis si :other est :value.',
    'required_with'        => ':attribute est requis si :values est présent.',
    'required_with_all'    => ':attribute est requis si :values est présent.',
    'required_without'     => ':attribute est requis si:values n\'est pas présent.',
    'required_without_all' => ':attribute est requis si aucun des valeurs :values n\'est présente.',
    'same'                 => ':attribute et :other doivent être identiques.',
    'safe_url'             => 'Le lien fourni peut ne pas être sûr.',
    'size'                 => [
        'numeric' => ':attribute doit avoir la taille :size.',
        'file'    => ':attribute doit peser :size kilobytes.',
        'string'  => ':attribute doit contenir :size caractères.',
        'array'   => ':attribute doit contenir :size éléments.',
    ],
    'string'               => ':attribute doit être une chaîne de caractères.',
    'timezone'             => ':attribute doit être une zone valide.',
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => ':attribute est déjà utilisé.',
    'url'                  => ':attribute a un format invalide.',
    'uploaded'             => 'Le fichier n\'a pas pu être envoyé. Le serveur peut ne pas accepter des fichiers de cette taille.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'La confirmation du mot de passe est requise',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
