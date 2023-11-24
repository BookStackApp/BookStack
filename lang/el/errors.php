<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Δεν έχετε δικαίωμα πρόσβασης στη ζητούμενη σελίδα.',
    'permissionJson' => 'Δεν έχετε άδεια να εκτελέσετε την αιτούμενη ενέργεια.',

    // Auth
    'error_user_exists_different_creds' => 'Ένας χρήστης με email :email υπάρχει ήδη αλλά με διαφορετικά διαπιστευτήρια.',
    'email_already_confirmed' => 'Το email έχει ήδη επιβεβαιωθεί, Δοκιμάστε να συνδεθείτε.',
    'email_confirmation_invalid' => 'Αυτό το διακριτικό επιβεβαίωσης δεν είναι έγκυρο ή έχει ήδη χρησιμοποιηθεί, Παρακαλώ δοκιμάστε να εγγραφείτε ξανά.',
    'email_confirmation_expired' => 'Το διακριτικό επιβεβαίωσης έχει λήξει, έχει σταλεί ένα νέο email επιβεβαίωσης.',
    'email_confirmation_awaiting' => 'Η διεύθυνση ηλεκτρονικού ταχυδρομείου για το λογαριασμό που χρησιμοποιείται πρέπει να επιβεβαιωθεί',
    'ldap_fail_anonymous' => 'Η πρόσβαση LDAP απέτυχε με ανώνυμη σύνδεση',
    'ldap_fail_authed' => 'Η πρόσβαση LDAP απέτυχε με τη χρήση δοσμένων λεπτομερειών dn & κωδικού πρόσβασης',
    'ldap_extension_not_installed' => 'Η επέκταση LDAP PHP δεν εγκαταστάθηκε',
    'ldap_cannot_connect' => 'Αδυναμία σύνδεσης στο διακομιστή ldap, η αρχική σύνδεση απέτυχε',
    'saml_already_logged_in' => 'Ήδη συνδεδεμένος',
    'saml_user_not_registered' => 'Ο χρήστης :name δεν είναι εγγεγραμμένος και η αυτόματη εγγραφή είναι απενεργοποιημένη',
    'saml_no_email_address' => 'Δεν ήταν δυνατή η εύρεση μιας διεύθυνσης ηλεκτρονικού ταχυδρομείου, για αυτόν τον χρήστη, στα δεδομένα που παρέχονται από το εξωτερικό σύστημα ελέγχου ταυτότητας',
    'saml_invalid_response_id' => 'Το αίτημα από το εξωτερικό σύστημα ελέγχου ταυτότητας δεν αναγνωρίζεται από μια διαδικασία που ξεκίνησε από αυτή την εφαρμογή. Η πλοήγηση πίσω μετά από μια σύνδεση θα μπορούσε να προκαλέσει αυτό το ζήτημα.',
    'saml_fail_authed' => 'Η σύνδεση με τη χρήση :system απέτυχε, το σύστημα δεν παρείχε επιτυχή εξουσιοδότηση',
    'oidc_already_logged_in' => 'Ήδη συνδεδεμένος',
    'oidc_user_not_registered' => 'Ο χρήστης :name δεν είναι εγγεγραμμένος και η αυτόματη εγγραφή είναι απενεργοποιημένη',
    'oidc_no_email_address' => 'Δεν ήταν δυνατή η εύρεση μιας διεύθυνσης ηλεκτρονικού ταχυδρομείου, για αυτόν τον χρήστη, στα δεδομένα που παρέχονται από το εξωτερικό σύστημα ελέγχου ταυτότητας',
    'oidc_fail_authed' => 'Η σύνδεση με τη χρήση :system απέτυχε, το σύστημα δεν παρείχε επιτυχή εξουσιοδότηση',
    'social_no_action_defined' => 'Καμία ενέργεια δεν ορίστηκε',
    'social_login_bad_response' => "Παρουσιάστηκε σφάλμα κατά τη διάρκεια :socialAccount login: \n:error",
    'social_account_in_use' => 'Αυτός ο λογαριασμός :socialAccount είναι ήδη σε χρήση, Δοκιμάστε να συνδεθείτε μέσω της επιλογής :socialAccount .',
    'social_account_email_in_use' => 'Το email :email είναι ήδη σε χρήση. Αν έχετε ήδη ένα λογαριασμό, μπορείτε να συνδέσετε τον :socialAccount λογαριασμό σας από τις ρυθμίσεις του προφίλ σας.',
    'social_account_existing' => 'Αυτός ο :socialAccount είναι ήδη συνδεδεμένος στο προφίλ σας.',
    'social_account_already_used_existing' => 'Αυτός ο :socialAccount λογαριασμός χρησιμοποιείται ήδη από άλλο χρήστη.',
    'social_account_not_used' => 'Αυτός ο :socialAccount λογαριασμός δεν είναι συνδεδεμένος με κανέναν χρήστη. Παρακαλώ επισυνάψτε τον στις ρυθμίσεις του προφίλ σας. ',
    'social_account_register_instructions' => 'Εάν δεν έχετε ακόμα λογαριασμό, μπορείτε να καταχωρήσετε ένα λογαριασμό χρησιμοποιώντας την επιλογή :socialAccount .',
    'social_driver_not_found' => 'Δεν βρέθηκε κοινωνικός οδηγός',
    'social_driver_not_configured' => 'Οι κοινωνικές ρυθμίσεις του :socialAccount δεν έχουν ρυθμιστεί σωστά.',
    'invite_token_expired' => 'Αυτός ο σύνδεσμος πρόσκλησης έχει λήξει. Αντ\' αυτού μπορείτε να προσπαθήσετε να επαναφέρετε τον κωδικό πρόσβασής σας.',

    // System
    'path_not_writable' => 'Η διαδρομή αρχείου :filePath δεν μπόρεσε να μεταφορτωθεί. Βεβαιωθείτε ότι είναι εγγράψιμη στο διακομιστή.',
    'cannot_get_image_from_url' => 'Αδυναμία λήψης εικόνας από :url',
    'cannot_create_thumbs' => 'Ο διακομιστής δεν μπορεί να δημιουργήσει μικρογραφίες. Παρακαλώ ελέγξτε ότι έχετε την επέκταση GD PHP εγκατεστημένη.',
    'server_upload_limit' => 'Ο διακομιστής δεν επιτρέπει τη μεταφόρτωση αυτού του μεγέθους. Παρακαλώ δοκιμάστε ένα μικρότερο μέγεθος αρχείου.',
    'server_post_limit' => 'The server cannot receive the provided amount of data. Try again with less data or a smaller file.',
    'uploaded'  => 'Ο διακομιστής δεν επιτρέπει τη μεταφόρτωση αυτού του μεγέθους. Παρακαλώ δοκιμάστε ένα μικρότερο μέγεθος αρχείου.',

    // Drawing & Images
    'image_upload_error' => 'Παρουσιάστηκε σφάλμα κατά το ανέβασμα της εικόνας.',
    'image_upload_type_error' => 'Ο τύπος εικόνας που μεταφορτώθηκε δεν είναι έγκυρος',
    'image_upload_replace_type' => 'Image file replacements must be of the same type',
    'image_upload_memory_limit' => 'Failed to handle image upload and/or create thumbnails due to system resource limits.',
    'image_thumbnail_memory_limit' => 'Failed to create image size variations due to system resource limits.',
    'image_gallery_thumbnail_memory_limit' => 'Failed to create gallery thumbnails due to system resource limits.',
    'drawing_data_not_found' => 'Δεν ήταν δυνατή η φόρτωση δεδομένων σχεδίασης. Το αρχείο σχεδίασης ενδέχεται να μην υπάρχει πλέον ή ενδέχεται να μην έχετε άδεια πρόσβασης σε αυτά.',

    // Attachments
    'attachment_not_found' => 'Το συνημμένο δεν βρέθηκε',
    'attachment_upload_error' => 'An error occurred uploading the attachment file',

    // Pages
    'page_draft_autosave_fail' => 'Αποτυχία αποθήκευσης προσχέδιου. Βεβαιωθείτε ότι έχετε σύνδεση στο διαδίκτυο πριν την αποθήκευση αυτής της σελίδας',
    'page_draft_delete_fail' => 'Failed to delete page draft and fetch current page saved content',
    'page_custom_home_deletion' => 'Δεν μπορεί να διαγραφεί μια σελίδα ενώ έχει οριστεί ως αρχική σελίδα',

    // Entities
    'entity_not_found' => 'Η οντότητα δεν βρέθηκε',
    'bookshelf_not_found' => 'Το ράφι δεν βρέθηκε',
    'book_not_found' => 'Το βιβλίο δεν βρέθηκε',
    'page_not_found' => 'Η σελίδα δεν βρέθηκε',
    'chapter_not_found' => 'Το κεφάλαιο δεν βρέθηκε',
    'selected_book_not_found' => 'Το επιλεγμένο βιβλίο δεν βρέθηκε',
    'selected_book_chapter_not_found' => 'Το επιλεγμένο βιβλίο ή κεφάλαιο δεν βρέθηκε',
    'guests_cannot_save_drafts' => 'Οι επισκέπτες δεν μπορούν να αποθηκεύσουν πρόχειρα',

    // Users
    'users_cannot_delete_only_admin' => 'Δεν μπορείτε να διαγράψετε τον μοναδικό διαχειριστή',
    'users_cannot_delete_guest' => 'Δεν μπορείτε να διαγράψετε τον επισκέπτη',

    // Roles
    'role_cannot_be_edited' => 'Αυτός ο ρόλος δεν μπορεί να επεξεργαστεί',
    'role_system_cannot_be_deleted' => 'Αυτός ο ρόλος είναι ρόλος συστήματος και δεν μπορεί να διαγραφεί',
    'role_registration_default_cannot_delete' => 'Αυτός ο ρόλος δεν μπορεί να διαγραφεί ενώ έχει οριστεί ως προεπιλεγμένος ρόλος εγγραφής',
    'role_cannot_remove_only_admin' => 'Αυτός ο χρήστης είναι ο μόνος χρήστης που έχει ανατεθεί στον ρόλο διαχειριστή. Εκχωρήστε τον ρόλο διαχειριστή σε άλλο χρήστη πριν επιχειρήσετε να τον καταργήσετε εδώ.',

    // Comments
    'comment_list' => 'Παρουσιάστηκε σφάλμα κατά την λήψη σχολίων.',
    'cannot_add_comment_to_draft' => 'Δεν μπορείτε να προσθέσετε σχόλια σε ένα προσχέδιο.',
    'comment_add' => 'Παρουσιάστηκε σφάλμα κατά την προσθήκη / ενημέρωση του σχολίου.',
    'comment_delete' => 'Παρουσιάστηκε σφάλμα κατά τη διαγραφή του σχολίου.',
    'empty_comment' => 'Αδυναμία προσθήκης ενός κενού σχολίου.',

    // Error pages
    '404_page_not_found' => 'Η Σελίδα δε βρέθηκε',
    'sorry_page_not_found' => 'Λυπούμαστε, Η σελίδα που αναζητάτε δεν βρέθηκε.',
    'sorry_page_not_found_permission_warning' => 'Αν περιμένατε να υπάρχει αυτή η σελίδα, ίσως να μην έχετε δικαίωμα να την δείτε.',
    'image_not_found' => 'Η Εικόνα δεν βρέθηκε',
    'image_not_found_subtitle' => 'Λυπούμαστε, το αρχείο εικόνας που αναζητάτε δεν μπορεί να βρεθεί.',
    'image_not_found_details' => 'Αν περιμένατε να υπάρχει αυτή η εικόνα, ίσως να έχει διαγραφεί.',
    'return_home' => 'Επιστροφή στην αρχική σελίδα',
    'error_occurred' => 'Προέκυψε Ένα Σφάλμα',
    'app_down' => ':appName είναι προσωρινά μη διαθέσιμη',
    'back_soon' => 'Θα υπάρξει σύντομα υποστήριξη.',

    // API errors
    'api_no_authorization_found' => 'Δεν βρέθηκε διακριτικό εξουσιοδότησης κατόπιν αιτήματος',
    'api_bad_authorization_format' => 'Ένα διακριτικό εξουσιοδότησης βρέθηκε κατόπιν αιτήματος, αλλά η μορφή εμφανίστηκε εσφαλμένη',
    'api_user_token_not_found' => 'Δεν βρέθηκε αντίστοιχο διακριτικό API για το παρεχόμενο διακριτικό εξουσιοδότησης',
    'api_incorrect_token_secret' => 'Το μυστικό που παρέχεται για το δεδομένο χρησιμοποιημένο διακριτικό API είναι εσφαλμένο',
    'api_user_no_api_permission' => 'Ο ιδιοκτήτης του χρησιμοποιημένου διακριτικού API δεν έχει άδεια για να κάνει κλήσεις API',
    'api_user_token_expired' => 'Το διακριτικό εξουσιοδότησης που χρησιμοποιείται έχει λήξει',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Σφάλμα κατά την αποστολή δοκιμαστικού email:',

    // HTTP errors
    'http_ssr_url_no_match' => 'The URL does not match the configured allowed SSR hosts',
];
