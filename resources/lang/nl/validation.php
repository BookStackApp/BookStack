<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => ':attribute moet geaccepteerd worden.',
    'active_url'           => ':attribute is geen geldige URL.',
    'after'                => ':attribute moet een datum zijn later dan :date.',
    'alpha'                => ':attribute mag alleen letters bevatten.',
    'alpha_dash'           => ':attribute mag alleen letters, cijfers, streepjes en liggende streepjes bevatten.',
    'alpha_num'            => ':attribute mag alleen letters en nummers bevatten.',
    'array'                => ':attribute moet een reeks zijn.',
    'before'               => ':attribute moet een datum zijn voor :date.',
    'between'              => [
        'numeric' => ':attribute moet tussen de :min en :max zijn.',
        'file'    => ':attribute moet tussen de :min en :max kilobytes zijn.',
        'string'  => ':attribute moet tussen de :min en :max tekens zijn.',
        'array'   => ':attribute moet tussen de :min en :max items bevatten.',
    ],
    'boolean'              => ':attribute moet ja of nee zijn.',
    'confirmed'            => ':attribute bevestiging komt niet overeen.',
    'date'                 => ':attribute is geen geldige datum.',
    'date_format'          => ':attribute komt niet overeen met het formaat :format.',
    'different'            => ':attribute en :other moeten verschillend zijn.',
    'digits'               => ':attribute moet bestaan uit :digits cijfers.',
    'digits_between'       => ':attribute moet tussen de :min en :max cijfers zijn.',
    'email'                => ':attribute is geen geldig e-mailadres.',
    'ends_with' => ':attribute moet eindigen met een van de volgende: :values',
    'filled'               => ':attribute is verplicht.',
    'gt'                   => [
        'numeric' => ':attribute moet groter zijn dan :value.',
        'file'    => ':attribute moet groter zijn dan :value kilobytes.',
        'string'  => ':attribute moet meer dan :value tekens bevatten.',
        'array'   => ':attribute moet meer dan :value items bevatten.',
    ],
    'gte'                  => [
        'numeric' => ':attribute moet groter of gelijk zijn aan :value.',
        'file'    => ':attribute moet groter of gelijk zijn aan :value kilobytes.',
        'string'  => ':attribute moet :value of meer tekens bevatten.',
        'array'   => ':attribute moet :value items of meer bevatten.',
    ],
    'exists'               => ':attribute is ongeldig.',
    'image'                => ':attribute moet een afbeelding zijn.',
    'image_extension'      => ':attribute moet een geldige en ondersteunde afbeeldings-extensie hebben.',
    'in'                   => ':attribute is ongeldig.',
    'integer'              => ':attribute moet een getal zijn.',
    'ip'                   => ':attribute moet een geldig IP-adres zijn.',
    'ipv4'                 => ':attribute moet een geldig IPv4-adres zijn.',
    'ipv6'                 => ':attribute moet een geldig IPv6-adres zijn.',
    'json'                 => ':attribute moet een geldige JSON-string zijn.',
    'lt'                   => [
        'numeric' => ':attribute moet kleiner zijn dan :value.',
        'file'    => ':attribute moet kleiner zijn dan :value kilobytes.',
        'string'  => ':attribute moet minder dan :value tekens bevatten.',
        'array'   => ':attribute moet minder dan :value items bevatten.',
    ],
    'lte'                  => [
        'numeric' => ':attribute moet kleiner of gelijk zijn aan :value.',
        'file'    => ':attribute moet kleiner of gelijk zijn aan :value kilobytes.',
        'string'  => ':attribute moet :value tekens of minder bevatten.',
        'array'   => ':attribute mag niet meer dan :value items bevatten.',
    ],
    'max'                  => [
        'numeric' => ':attribute mag niet groter zijn dan :max.',
        'file'    => ':attribute mag niet groter zijn dan :max kilobytes.',
        'string'  => ':attribute mag niet groter zijn dan :max tekens.',
        'array'   => ':attribute mag niet meer dan :max items bevatten.',
    ],
    'mimes'                => ':attribute moet een bestand zijn van het type: :values.',
    'min'                  => [
        'numeric' => ':attribute moet minstens :min zijn.',
        'file'    => ':attribute moet minstens :min kilobytes zijn.',
        'string'  => ':attribute moet minstens :min karakters bevatten.',
        'array'   => ':attribute moet minstens :min items bevatten.',
    ],
    'not_in'               => ':attribute is ongeldig.',
    'not_regex'            => ':attribute formaat is ongeldig.',
    'numeric'              => ':attribute moet een getal zijn.',
    'regex'                => ':attribute formaat is ongeldig.',
    'required'             => ':attribute veld is verplicht.',
    'required_if'          => ':attribute veld is verplicht als :other gelijk is aan :value.',
    'required_with'        => ':attribute veld is verplicht wanneer :values ingesteld is.',
    'required_with_all'    => ':attribute veld is verplicht wanneer :values ingesteld is.',
    'required_without'     => ':attribute veld is verplicht wanneer :values niet ingesteld is.',
    'required_without_all' => ':attribute veld is verplicht wanneer geen van :values ingesteld zijn.',
    'same'                 => ':attribute en :other moeten overeenkomen.',
    'safe_url'             => 'De opgegeven link is mogelijk niet veilig.',
    'size'                 => [
        'numeric' => ':attribute moet :size zijn.',
        'file'    => ':attribute moet :size kilobytes zijn.',
        'string'  => ':attribute moet :size tekens bevatten.',
        'array'   => ':attribute moet :size items bevatten.',
    ],
    'string'               => ':attribute moet tekst zijn.',
    'timezone'             => ':attribute moet een geldige zone zijn.',
    'unique'               => ':attribute is al in gebruik.',
    'url'                  => ':attribute formaat is ongeldig.',
    'uploaded'             => 'Het bestand kon niet worden geüpload. De server accepteert mogelijk geen bestanden van deze grootte.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Wachtwoord bevestiging verplicht',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
