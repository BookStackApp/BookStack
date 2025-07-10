<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute स्वीकार गर्नुपर्छ।',
    'active_url'           => ':attribute मान्य URL होइन।',
    'after'                => ':attribute मिति :date पछिको हुनुपर्छ।',
    'alpha'                => ':attribute मा अक्षर मात्र हुनुपर्छ।',
    'alpha_dash'           => ':attribute मा अक्षर, अंक, ड्यास (-) र अन्डरस्कोर (_) मात्र हुनुपर्छ।',
    'alpha_num'            => ':attribute मा अक्षर र अंक मात्र हुनुपर्छ।',
    'array'                => ':attribute array हुनुपर्छ।',
    'backup_codes'         => 'दिइएको कोड गलत छ वा पहिल्यै प्रयोग भइसकेको छ।',
    'before'               => ':attribute मिति :date भन्दा पहिला हुनुपर्छ।',
    'between'              => [
        'numeric' => ':attribute :min देखि :max बीचमा हुनुपर्छ।',
        'file'    => ':attribute :min देखि :max किलोबाइट बीचमा हुनुपर्छ।',
        'string'  => ':attribute :min देखि :max क्यारेक्टरबीच हुनुपर्छ।',
        'array'   => ':attribute मा :min देखि :max वस्तुहरू हुनुपर्छ।',
    ],
    'boolean'              => ':attribute साँचो (true) वा झूटो (false) हुनुपर्छ।',
    'confirmed'            => ':attribute पुष्टि मिलेन।',
    'date'                 => ':attribute मान्य मिति होइन।',
    'date_format'          => ':attribute ढाँचा :format सँग मेल खाँदैन।',
    'different'            => ':attribute र :other फरक हुनुपर्छ।',
    'digits'               => ':attribute मा ठीक :digits अंक हुनुपर्छ।',
    'digits_between'       => ':attribute मा :min देखि :max अंक हुनुपर्छ।',
    'email'                => ':attribute मान्य ईमेल ठेगाना हुनुपर्छ।',
    'ends_with' => ':attribute यी मध्ये एकले अन्त्य हुनुपर्छ: :values',
    'file'                 => ':attribute मान्य फाइल हुनुपर्छ।',
    'filled'               => ':attribute आवश्यक छ।',
    'gt'                   => [
        'numeric' => ':attribute :value भन्दा बढी हुनुपर्छ।',
        'file'    => ':attribute :value किलोबाइटभन्दा बढी हुनुपर्छ।',
        'string'  => ':attribute :value क्यारेक्टरभन्दा बढी हुनुपर्छ।',
        'array'   => ':attribute मा :value भन्दा बढी वस्तुहरू हुनुपर्छ।',
    ],
    'gte'                  => [
        'numeric' => ':attribute :value भन्दा बढी वा बराबर हुनुपर्छ।',
        'file'    => ':attribute :value किलोबाइटभन्दा बढी वा बराबर हुनुपर्छ।',
        'string'  => ':attribute :value क्यारेक्टरभन्दा बढी वा बराबर हुनुपर्छ।',
        'array'   => ':attribute मा कम्तीमा :value वस्तुहरू हुनुपर्छ।',
    ],
    'exists'               => 'चयन गरिएको :attribute अमान्य छ।',
    'image'                => ':attribute एउटा तस्बिर हुनुपर्छ।',
    'image_extension'      => ':attribute मा मान्य र समर्थित तस्बिर विस्तार (extension) हुनुपर्छ।',
    'in'                   => 'चयन गरिएको :attribute अमान्य छ।',
    'integer'              => ':attribute पूर्णांक (integer) हुनुपर्छ।',
    'ip'                   => ':attribute मान्य IP ठेगाना हुनुपर्छ।',
    'ipv4'                 => ':attribute मान्य IPv4 ठेगाना हुनुपर्छ।',
    'ipv6'                 => ':attribute मान्य IPv6 ठेगाना हुनुपर्छ।',
    'json'                 => ':attribute मान्य JSON स्ट्रिङ हुनुपर्छ।',
    'lt'                   => [
        'numeric' => ':attribute :value भन्दा कम हुनुपर्छ।',
        'file'    => ':attribute :value किलोबाइटभन्दा कम हुनुपर्छ।',
        'string'  => ':attribute :value क्यारेक्टरभन्दा कम हुनुपर्छ।',
        'array'   => ':attribute मा :value भन्दा कम वस्तुहरू हुनुपर्छ।',
    ],
    'lte'                  => [
        'numeric' => ':attribute :value भन्दा कम वा बराबर हुनुपर्छ।',
        'file'    => ':attribute :value किलोबाइटभन्दा कम वा बराबर हुनुपर्छ।',
        'string'  => ':attribute :value क्यारेक्टरभन्दा कम वा बराबर हुनुपर्छ।',
        'array'   => ':attribute मा :value भन्दा बढी वस्तुहरू हुनु हुँदैन।',
    ],
    'max'                  => [
        'numeric' => ':attribute :max भन्दा बढी हुन सक्दैन।',
        'file'    => ':attribute :max किलोबाइटभन्दा बढी हुन सक्दैन।',
        'string'  => ':attribute :max क्यारेक्टरभन्दा बढी हुन सक्दैन।',
        'array'   => ':attribute मा :max भन्दा बढी वस्तुहरू हुनु हुँदैन।',
    ],
    'mimes'                => ':attribute फाइलको प्रकार :values हुनुपर्छ।',
    'min'                  => [
        'numeric' => ':attribute कम्तीमा :min हुनुपर्छ।',
        'file'    => ':attribute कम्तीमा :min किलोबाइट हुनुपर्छ।',
        'string'  => ':attribute कम्तीमा :min क्यारेक्टर हुनुपर्छ।',
        'array'   => ':attribute मा कम्तीमा :min वस्तुहरू हुनुपर्छ।',
    ],
    'not_in'               => 'चयन गरिएको :attribute अमान्य छ।',
    'not_regex'            => ':attribute को ढाँचा अमान्य छ।',
    'numeric'              => ':attribute संख्या हुनुपर्छ।',
    'regex'                => ':attribute ढाँचा अमान्य छ।',
    'required'             => ':attribute आवश्यक छ।',
    'required_if'          => ':other :value हुँदा :attribute आवश्यक हुन्छ।',
    'required_with'        => ':values भएमा :attribute आवश्यक छ।',
    'required_with_all'    => ':values भएमा :attribute आवश्यक छ।',
    'required_without'     => ':values नभएमा :attribute आवश्यक छ।',
    'required_without_all' => ':values मध्ये कुनै पनि नभएमा :attribute आवश्यक छ।',
    'same'                 => ':attribute र :other मिल्नुपर्छ।',
    'safe_url'             => 'दिएको लिङ्क सुरक्षित नहुन सक्छ।',
    'size'                 => [
        'numeric' => ':attribute ठीक :size हुनुपर्छ।',
        'file'    => ':attribute ठीक :size किलोबाइट हुनुपर्छ।',
        'string'  => ':attribute ठीक :size क्यारेक्टर हुनुपर्छ।',
        'array'   => ':attribute मा ठीक :size वस्तुहरू हुनुपर्छ।',
    ],
    'string'               => ':attribute स्ट्रिङ (पाठ) हुनुपर्छ।',
    'timezone'             => ':attribute मान्य समय क्षेत्र (timezone) हुनुपर्छ।',
    'totp'                 => 'दिएको कोड गलत छ वा सकिएको छ।',
    'unique'               => ':attribute पहिल्यै प्रयोग भइसकेको छ।',
    'url'                  => ':attribute को ढाँचा अमान्य छ।',
    'uploaded'             => 'फाइल अपलोड हुन सकेन। सर्भरले यस्तो साइज स्वीकार नगर्न सक्छ।',

    'zip_file' => ':attribute ले ZIP फाइलभित्रको फाइल देखाउनु पर्छ।',
    'zip_file_mime' => ':attribute मा :validTypes प्रकारको फाइल हुनुपर्छ, तर :foundType भेटियो।',
    'zip_model_expected' => 'डेटा वस्तु चाहिएको थियो तर ":type" भेटियो।',
    'zip_unique' => ':attribute ZIP भित्रको वस्तु प्रकारको लागि अद्वितीय हुनुपर्छ।',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'पासवर्ड पुष्टि आवश्यक छ।',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
