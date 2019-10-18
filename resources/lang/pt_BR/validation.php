<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'O :attribute deve ser aceito.',
    'active_url'           => 'O :attribute não é uma URL válida.',
    'after'                => 'O :attribute deve ser uma data posterior à data :date.',
    'alpha'                => 'O :attribute deve conter apenas letras.',
    'alpha_dash'           => 'O :attribute deve conter apenas letras, números e traços.',
    'alpha_num'            => 'O :attribute deve conter apenas letras e números.',
    'array'                => 'O :attribute deve ser uma array.',
    'before'               => 'O :attribute deve ser uma data anterior à data :date.',
    'between'              => [
        'numeric' => 'O :attribute deve ter tamanho entre :min e :max.',
        'file'    => 'O :attribute deve ter entre :min e :max kilobytes.',
        'string'  => 'O :attribute deve ter entre :min e :max caracteres.',
        'array'   => 'O :attribute deve ter entre :min e :max itens.',
    ],
    'boolean'              => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'O campo :attribute de confirmação não é igual.',
    'date'                 => 'O campo :attribute não está em um formato de data válido.',
    'date_format'          => 'O campo :attribute não tem a formatação :format.',
    'different'            => 'O campo :attribute e o campo :other devem ser diferentes.',
    'digits'               => 'O campo :attribute deve ter :digits dígitos.',
    'digits_between'       => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'email'                => 'O campo :attribute deve ser um e-mail válido.',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'filled'               => 'O campo :attribute é requerido.',
    'gt'                   => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'string'  => 'The :attribute must be greater than :value characters.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'string'  => 'The :attribute must be greater than or equal :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'exists'               => 'O atributo :attribute selecionado não é válido.',
    'image'                => 'O campo :attribute deve ser uma imagem.',
    'image_extension'      => 'O campo :attribute deve ter uma extensão de imagem válida & suportada.',
    'in'                   => 'The selected :attribute is invalid.',
    'integer'              => 'O campo :attribute deve ser um número inteiro.',
    'ip'                   => 'O campo :attribute deve ser um IP válido.',
    'ipv4'                 => 'The :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'The :attribute must be a valid IPv6 address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'lt'                   => [
        'numeric' => 'The :attribute must be less than :value.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'string'  => 'The :attribute must be less than :value characters.',
        'array'   => 'The :attribute must have less than :value items.',
    ],
    'lte'                  => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'string'  => 'The :attribute must be less than or equal :value characters.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max'                  => [
        'numeric' => 'O valor para o campo :attribute não deve ser maior que :max.',
        'file'    => 'O valor para o campo :attribute não deve ter tamanho maior que :max kilobytes.',
        'string'  => 'O valor para o campo :attribute não deve ter mais que :max caracteres.',
        'array'   => 'O valor para o campo :attribute não deve ter mais que :max itens.',
    ],
    'mimes'                => 'O :attribute deve ser do tipo type: :values.',
    'min'                  => [
        'numeric' => 'O valor para o campo :attribute não deve ser menor que :min.',
        'file'    => 'O valor para o campo :attribute não deve ter tamanho menor que :min kilobytes.',
        'string'  => 'O valor para o campo :attribute não deve ter menos que :min caracteres.',
        'array'   => 'O valor para o campo :attribute não deve ter menos que :min itens.',
    ],
    'no_double_extension'  => 'O campo :attribute deve ter apenas uma extensão de arquivo.',
    'not_in'               => 'O campo selecionado :attribute é inválido.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => 'O campo :attribute deve ser um número.',
    'regex'                => 'O formato do campo :attribute é inválido.',
    'required'             => 'O campo :attribute é requerido.',
    'required_if'          => 'O campo :attribute é requerido quando o campo :other tem valor :value.',
    'required_with'        => 'O campo :attribute é requerido quando os valores :values estiverem presentes.',
    'required_with_all'    => 'O campo :attribute é requerido quando os valores :values estiverem presentes.',
    'required_without'     => 'O campo :attribute é requerido quando os valores :values não estiverem presentes.',
    'required_without_all' => 'O campo :attribute é requerido quando nenhum dos valores :values estiverem presentes.',
    'same'                 => 'O campo :attribute e o campo :other devem ser iguais.',
    'size'                 => [
        'numeric' => 'O tamanho do campo :attribute deve ser :size.',
        'file'    => 'O tamanho do arquivo :attribute deve ser de :size kilobytes.',
        'string'  => 'O tamanho do campo :attribute deve ser de :size caracteres.',
        'array'   => 'O campo :attribute deve conter :size itens.',
    ],
    'string'               => 'O campo :attribute deve ser uma string.',
    'timezone'             => 'O campo :attribute deve conter uma timezone válida.',
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
