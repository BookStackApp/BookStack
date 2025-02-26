<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'Rhaid derbyn y :attribute.',
    'active_url'           => 'Nid ywr :attribute yn URL dilys.',
    'after'                => 'Rhaid i\'r :attribute bod yn dyddiad ar ol :date.',
    'alpha'                => 'Rhaid ir :attribute cynnwys llythrennau yn unig.',
    'alpha_dash'           => 'Dim ond llythrennau, rhifau, llinellau toriad a thanlinellau y gall y :attribute gynnwys.',
    'alpha_num'            => 'Rhaid ir :attribute cynnwys llythrennau a rhifau yn unig.',
    'array'                => 'Rhaid i :attribute fod yn array.',
    'backup_codes'         => 'Nid yw\'r cod a ddarparwyd yn ddilys neu mae eisoes wedi\'i ddefnyddio.',
    'before'               => 'Rhaid i\'r :attribute bod yn dyddiad cyn :date.',
    'between'              => [
        'numeric' => 'Rhaid i\'r :attribute bod rhwng :min a :max.',
        'file'    => 'Rhaid i\'r :attribute bod rhwng :min a :max kilobytes.',
        'string'  => 'Rhaid i\'r :attribute bod rhwng :min a :max cymeriadau.',
        'array'   => 'Rhaid i\'r :attribute cael rhwng :min a :max o eitemau.',
    ],
    'boolean'              => 'Rhaid i :attribute fod yn wir neu ddim.',
    'confirmed'            => 'Dydi\'r cadarnhad :attribute ddim yn cydfynd.',
    'date'                 => 'Nid yw\'r :attribute yn dyddiad dilys.',
    'date_format'          => 'Nid yw\'r :attribute yn cydfynd ar format :format.',
    'different'            => 'Rhaid i :attribute a :other bod yn wahanol.',
    'digits'               => 'Rhai i\'r :attribute bod yn :digits o ddigidau.',
    'digits_between'       => 'Rhaid i\'r :attribute bod rhwng :min a :max o digidau.',
    'email'                => 'Rhaid i\'r :attribute bod yn cyfeiriad e-bost dilys.',
    'ends_with' => 'Rhaid i\'r :attribute orffen gydag un o\'r canlynol: :values',
    'file'                 => 'Rhaid darparu\'r :attribute fel ffeil ddilys.',
    'filled'               => 'Mae angen llenwi\'r maes :attribute.',
    'gt'                   => [
        'numeric' => 'Rhaid i\'r :attribute fod yn fwy na :value.',
        'file'    => 'Rhaid i\'r :attribute fod yn fwy na :value kilobytes.',
        'string'  => 'Rhaid i\'r :attribute fod yn fwy na :value cymeriadau.',
        'array'   => 'Rhaid i\'r :attribute fod yn fwy na :value eitemau.',
    ],
    'gte'                  => [
        'numeric' => 'Rhaid i’r :attribute fod yn fwy na, neu’n gyfartal â :value.',
        'file'    => 'Rhaid i’r :attribute fod yn fwy na, neu’n gyfartal â :value cilobeit.',
        'string'  => 'Rhaid i’r :attribute fod yn fwy na, neu’n gyfartal â :value nod.',
        'array'   => 'Rhaid i’r :attribute fod â :value o eitemau neu fwy.',
    ],
    'exists'               => 'Mae\'r dewis :attribute yn annilys.',
    'image'                => 'Rhaid i’r :attribute fod yn ddelwedd.',
    'image_extension'      => 'Rhaid i’r :attribute fod ag estyniad delwedd dilys & gefnogir.',
    'in'                   => 'Mae\'r dewis :attribute yn annilys.',
    'integer'              => 'Rhaid i’r :attribute fod yn gyfanrif.',
    'ip'                   => 'Rhaid i’r :attribute fod yn gyfeiriad IP dilys.',
    'ipv4'                 => 'Rhaid i’r :attribute fod yn gyfeiriad IPv4 dilys.',
    'ipv6'                 => 'Rhaid i’r :attribute fod yn gyfeiriad IPv6 dilys.',
    'json'                 => 'Rhaid i’r :attribute fod yn llinyn JSON dilys.',
    'lt'                   => [
        'numeric' => 'Rhaid i’r :attribute fod yn llai na :value.',
        'file'    => 'Rhaid i’r :attribute fod yn llai na :value cilobeit.',
        'string'  => 'Rhaid i’r :attribute fod yn llai na :value nod.',
        'array'   => 'Rhaid i’r :attribute fod â llai na :value o eitemau.',
    ],
    'lte'                  => [
        'numeric' => 'Rhaid i’r :attribute fod yn llai na, neu’n gyfartal â :value.',
        'file'    => 'Rhaid i’r :attribute fod yn llai na, neu’n gyfartal â :value cilobeit.',
        'string'  => 'Rhaid i’r :attribute fod yn llai na, neu’n gyfartal â :value nod.',
        'array'   => 'Ni ddylai’r :attribute fod â mwy na :value o eitemau.',
    ],
    'max'                  => [
        'numeric' => 'Ni ddylai’r :attribute fod yn fwy na :max.',
        'file'    => 'Ni ddylai’r :attribute fod yn fwy na :max cilobeit.',
        'string'  => 'Ni ddylai’r :attribute fod yn fwy na :max nod.',
        'array'   => 'Ni ddylai’r :attribute fod â mwy na :max o eitemau.',
    ],
    'mimes'                => 'Rhaid i’r :attribute fod yn ffeil o fath: :values.',
    'min'                  => [
        'numeric' => 'Rhaid i’r :attribute fod yn o leiaf :min.',
        'file'    => 'Rhaid i’r :attribute fod yn o leiaf :min cilobeit.',
        'string'  => 'Rhaid i’r :attribute fod yn o leiaf :min nod.',
        'array'   => 'Rhaid i’r :attribute fod â llai na :min o eitemau.',
    ],
    'not_in'               => 'Mae\'r dewis :attribute yn annilys.',
    'not_regex'            => 'Mae’r fformat :attribute yn annilys.',
    'numeric'              => 'Rhaid i’r :attribute fod yn rhif.',
    'regex'                => 'Mae’r fformat :attribute yn annilys.',
    'required'             => 'Mae :attribute yn faes gofynnol.',
    'required_if'          => 'Mae :attribute yn faes gofynnol pan fo :other yn :value.',
    'required_with'        => 'Mae :attribute yn faes gofynnol pan fo :values yn bresennol.',
    'required_with_all'    => 'Mae :attribute yn faes gofynnol pan fo :values yn bresennol.',
    'required_without'     => 'Mae :attitude yn faes gofynnol pan nad yw :values yn bresennol.',
    'required_without_all' => 'Mae angen y maes :attribute os dydi\'r un o :values yn bresennol.',
    'same'                 => 'Mae’n rhaid i’r :attribute a :other gyd-fynd.',
    'safe_url'             => 'Efallai na fydd y ddolen a ddarperir yn ddiogel.',
    'size'                 => [
        'numeric' => 'Rhaid i’r :attribute fod yn :size.',
        'file'    => 'Rhaid i’r :attribute fod yn :size cilobeit.',
        'string'  => 'Rhaid i’r :attribute fod yn :size nod.',
        'array'   => 'Rhaid i’r :attribute gynnwys eitemau :size.',
    ],
    'string'               => 'Rhaid i’r :attribute fod yn llinyn.',
    'timezone'             => 'Rhaid i’r :attribute fod yn barth dilys.',
    'totp'                 => 'Nid yw\'r cod a ddarperir yn ddilys neu mae wedi dod i ben.',
    'unique'               => 'Mae’r :attribute eisoes wedi ei gymryd.',
    'url'                  => 'Mae’r fformat :attribute yn annilys.',
    'uploaded'             => 'Nid oedd modd uwchlwytho’r ffeil. Efallai na fydd y gweinydd yn derbyn ffeiliau o\'r maint hwn.',

    'zip_file' => 'Mae\'r :attribute angen cyfeirio at ffeil yn y ZIP.',
    'zip_file_mime' => 'Mae\'r :attribute angen cyfeirio at ffeil o fath :valid Types, sydd wedi\'i ffeindio :foundType.',
    'zip_model_expected' => 'Dyswgyl am wrthrych data ond wedi ffeindio ":type".',
    'zip_unique' => 'Mae rhaid y :attribute fod yn unigol i\'r fath o wrthrych yn y ZIP.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Rhaid cadarnhau cyfrinair',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
