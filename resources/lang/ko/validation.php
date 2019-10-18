<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute가 반드시 허용되어야 합니다.',
    'active_url'           => ':attribute가 올바른 URL이 아닙니다.',
    'after'                => ':attribute는 :date이후 날짜여야 합니다.',
    'alpha'                => ':attribute는 문자만 포함해야 합니다.',
    'alpha_dash'           => ':attribute는 문자, 숫자, 대시만 포함해야 합니다.',
    'alpha_num'            => ':attribute는 문자와 숫자만 포함됩니다.',
    'array'                => ':attribute는 배열이어야 합니다.',
    'before'               => ':attribute는 :date이전 날짜여야 합니다.',
    'between'              => [
        'numeric' => ':attribute는 반드시 :min이상 :max이하여야 합니다.',
        'file'    => ':attribute는 반드시 :min이상 :max kilobytes이하여야 합니다.',
        'string'  => ':attribute는 반드시 :min이상 :max 문자 이하여야 합니다.',
        'array'   => ':attribute는 반드시 :min이상 :max이하 항목이어야 합니다.',
    ],
    'boolean'              => ':attribute 는 true혹은 false값만 가능합니다.',
    'confirmed'            => ':attribute 확인이 일치하지 않습니다.',
    'date'                 => ':attribute 는 잘못된 날짜입니다.',
    'date_format'          => ':attribute 이 :format 포멧과 일치하지 않습니다.',
    'different'            => ':attribute 와 :other는 반드시 달라야 합니다.',
    'digits'               => ':attribute 는 반드시 :digits 숫자(digit)여야 합니다.',
    'digits_between'       => ':attribute 는 반드시 :min이상 :max이하 숫자여야 합니다.',
    'email'                => ':attribute 는 반드시 이메일 이어야 합니다.',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'filled'               => ':attribute 항목이 꼭 필요합니다.',
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
    'exists'               => '선택된 :attribute 은(는) 사용 불가합니다.',
    'image'                => ':attribute 는 반드시 이미지여야 합니다.',
    'image_extension'      => 'The :attribute must have a valid & supported image extension.',
    'in'                   => '선택된 :attribute 은(는) 사용 불가합니다.',
    'integer'              => ':attribute 는 반드시(integer)여야 합니다.',
    'ip'                   => ':attribute 는 반드시 IP주소 여야 합니다.',
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
        'numeric' => ':attribute :max 보다 크면 안됩니다.',
        'file'    => ':attribute :max kilobytes보다 크면 안됩니다.',
        'string'  => ':attribute :max 문자보다 길면 안됩니다.',
        'array'   => ':attribute :max 를 초과하면 안됩니다.',
    ],
    'mimes'                => ':attribute 은(는) 반드시 :values 타입이어야 합니다.',
    'min'                  => [
        'numeric' => ':attribute 은(는) 최소한 :min 이어야 합니다.',
        'file'    => ':attribute 은(는) 최소한 :min kilobytes여야 합니다.',
        'string'  => ':attribute 은(는) 최소한 :min 개 문자여야 합니다.',
        'array'   => ':attribute 은(는) 적어도 :min 개의 항목이어야 합니다.',
    ],
    'no_double_extension'  => 'The :attribute must only have a single file extension.',
    'not_in'               => '선택된 :attribute 는 사용할 수 없습니다',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => ':attribute 반드시 숫자여야 합니다.',
    'regex'                => ':attribute 포멧이 잘못되었습니다.',
    'required'             => ':attribute 항목은 필수입니다..',
    'required_if'          => ':attribute 은(는) :other 가 :value 일때 필수항목입니다.',
    'required_with'        => ':attribute 은(는) :values 가 있을때 필수항목입니다.',
    'required_with_all'    => ':attribute 은(는) :values 가 있을때 필수항목입니다.',
    'required_without'     => ':attribute 은(는) :values 가 없을때 필수항목입니다.',
    'required_without_all' => ':attribute 은(는) :values 가 전혀 없을때 필수항목입니다.',
    'same'                 => ':attribute 와 :other 은(는) 반드시 일치해야합니다.',
    'size'                 => [
        'numeric' => ':attribute 은(는) :size 여야합니다.',
        'file'    => ':attribute 은(는) :size kilobytes여야합니다.',
        'string'  => ':attribute 은(는) :size 문자여야합니다.',
        'array'   => ':attribute 은(는) :size 개 항목을 포함해야 합니다.',
    ],
    'string'               => ':attribute 문자열이어야 합니다.',
    'timezone'             => ':attribute 정상적인 지역(zone)이어야 합니다.',
    'unique'               => ':attribute 은(는) 이미 사용중입니다..',
    'url'                  => ':attribute 포멧이 사용 불가합니다.',
    'uploaded'             => 'The file could not be uploaded. The server may not accept files of this size.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => '비밀번호 확인이 필요합니다.',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
