<?php
/**
 * Validation Lines
 * The following language lines contain the default error messages used by
 * the validator class. Some of these rules have multiple versions such
 * as the size rules. Feel free to tweak each of these messages here.
 */
return [

    // Standard laravel validation lines
    'accepted'             => 'Το :attribute πρέπει να γίνει δεκτό.',
    'active_url'           => 'Το :attribute δεν είναι ένα έγκυρο URL.',
    'after'                => 'Το :attribute πρέπει να είναι μια ημερομηνία μετά τις :date.',
    'alpha'                => 'Το :attribute μπορεί να περιέχει μόνο γράμματα.',
    'alpha_dash'           => 'Tο :attribute μπορεί να περιλαμβάνει μόνο γράμματα, αριθμούς, παύλες και κάτω παύλες.',
    'alpha_num'            => 'Tο :attribute μπορεί να περιλαμβάνει μόνο γράμματα και αριθμούς.',
    'array'                => 'Το :attribute πρέπει να είναι πίνακας.',
    'backup_codes'         => 'Ο παρεχόμενος κωδικός δεν είναι έγκυρος ή έχει ήδη χρησιμοποιηθεί.',
    'before'               => 'Tο :attribute πρέπει να είναι μια ημερομηνία πριν από :date.',
    'between'              => [
        'numeric' => 'Το :attribute πρέπει να είναι μεταξύ :min και :max.',
        'file'    => 'Το :attribute πρέπει να είναι μεταξύ :min και :max kilobytes.',
        'string'  => 'Το πεδίο :attribute πρέπει να είναι μεταξύ από :min και :max characters.',
        'array'   => 'Το πεδίο :attribute πρέπει να είναι μεταξύ :min και :max αντικείμενα.',
    ],
    'boolean'              => 'Το πεδίο :attribute πρέπει να είναι σωστό ή λάθος.',
    'confirmed'            => 'Η επιβεβαίωση του :attribute δεν ταιριάζει.',
    'date'                 => 'Το :attribute δεν έχει έγκυρη ημερομηνία.',
    'date_format'          => 'Το :attribute δεν ταιριάζει με τη μορφή :format.',
    'different'            => 'Τα πεδία :attribute και :other πρέπει να είναι διαφορετικά.',
    'digits'               => 'Το πεδίο :attribute πρέπει να είναι :digits ψηφία.',
    'digits_between'       => 'To :attribute πρέπει να είναι μεταξύ :min και :max ψηφία.',
    'email'                => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση E-mail.',
    'ends_with' => 'Το :attribute πρέπει να τελειώνει με μια απο τις ακόλουθες: :values',
    'file'                 => 'Το :attribute πρέπει να παρέχεται ως έγκυρο αρχείο.',
    'filled'               => 'Το πεδίο :attribute είναι υποχρεωτικό.',
    'gt'                   => [
        'numeric' => 'Το :attribute πρέπει να είναι μεγαλύτερο από :value.',
        'file'    => 'To :attribute πρέπει να είναι μεγαλύτερο από :value kilobytes.',
        'string'  => 'Tο :attribute πρέπει να έχει περισσότερους από :value χαρακτήρες.',
        'array'   => 'Το :attribute πρέπει να περιέχει περισσότερα από :value αντικείμενα.',
    ],
    'gte'                  => [
        'numeric' => 'Το :attribute πρέπει να είναι μεγαλύτερο ή ίσο από :value.',
        'file'    => 'Το :attribute πρέπει να είναι μεγαλύτερο ή ίσο με :value kilobytes.',
        'string'  => 'To :attribute πρέπει να είναι μεγαλύτερο ή ίσο από :value χαρακτήρες.',
        'array'   => 'Tο :attribute πρέπει να έχει :value αντικείμενα ή περισσότερα.',
    ],
    'exists'               => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'image'                => 'Tο :attribute πρέπει να είναι εικόνα.',
    'image_extension'      => 'Το πεδίο :attribute πρέπει να έχει μια έγκυρη & υποστηριζόμενη επέκταση εικόνας.',
    'in'                   => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'integer'              => 'Tο :attribute πρέπει να είναι ακέραιος αριθμός.',
    'ip'                   => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IP.',
    'ipv4'                 => 'Tο :attribute πρέπει να είναι μια έγκυρη διεύθυνση IPv4.',
    'ipv6'                 => 'Tο :attribute πρέπει να είναι μια έγκυρη διεύθυνση IPv6.',
    'json'                 => 'H :attribute πρεπει να είναι μια έγκυρη συμβολοσειρά JSON.',
    'lt'                   => [
        'numeric' => 'Tο :attribute πρέπει να είναι λιγότερο από :value.',
        'file'    => 'To :attribute πρέπει να είναι μικρότερο από :value kilobytes.',
        'string'  => 'To :attribute πρέπει να είναι μικρότερο από :value kilobytes.',
        'array'   => 'Tο :attribute πρέπει να έχει λιγότερα από :value αντικείμενα.',
    ],
    'lte'                  => [
        'numeric' => 'Το :attribute πρέπει να είναι μικρότερο ή ίσο του :value.',
        'file'    => 'Το :attribute πρέπει να είναι μικρότερο ή ίσο του :value kilobytes.',
        'string'  => 'Tο :attribute πρέπει να έχει λιγότερους από ή ίδιους :value χαρακτήρες.',
        'array'   => 'Tο :attribute δεν πρέπει να έχει περισσότερα από :value αντικείμενα.',
    ],
    'max'                  => [
        'numeric' => 'Tο :attribute δεν μπορεί να είναι μεγαλύτερο από :max.',
        'file'    => 'To :attribute δεν μπορεί να είναι μεγαλύτερο από :max kilobytes.',
        'string'  => 'Το :attribute δεν μπορεί να είναι μεγαλύτερο από :max χαρακτήρες.',
        'array'   => 'Tο :attribute δεν μπορεί να έχει περισσότερα από :max αντικείμενα.',
    ],
    'mimes'                => 'Το πεδίο :attribute πρέπει να είναι ένα αρχείου τύπου: :values.',
    'min'                  => [
        'numeric' => 'To :attribute πρέπει να είναι τουλάχιστον :min.',
        'file'    => 'Το :attribute πρέπει είναι τουλάχιστον :min kilobytes.',
        'string'  => 'Το :attribute πρέπει να είναι τουλάχιστον :min χαρακτήρες.',
        'array'   => 'To :attribute πρέπει να έχει τουλάχιστον :min αντικείμενα.',
    ],
    'not_in'               => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'not_regex'            => 'Η μορφή του :attribute δεν είναι έγκυρη.',
    'numeric'              => 'To :attribute πρέπει να είναι αριθμός.',
    'regex'                => 'Το :attribute έχει μη έγκυρη μορφή.',
    'required'             => 'Το πεδίο :attribute είναι υποχρεωτικό.',
    'required_if'          => 'To πεδίο :attribute είναι απαραίτητο εκτός αν :other είναι σε :values.',
    'required_with'        => 'To πεδίο :attribute είναι απαραίτητο όταν υπάρχουν οι :values.',
    'required_with_all'    => 'To πεδίο :attribute είναι απαραίτητο όταν υπάρχουν οι :values.',
    'required_without'     => 'To πεδίο :attribute είναι απαραίτητο όταν δεν υπάρχουν οι :values.',
    'required_without_all' => 'To πεδίο :attribute είναι απαραίτητο όταν δεν υπάρχουν καμία από :values.',
    'same'                 => 'Το πεδίο :attribute και :other πρέπει να είναι ίδια.',
    'safe_url'             => 'Ο παρεχόμενος σύνδεσμος μπορεί να μην είναι ασφαλής.',
    'size'                 => [
        'numeric' => 'Το :attribute πρέπει να είναι :size.',
        'file'    => 'Το :attribute πρέπει να έχει μέγεθος :size kilobytes.',
        'string'  => 'Το πεδίο :attribute πρέπει να είναι :size χαρακτήρες.',
        'array'   => 'Το πεδίο :attribute πρέπει να περιέχει :size αντικείμενα.',
    ],
    'string'               => 'Το :attribute πρέπει να είναι συμβολοσειρά.',
    'timezone'             => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη ζώνη ώρας.',
    'totp'                 => 'Ο παρεχόμενος κωδικός δεν είναι έγκυρος ή έχει λήξει.',
    'unique'               => 'Το πεδίο :attribute έχει ήδη χρησιμοποιηθεί.',
    'url'                  => 'Η μορφή του :attribute δεν είναι έγκυρη.',
    'uploaded'             => 'Δεν ήταν δυνατή η αποστολή του αρχείου. Ο διακομιστής ενδέχεται να μην δέχεται αρχεία αυτού του μεγέθους.',

    // Custom validation lines
    'custom' => [
        'password-confirm' => [
            'required_with' => 'Απαιτείται επιβεβαίωση κωδικού πρόσβασης',
        ],
    ],

    // Custom validation attributes
    'attributes' => [],
];
