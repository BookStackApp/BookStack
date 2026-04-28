<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'ต้องยอมรับ :attribute',
    'active_url'           => ':attribute ไม่ใช่ URL ที่ถูกต้อง',
    'after'                => ':attribute ต้องเป็นวันที่หลังจาก :date',
    'alpha'                => ':attribute ต้องมีเฉพาะตัวอักษรเท่านั้น',
    'alpha_dash'           => ':attribute ต้องมีเฉพาะตัวอักษร ตัวเลข ขีดกลาง และขีดล่างเท่านั้น',
    'alpha_num'            => ':attribute ต้องมีเฉพาะตัวอักษรและตัวเลขเท่านั้น',
    'array'                => ':attribute ต้องเป็น array',
    'backup_codes'         => 'รหัสที่ให้มาไม่ถูกต้องหรือถูกใช้ไปแล้ว',
    'before'               => ':attribute ต้องเป็นวันที่ก่อน :date',
    'between'              => [
        'numeric' => ':attribute ต้องอยู่ระหว่าง :min ถึง :max',
        'file'    => ':attribute ต้องมีขนาดระหว่าง :min ถึง :max กิโลไบต์',
        'string'  => ':attribute ต้องมีความยาวระหว่าง :min ถึง :max ตัวอักษร',
        'array'   => ':attribute ต้องมีระหว่าง :min ถึง :max รายการ',
    ],
    'boolean'              => 'ฟิลด์ :attribute ต้องเป็น true หรือ false',
    'confirmed'            => 'การยืนยัน :attribute ไม่ตรงกัน',
    'date'                 => ':attribute ไม่ใช่วันที่ที่ถูกต้อง',
    'date_format'          => ':attribute ไม่ตรงกับรูปแบบ :format',
    'different'            => ':attribute และ :other ต้องแตกต่างกัน',
    'digits'               => ':attribute ต้องมี :digits หลัก',
    'digits_between'       => ':attribute ต้องมีระหว่าง :min ถึง :max หลัก',
    'email'                => ':attribute ต้องเป็นที่อยู่อีเมลที่ถูกต้อง',
    'ends_with' => ':attribute ต้องลงท้ายด้วยหนึ่งในนี้: :values',
    'file'                 => ':attribute ต้องเป็นไฟล์ที่ถูกต้อง',
    'filled'               => 'ฟิลด์ :attribute จำเป็นต้องกรอก',
    'gt'                   => [
        'numeric' => ':attribute ต้องมากกว่า :value',
        'file'    => ':attribute ต้องมากกว่า :value กิโลไบต์',
        'string'  => ':attribute ต้องมีความยาวมากกว่า :value ตัวอักษร',
        'array'   => ':attribute ต้องมีมากกว่า :value รายการ',
    ],
    'gte'                  => [
        'numeric' => ':attribute ต้องมากกว่าหรือเท่ากับ :value',
        'file'    => ':attribute ต้องมากกว่าหรือเท่ากับ :value กิโลไบต์',
        'string'  => ':attribute ต้องมีความยาวมากกว่าหรือเท่ากับ :value ตัวอักษร',
        'array'   => ':attribute ต้องมี :value รายการขึ้นไป',
    ],
    'exists'               => ':attribute ที่เลือกไม่ถูกต้อง',
    'image'                => ':attribute ต้องเป็นรูปภาพ',
    'image_extension'      => ':attribute ต้องมีนามสกุลรูปภาพที่ถูกต้องและรองรับ',
    'in'                   => ':attribute ที่เลือกไม่ถูกต้อง',
    'integer'              => ':attribute ต้องเป็นจำนวนเต็ม',
    'ip'                   => ':attribute ต้องเป็นที่อยู่ IP ที่ถูกต้อง',
    'ipv4'                 => ':attribute ต้องเป็นที่อยู่ IPv4 ที่ถูกต้อง',
    'ipv6'                 => ':attribute ต้องเป็นที่อยู่ IPv6 ที่ถูกต้อง',
    'json'                 => ':attribute ต้องเป็น JSON string ที่ถูกต้อง',
    'lt'                   => [
        'numeric' => ':attribute ต้องน้อยกว่า :value',
        'file'    => ':attribute ต้องน้อยกว่า :value กิโลไบต์',
        'string'  => ':attribute ต้องมีความยาวน้อยกว่า :value ตัวอักษร',
        'array'   => ':attribute ต้องมีน้อยกว่า :value รายการ',
    ],
    'lte'                  => [
        'numeric' => ':attribute ต้องน้อยกว่าหรือเท่ากับ :value',
        'file'    => ':attribute ต้องน้อยกว่าหรือเท่ากับ :value กิโลไบต์',
        'string'  => ':attribute ต้องมีความยาวน้อยกว่าหรือเท่ากับ :value ตัวอักษร',
        'array'   => ':attribute ต้องไม่มีมากกว่า :value รายการ',
    ],
    'max'                  => [
        'numeric' => ':attribute ต้องไม่มากกว่า :max',
        'file'    => ':attribute ต้องไม่มากกว่า :max กิโลไบต์',
        'string'  => ':attribute ต้องไม่มากกว่า :max ตัวอักษร',
        'array'   => ':attribute ต้องไม่มีมากกว่า :max รายการ',
    ],
    'mimes'                => ':attribute ต้องเป็นไฟล์ประเภท: :values',
    'min'                  => [
        'numeric' => ':attribute ต้องอย่างน้อย :min',
        'file'    => ':attribute ต้องอย่างน้อย :min กิโลไบต์',
        'string'  => ':attribute ต้องมีความยาวอย่างน้อย :min ตัวอักษร',
        'array'   => ':attribute ต้องมีอย่างน้อย :min รายการ',
    ],
    'not_in'               => ':attribute ที่เลือกไม่ถูกต้อง',
    'not_regex'            => 'รูปแบบ :attribute ไม่ถูกต้อง',
    'numeric'              => ':attribute ต้องเป็นตัวเลข',
    'regex'                => 'รูปแบบ :attribute ไม่ถูกต้อง',
    'required'             => 'ฟิลด์ :attribute จำเป็นต้องกรอก',
    'required_if'          => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อ :other เป็น :value',
    'required_with'        => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อมี :values',
    'required_with_all'    => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อมี :values',
    'required_without'     => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อไม่มี :values',
    'required_without_all' => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อไม่มีสิ่งใดใน :values',
    'same'                 => ':attribute และ :other ต้องตรงกัน',
    'safe_url'             => 'ลิงก์ที่ให้มาอาจไม่ปลอดภัย',
    'size'                 => [
        'numeric' => ':attribute ต้องเป็น :size',
        'file'    => ':attribute ต้องมีขนาด :size กิโลไบต์',
        'string'  => ':attribute ต้องมีความยาว :size ตัวอักษร',
        'array'   => ':attribute ต้องมี :size รายการ',
    ],
    'string'               => ':attribute ต้องเป็น string',
    'timezone'             => ':attribute ต้องเป็นเขตเวลาที่ถูกต้อง',
    'totp'                 => 'รหัสที่ให้มาไม่ถูกต้องหรือหมดอายุแล้ว',
    'unique'               => ':attribute ถูกใช้งานแล้ว',
    'url'                  => 'รูปแบบ :attribute ไม่ถูกต้อง',
    'uploaded'             => 'ไม่สามารถอัปโหลดไฟล์ได้ เซิร์ฟเวอร์อาจไม่รับไฟล์ขนาดนี้',

    'zip_file' => ':attribute ต้องอ้างอิงถึงไฟล์ภายใน ZIP',
    'zip_file_size' => 'ไฟล์ :attribute ต้องไม่เกิน :size MB',
    'zip_file_mime' => ':attribute ต้องอ้างอิงถึงไฟล์ประเภท :validTypes แต่พบ :foundType',
    'zip_model_expected' => 'คาดหวัง data object แต่พบ ":type"',
    'zip_unique' => ':attribute ต้องไม่ซ้ำกันสำหรับประเภท object ภายใน ZIP',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'จำเป็นต้องยืนยันรหัสผ่าน',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
