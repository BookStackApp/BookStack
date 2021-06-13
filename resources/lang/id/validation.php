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
        'string'  => ':attribute harus kurang dari :value karakter.',
        'array'   => ':attribute harus memiliki kurang dari :value item.',
    ],
    'lte'                  => [
        'numeric' => ':attribute harus kurang dari atau sama dengan :value.',
        'file'    => ':attribute harus kurang dari atau sama dengan :value kilobyte.',
        'string'  => ':attribute harus kurang dari atau sama dengan :value karakter.',
        'array'   => ':attribute tidak boleh memiliki lebih dari :value item.',
    ],
    'max'                  => [
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'file'    => ':attribute tidak boleh lebih dari :max kilobyte.',
        'string'  => ':attribute tidak boleh lebih dari :max karakter.',
        'array'   => ':attribute tidak boleh memiliki lebih dari :max item.',
    ],
    'mimes'                => ':attribute harus berupa file dengan tipe: :value.',
    'min'                  => [
        'numeric' => ':attribute minimal harus :min.',
        'file'    => ':attribute minimal harus :min kilobyte.',
        'string'  => ':attribute setidaknya harus :min karakter.',
        'array'   => ':attribute minimal harus memiliki :min item.',
    ],
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
    'uploaded'             => 'Berkas tidak dapat diunggah. Server mungkin tidak menerima berkas dengan ukuran ini.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Konfirmasi kata sandi diperlukan',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
