<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'O campo :attribute deve ser aceito.',
    'active_url'           => 'O campo :attribute não é uma URL válida.',
    'after'                => 'O campo :attribute deve ser uma data posterior à data :date.',
    'alpha'                => 'O campo :attribute deve conter apenas letras.',
    'alpha_dash'           => 'O campo :attribute deve conter apenas letras, números, traços e underlines.',
    'alpha_num'            => 'O campo :attribute deve conter apenas letras e números.',
    'array'                => 'O campo :attribute deve ser uma array.',
    'backup_codes'         => 'O código fornecido não é válido ou já foi usado.',
    'before'               => 'O campo :attribute deve ser uma data anterior à data :date.',
    'between'              => [
        'numeric' => 'O campo :attribute deve estar entre :min e :max.',
        'file'    => 'O campo :attribute deve ter entre :min e :max kilobytes.',
        'string'  => 'O campo :attribute deve ter entre :min e :max caracteres.',
        'array'   => 'O campo :attribute deve ter entre :min e :max itens.',
    ],
    'boolean'              => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'O campo :attribute não é igual à sua confirmação.',
    'date'                 => 'O campo :attribute não está em um formato de data válido.',
    'date_format'          => 'O campo :attribute não tem a formatação :format.',
    'different'            => 'O campo :attribute e o campo :other devem ser diferentes.',
    'digits'               => 'O campo :attribute deve ter :digits dígitos.',
    'digits_between'       => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'email'                => 'O campo :attribute deve ser um e-mail válido.',
    'ends_with' => 'O campo :attribute deve terminar com um dos seguintes: :values',
    'filled'               => 'O campo :attribute é requerido.',
    'gt'                   => [
        'numeric' => 'O campo :attribute deve ser maior que :value.',
        'file'    => 'O campo :attribute deve ser maior que :value kilobytes.',
        'string'  => 'O campo :attribute deve ser maior que :value caracteres.',
        'array'   => 'O campo :attribute deve ter mais que :value itens.',
    ],
    'gte'                  => [
        'numeric' => 'O campo :attribute deve ser maior ou igual a :value.',
        'file'    => 'O campo :attribute deve ser maior ou igual a :value kilobytes.',
        'string'  => 'O campo :attribute deve ser maior ou igual a :value caracteres.',
        'array'   => 'O campo :attribute deve ter :value itens ou mais.',
    ],
    'exists'               => 'O campo :attribute selecionado não é válido.',
    'image'                => 'O campo :attribute deve ser uma imagem.',
    'image_extension'      => 'O campo :attribute deve ter uma extensão de imagem válida e suportada.',
    'in'                   => 'O campo :attribute selecionado não é válido.',
    'integer'              => 'O campo :attribute deve ser um número inteiro.',
    'ip'                   => 'O campo :attribute deve ser um endereço IP válido.',
    'ipv4'                 => 'O campo :attribute deve ser um endereço IPv4 válido.',
    'ipv6'                 => 'O campo :attribute deve ser um endereço IPv6 válido.',
    'json'                 => 'O campo :attribute deve ser uma string JSON válida.',
    'lt'                   => [
        'numeric' => 'O campo :attribute deve ser menor que :value.',
        'file'    => 'O campo :attribute deve ser menor que :value kilobytes.',
        'string'  => 'O campo :attribute deve ser menor que :value caracteres.',
        'array'   => 'O campo :attribute deve conter menos que :value itens.',
    ],
    'lte'                  => [
        'numeric' => 'O campo :attribute deve ser menor ou igual a :value.',
        'file'    => 'O campo :attribute deve ser menor ou igual a :value kilobytes.',
        'string'  => 'O campo :attribute deve ser menor ou igual a :value caracteres.',
        'array'   => 'O campo :attribute não deve conter mais que :value itens.',
    ],
    'max'                  => [
        'numeric' => 'O valor para o campo :attribute não deve ser maior que :max.',
        'file'    => 'O valor para o campo :attribute não deve ter tamanho maior que :max kilobytes.',
        'string'  => 'O valor para o campo :attribute não deve ter mais que :max caracteres.',
        'array'   => 'O valor para o campo :attribute não deve ter mais que :max itens.',
    ],
    'mimes'                => 'O campo :attribute deve ser do tipo type: :values.',
    'min'                  => [
        'numeric' => 'O campo :attribute não deve ser menor que :min.',
        'file'    => 'O campo :attribute não deve ter tamanho menor que :min kilobytes.',
        'string'  => 'O campo :attribute não deve ter menos que :min caracteres.',
        'array'   => 'O campo :attribute não deve ter menos que :min itens.',
    ],
    'not_in'               => 'O campo selecionado :attribute é inválido.',
    'not_regex'            => 'O formato do campo :attribute é inválido.',
    'numeric'              => 'O campo :attribute deve ser um número.',
    'regex'                => 'O formato do campo :attribute é inválido.',
    'required'             => 'O campo :attribute é requerido.',
    'required_if'          => 'O campo :attribute é requerido quando o campo :other tem valor :value.',
    'required_with'        => 'O campo :attribute é requerido quando os valores :values estiverem presentes.',
    'required_with_all'    => 'O campo :attribute é requerido quando os valores :values estiverem presentes.',
    'required_without'     => 'O campo :attribute é requerido quando os valores :values não estiverem presentes.',
    'required_without_all' => 'O campo :attribute é requerido quando nenhum dos valores :values estiverem presentes.',
    'same'                 => 'O campo :attribute e o campo :other devem ser iguais.',
    'safe_url'             => 'O link fornecido pode não ser seguro.',
    'size'                 => [
        'numeric' => 'O tamanho do campo :attribute deve ser :size.',
        'file'    => 'O tamanho do arquivo :attribute deve ser de :size kilobytes.',
        'string'  => 'O tamanho do campo :attribute deve ser de :size caracteres.',
        'array'   => 'O campo :attribute deve conter :size itens.',
    ],
    'string'               => 'O campo :attribute deve ser uma string.',
    'timezone'             => 'O campo :attribute deve conter uma timezone válida.',
    'totp'                 => 'O código fornecido não é válido ou expirou.',
    'unique'               => 'Já existe um campo/dado de nome :attribute.',
    'url'                  => 'O formato da URL :attribute é inválido.',
    'uploaded'             => 'O arquivo não pôde ser carregado. O servidor pode não aceitar arquivos deste tamanho.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Confirmação de senha requerida',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
