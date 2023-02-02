<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute phải được chấp nhận.',
    'active_url'           => ':attribute không phải là một đường dẫn hợp lệ.',
    'after'                => ':attribute phải là một ngày sau :date.',
    'alpha'                => ':attribute chỉ được chứa chữ cái.',
    'alpha_dash'           => ':attribute chỉ được chứa chữ cái, chữ số, gạch nối và gạch dưới.',
    'alpha_num'            => ':attribute chỉ được chứa chữ cái hoặc chữ số.',
    'array'                => ':attribute phải là một mảng.',
    'backup_codes'         => 'Mã cung cấp không hợp lệ hoặc đã được sử dụng.',
    'before'               => ':attribute phải là một ngày trước :date.',
    'between'              => [
        'numeric' => ':attribute phải nằm trong khoảng :min đến :max.',
        'file'    => ':attribute phải nằm trong khoảng :min đến :max KB.',
        'string'  => ':attribute phải trong khoảng :min đến :max ký tự.',
        'array'   => ':attribute phải nằm trong khoảng :min đến :max mục.',
    ],
    'boolean'              => 'Trường :attribute phải có giá trị đúng hoặc sai.',
    'confirmed'            => 'Xác nhận :attribute không khớp.',
    'date'                 => ':attribute không phải là ngày hợp lệ.',
    'date_format'          => ':attribute không khớp với định dạng :format.',
    'different'            => ':attribute và :other phải khác nhau.',
    'digits'               => ':attribute phải có :digits chữ số.',
    'digits_between'       => ':attribute phải có từ :min đến :max chữ số.',
    'email'                => ':attribute phải là địa chỉ email hợp lệ.',
    'ends_with' => ':attribute phải kết thúc bằng một trong các ký tự: :values',
    'file'                 => ':attribute phải được cung cấp dưới dạng tệp hợp lệ.',
    'filled'               => 'Trường :attribute là bắt buộc.',
    'gt'                   => [
        'numeric' => ':attribute phải lớn hơn :value.',
        'file'    => ':attribute phải lớn hơn :value KB.',
        'string'  => ':attribute phải có nhiều hơn :value ký tự.',
        'array'   => ':attribute phải có nhiều hơn :value mục.',
    ],
    'gte'                  => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :value.',
        'file'    => ':attribute phải lớn hơn hoặc bằng :value KB.',
        'string'  => ':attribute phải có nhiều hơn hoặc bằng :value ký tự.',
        'array'   => ':attribute phải có :value mục trở lên.',
    ],
    'exists'               => ':attribute đã chọn không hợp lệ.',
    'image'                => ':attribute phải là ảnh.',
    'image_extension'      => ':attribute phải có định dạng ảnh hợp lệ và được hỗ trợ.',
    'in'                   => ':attribute đã chọn không hợp lệ.',
    'integer'              => ':attribute phải là một số nguyên.',
    'ip'                   => ':attribute phải là một địa chỉ IP hợp lệ.',
    'ipv4'                 => ':attribute phải là địa chỉ IPv4 hợp lệ.',
    'ipv6'                 => ':attribute phải là địa chỉ IPv6 hợp lệ.',
    'json'                 => ':attribute phải là một chuỗi JSON hợp lệ.',
    'lt'                   => [
        'numeric' => ':attribute phải nhỏ hơn :value.',
        'file'    => ':attribute phải nhỏ hơn :value KB.',
        'string'  => ':attribute phải có it hơn :value ký tự.',
        'array'   => ':attribute phải có ít hơn :value mục.',
    ],
    'lte'                  => [
        'numeric' => ':attribute phải nhỏ hơn hoặc bằng :value.',
        'file'    => ':attribute phải nhỏ hơn hoặc bằng :value KB.',
        'string'  => ':attribute phải có ít hơn hoặc bằng :value ký tự.',
        'array'   => ':attribute không được có nhiều hơn :value mục.',
    ],
    'max'                  => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'file'    => ':attribute không được lớn hơn :max KB.',
        'string'  => ':attribute không được nhiều hơn :max ký tự.',
        'array'   => ':attribute không thể có nhiều hơn :max mục.',
    ],
    'mimes'                => ':attribute phải là tệp tin có kiểu: :values.',
    'min'                  => [
        'numeric' => ':attribute phải tối thiểu là :min.',
        'file'    => ':attribute phải tối thiểu là :min KB.',
        'string'  => ':attribute phải có tối thiểu :min ký tự.',
        'array'   => ':attribute phải có tối thiểu :min mục.',
    ],
    'not_in'               => ':attribute đã chọn không hợp lệ.',
    'not_regex'            => 'Định dạng của :attribute không hợp lệ.',
    'numeric'              => ':attribute phải là một số.',
    'regex'                => 'Định dạng của :attribute không hợp lệ.',
    'required'             => 'Trường :attribute là bắt buộc.',
    'required_if'          => 'Trường :attribute là bắt buộc khi :other là :value.',
    'required_with'        => 'Trường :attribute là bắt buộc khi :values tồn tại.',
    'required_with_all'    => 'Trường :attribute là bắt buộc khi :values tồn tại.',
    'required_without'     => 'Trường :attribute là bắt buộc khi :values không tồn tại.',
    'required_without_all' => 'Trường :attribute là bắt buộc khi không có bất cứ :values nào tồn tại.',
    'same'                 => ':attribute và :other phải trùng khớp với nhau.',
    'safe_url'             => 'Đường dẫn cung cấp có thể không an toàn.',
    'size'                 => [
        'numeric' => ':attribute phải có cỡ :size.',
        'file'    => ':attribute phải có cỡ :size KB.',
        'string'  => ':attribute phải có :size ký tự.',
        'array'   => ':attribute phải chứa :size mục.',
    ],
    'string'               => ':attribute phải là một chuỗi.',
    'timezone'             => ':attribute phải là một khu vực hợp lệ.',
    'totp'                 => 'Mã cung cấp không hợp lệ hoặc đã hết hạn.',
    'unique'               => ':attribute đã có người sử dụng.',
    'url'                  => 'Định dạng của :attribute không hợp lệ.',
    'uploaded'             => 'Tệp tin đã không được tải lên. Máy chủ không chấp nhận các tệp tin với dung lượng lớn như tệp tin trên.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Bắt buộc xác nhận mật khẩu',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
