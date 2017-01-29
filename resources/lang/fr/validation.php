<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute doit être accepté.',
    'active_url'           => ':attribute n\'est pas une URL valide.',
    'after'                => ':attribute doit être supérieur à :date.',
    'alpha'                => ':attribute ne doit contenir que des lettres.',
    'alpha_dash'           => ':attribute doit contenir uniquement des lettres, chiffres et traits d\'union.',
    'alpha_num'            => ':attribute doit contenir uniquement des chiffres et des lettres.',
    'array'                => ':attribute doit être un tableau.',
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
    'filled'               => ':attribute est un champ requis.',
    'exists'               => 'L\'attribut :attribute est invalide.',
    'image'                => ':attribute doit être une image.',
    'in'                   => 'L\'attribut :attribute est invalide.',
    'integer'              => ':attribute doit être un chiffre entier.',
    'ip'                   => ':attribute doit être une adresse IP valide.',
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
    'numeric'              => ':attribute doit être un nombre.',
    'regex'                => ':attribute a un format invalide.',
    'required'             => ':attribute est un champ requis.',
    'required_if'          => ':attribute est requis si :other est :value.',
    'required_with'        => ':attribute est requis si :values est présent.',
    'required_with_all'    => ':attribute est requis si :values est présent.',
    'required_without'     => ':attribute est requis si:values n\'est pas présent.',
    'required_without_all' => ':attribute est requis si aucun des valeurs :values n\'est présente.',
    'same'                 => ':attribute et :other doivent être identiques.',
    'size'                 => [
        'numeric' => ':attribute doit avoir la taille :size.',
        'file'    => ':attribute doit peser :size kilobytes.',
        'string'  => ':attribute doit contenir :size caractères.',
        'array'   => ':attribute doit contenir :size éléments.',
    ],
    'string'               => ':attribute doit être une chaîne de caractères.',
    'timezone'             => ':attribute doit être une zone valide.',
    'unique'               => ':attribute est déjà utilisé.',
    'url'                  => ':attribute a un format invalide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'password-confirm' => [
            'required_with' => 'La confirmation du mot de passe est requise',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
