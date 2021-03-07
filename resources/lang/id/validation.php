<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute harus diterima.',
    'active_url'           => ':attribute bukan URL yang valid.',
    'after'                => ':attribute harus setelah tanggal :date.',
    'alpha'                => ':attribute hanya boleh berisi huruf.',
    'alpha_dash'           => ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num'            => ':attribute hanya boleh berisi huruf dan angka.',
    'array'                => ':attribute harus berupa larik.',
    'before'               => ':attribute harus tanggal sebelum :date.',
    'between'              => [
        'numeric' => ':attribute harus di antara :min dan :max.',
        'file'    => ':attribute harus diantara :min dan :max kilobyte.',
        'string'  => ':attribute harus memiliki karakter antara :min dan :max.',
        'array'   => ':attribute harus memiliki item antara :min dan :max.',
    ],
    'boolean'              => ':attribute bidang harus berisi benar atau salah.',
    'confirmed'            => ':attribute konfirmasi tidak sama.',
    'date'                 => ':attribute bukan tanggal yang valid.',
    'date_format'          => ':attribute tidak sesuai dengan format :format.',
    'different'            => ':attribute dan :other harus berbeda.',
    'digits'               => ':attribute harus :digits digit.',
    'digits_between'       => ':attribute harus diantara :min dan :max digit.',
    'email'                => ':attrtibute Harus alamat e-mail yang valid.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu dari berikut ini: :values',
    'filled'               => ':attribute bidang diperlukan.',
    'gt'                   => [
        'numeric' => ':attribute harus lebih besar dari :value.',
        'file'    => ':attribute harus lebih besar dari :value kilobyte.',
        'string'  => ':attribute harus lebih besar dari :value karakter.',
        'array'   => ':attribute harus memiliki lebih dari item :value.',
    ],
    'gte'                  => [
        'numeric' => ':attribute harus lebih besar dari atau sama dengan :value.',
        'file'    => ':attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'string'  => ':attribute harus lebih besar dari atau sama dengan karakter :value.',
        'array'   => ':attribute harus memiliki :value item atau lebih.',
    ],
    'exists'               => ':attribute yang dipilih tidak valid.',
    'image'                => ':attribute harus berupa gambar.',
    'image_extension'      => ':attribute harus memiliki ekstensi gambar yang valid & didukung.',
    'in'                   => ':attribute yang dipilih tidak valid.',
    'integer'              => ':attribute harus berupa bilangan bulat.',
    'ip'                   => ':attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => ':attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => ':attribute harus berupa string JSON yang valid.',
    'lt'                   => [
        'numeric' => ':attribute harus kurang dari :value.',
        'file'    => ':attribute harus kurang dari :value kilobyte.',
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
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => ':attribute setidaknya harus :min karakter.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'no_double_extension'  => 'The :attribute must only have a single file extension.',
    'not_in'               => ':attribute yang dipilih tidak valid.',
    'not_regex'            => ':attribute format tidak valid.',
    'numeric'              => ':attribute harus berupa nomot.',
    'regex'                => 'Format :attribute tidak valid.',
    'required'             => ':attribute bidang harus diisi.',
    'required_if'          => ':attribute Bidang harus diisi saat :other atau :value.',
    'required_with'        => 'Bidang :attribute harus diisi jika ada :nilai.',
    'required_with_all'    => 'Bidang :attribute harus diisi jika ada :values.',
    'required_without'     => 'Bidang :attribute harus diisi jika :values tidak ada.',
    'required_without_all' => 'Bidang :attribute harus diisi jika tidak ada :value yang ada.',
    'same'                 => ':attribute dan :other harus sama.',
    'safe_url'             => 'Tautan yang diberikan mungkin tidak aman.',
    'size'                 => [
        'numeric' => ':attribute harus berukuran :size.',
        'file'    => ':attribute harus berukuran :size kilobyte.',
        'string'  => ':attribute harus memiliki karakter berukuran :size.',
        'array'   => ':attribute harus mengandung :size item.',
    ],
    'string'               => ':attribute harus berupa string.',
    'timezone'             => ':attribute harus menjadi zona yang valid.',
    'unique'               => ':attribute sudah diambil.',
    'url'                  => ':attribute format tidak valid.',
    'uploaded'             => 'File tidak dapat diunggah. Server mungkin tidak menerima file dengan ukuran ini.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Konfirmasi kata sandi diperlukan',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
