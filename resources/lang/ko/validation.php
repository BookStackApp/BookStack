<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute(을)를 허용하세요.',
    'active_url'           => ':attribute(을)를 유효한 주소로 구성하세요.',
    'after'                => ':attribute(을)를 :date 후로 설정하세요.',
    'alpha'                => ':attribute(을)를 문자로만 구성하세요.',
    'alpha_dash'           => ':attribute(을)를 문자, 숫자, -, _로만 구성하세요.',
    'alpha_num'            => ':attribute(을)를 문자, 숫자로만 구성하세요.',
    'array'                => ':attribute(을)를 배열로 구성하세요.',
    'backup_codes'         => 'The provided code is not valid or has already been used.',
    'before'               => ':attribute(을)를 :date 전으로 설정하세요.',
    'between'              => [
        'numeric' => ':attribute(을)를 :min~:max(으)로 구성하세요.',
        'file'    => ':attribute(을)를 :min~:max킬로바이트로 구성하세요.',
        'string'  => ':attribute(을)를 :min~:max바이트로 구성하세요.',
        'array'   => ':attribute(을)를 :min~:max개로 구성하세요.',
    ],
    'boolean'              => ':attribute(을)를 true나 false로만 구성하세요.',
    'confirmed'            => ':attribute(와)과 다릅니다.',
    'date'                 => ':attribute(을)를 유효한 날짜로 구성하세요.',
    'date_format'          => ':attribute(은)는 :format(와)과 다릅니다.',
    'different'            => ':attribute(와)과 :other(을)를 다르게 구성하세요.',
    'digits'               => ':attribute(을)를 :digits자리로 구성하세요.',
    'digits_between'       => ':attribute(을)를 :min~:max자리로 구성하세요.',
    'email'                => ':attribute(을)를 유효한 메일 주소로 구성하세요.',
    'ends_with' => ':attribute(을)를 :values(으)로 끝나게 구성하세요.',
    'file'                 => 'The :attribute must be provided as a valid file.',
    'filled'               => ':attribute(을)를 구성하세요.',
    'gt'                   => [
        'numeric' => ':attribute(을)를 :value(이)가 넘게 구성하세요.',
        'file'    => ':attribute(을)를 :value킬로바이트가 넘게 구성하세요.',
        'string'  => ':attribute(을)를 :value바이트가 넘게 구성하세요.',
        'array'   => ':attribute(을)를 :value개가 넘게 구성하세요.',
    ],
    'gte'                  => [
        'numeric' => ':attribute(을)를 적어도 :value(으)로 구성하세요.',
        'file'    => ':attribute(을)를 적어도 :value킬로바이트로 구성하세요.',
        'string'  => ':attribute(을)를 적어도 :value바이트로 구성하세요.',
        'array'   => ':attribute(을)를 적어도 :value개로 구성하세요..',
    ],
    'exists'               => '고른 :attribute(이)가 유효하지 않습니다.',
    'image'                => ':attribute(을)를 이미지로 구성하세요.',
    'image_extension'      => ':attribute(을)를 유효한 이미지 확장자로 구성하세요.',
    'in'                   => '고른 :attribute(이)가 유효하지 않습니다.',
    'integer'              => ':attribute(을)를 정수로 구성하세요.',
    'ip'                   => ':attribute(을)를 유효한 IP 주소로 구성하세요.',
    'ipv4'                 => ':attribute(을)를 유효한 IPv4 주소로 구성하세요.',
    'ipv6'                 => ':attribute(을)를 유효한 IPv6 주소로 구성하세요.',
    'json'                 => ':attribute(을)를 유효한 JSON으로 구성하세요.',
    'lt'                   => [
        'numeric' => ':attribute(을)를 :value(이)가 안 되게 구성하세요.',
        'file'    => ':attribute(을)를 :value킬로바이트가 안 되게 구성하세요.',
        'string'  => ':attribute(을)를 :value바이트가 안 되게 구성하세요.',
        'array'   => ':attribute(을)를 :value개가 안 되게 구성하세요.',
    ],
    'lte'                  => [
        'numeric' => ':attribute(을)를 많아야 :max(으)로 구성하세요.',
        'file'    => ':attribute(을)를 많아야 :max킬로바이트로 구성하세요.',
        'string'  => ':attribute(을)를 많아야 :max바이트로 구성하세요.',
        'array'   => ':attribute(을)를 많아야 :max개로 구성하세요.',
    ],
    'max'                  => [
        'numeric' => ':attribute(을)를 많아야 :max(으)로 구성하세요.',
        'file'    => ':attribute(을)를 많아야 :max킬로바이트로 구성하세요.',
        'string'  => ':attribute(을)를 많아야 :max바이트로 구성하세요.',
        'array'   => ':attribute(을)를 많아야 :max개로 구성하세요.',
    ],
    'mimes'                => ':attribute(을)를 :values 형식으로 구성하세요.',
    'min'                  => [
        'numeric' => ':attribute(을)를 적어도 :value(으)로 구성하세요.',
        'file'    => ':attribute(을)를 적어도 :value킬로바이트로 구성하세요.',
        'string'  => ':attribute(을)를 적어도 :value바이트로 구성하세요.',
        'array'   => ':attribute(을)를 적어도 :value개로 구성하세요..',
    ],
    'not_in'               => '고른 :attribute(이)가 유효하지 않습니다.',
    'not_regex'            => ':attribute(은)는 유효하지 않은 형식입니다.',
    'numeric'              => ':attribute(을)를 숫자로만 구성하세요.',
    'regex'                => ':attribute(은)는 유효하지 않은 형식입니다.',
    'required'             => ':attribute(을)를 구성하세요.',
    'required_if'          => ':other(이)가 :value일 때 :attribute(을)를 구성해야 합니다.',
    'required_with'        => ':values(이)가 있을 때 :attribute(을)를 구성해야 합니다.',
    'required_with_all'    => ':values(이)가 모두 있을 때 :attribute(을)를 구성해야 합니다.',
    'required_without'     => ':values(이)가 없을 때 :attribute(을)를 구성해야 합니다.',
    'required_without_all' => ':values(이)가 모두 없을 때 :attribute(을)를 구성해야 합니다.',
    'same'                 => ':attribute(와)과 :other(을)를 똑같이 구성하세요.',
    'safe_url'             => 'The provided link may not be safe.',
    'size'                 => [
        'numeric' => ':attribute(을)를 :size(으)로 구성하세요.',
        'file'    => ':attribute(을)를 :size킬로바이트로 구성하세요.',
        'string'  => ':attribute(을)를 :size바이트로 구성하세요.',
        'array'   => ':attribute(을)를 :size개로 구성하세요..',
    ],
    'string'               => ':attribute(을)를 문자로 구성하세요.',
    'timezone'             => ':attribute(을)를 유효한 시간대로 구성하세요.',
    'totp'                 => 'The provided code is not valid or has expired.',
    'unique'               => ':attribute(은)는 이미 있습니다.',
    'url'                  => ':attribute(은)는 유효하지 않은 형식입니다.',
    'uploaded'             => '파일 크기가 서버에서 허용하는 수치를 넘습니다.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => '같은 비밀번호를 다시 입력하세요.',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
