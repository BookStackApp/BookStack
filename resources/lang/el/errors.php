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
    'uploaded'  => 'Ο διακομιστής δεν επιτρέπει τη μεταφόρτωση αυτού του μεγέθους. Παρακαλώ δοκιμάστε ένα μικρότερο μέγεθος αρχείου.',
    'image_upload_error' => 'Παρουσιάστηκε σφάλμα κατά το ανέβασμα της εικόνας.',
    'image_upload_type_error' => 'Ο τύπος εικόνας που μεταφορτώθηκε δεν είναι έγκυρος',
    'file_upload_timeout' => 'Το χρονικό όριο μεταφόρτωσης αρχείου έληξε.',

    // Attachments
    'attachment_not_found' => 'Το συνημμένο δεν βρέθηκε',

    // Pages
    'page_draft_autosave_fail' => 'Αποτυχία αποθήκευσης προσχέδιου. Βεβαιωθείτε ότι έχετε σύνδεση στο διαδίκτυο πριν την αποθήκευση αυτής της σελίδας',
    'page_custom_home_deletion' => 'Δεν μπορεί να διαγραφεί μια σελίδα ενώ έχει οριστεί ως αρχική σελίδα',

    // Entities
    'entity_not_found' => 'Entity not found',
    'bookshelf_not_found' => 'Shelf not found',
    'book_not_found' => 'Book not found',
    'page_not_found' => 'Page not found',
    'chapter_not_found' => 'Chapter not found',
    'selected_book_not_found' => 'The selected book was not found',
    'selected_book_chapter_not_found' => 'The selected Book or Chapter was not found',
    'guests_cannot_save_drafts' => 'Guests cannot save drafts',

    // Users
    'users_cannot_delete_only_admin' => 'You cannot delete the only admin',
    'users_cannot_delete_guest' => 'You cannot delete the guest user',

    // Roles
    'role_cannot_be_edited' => 'This role cannot be edited',
    'role_system_cannot_be_deleted' => 'This role is a system role and cannot be deleted',
    'role_registration_default_cannot_delete' => 'This role cannot be deleted while set as the default registration role',
    'role_cannot_remove_only_admin' => 'This user is the only user assigned to the administrator role. Assign the administrator role to another user before attempting to remove it here.',

    // Comments
    'comment_list' => 'An error occurred while fetching the comments.',
    'cannot_add_comment_to_draft' => 'You cannot add comments to a draft.',
    'comment_add' => 'An error occurred while adding / updating the comment.',
    'comment_delete' => 'An error occurred while deleting the comment.',
    'empty_comment' => 'Cannot add an empty comment.',

    // Error pages
    '404_page_not_found' => 'Page Not Found',
    'sorry_page_not_found' => 'Sorry, The page you were looking for could not be found.',
    'sorry_page_not_found_permission_warning' => 'If you expected this page to exist, you might not have permission to view it.',
    'image_not_found' => 'Image Not Found',
    'image_not_found_subtitle' => 'Sorry, The image file you were looking for could not be found.',
    'image_not_found_details' => 'If you expected this image to exist it might have been deleted.',
    'return_home' => 'Return to home',
    'error_occurred' => 'An Error Occurred',
    'app_down' => ':appName is down right now',
    'back_soon' => 'It will be back up soon.',

    // API errors
    'api_no_authorization_found' => 'No authorization token found on the request',
    'api_bad_authorization_format' => 'An authorization token was found on the request but the format appeared incorrect',
    'api_user_token_not_found' => 'No matching API token was found for the provided authorization token',
    'api_incorrect_token_secret' => 'The secret provided for the given used API token is incorrect',
    'api_user_no_api_permission' => 'The owner of the used API token does not have permission to make API calls',
    'api_user_token_expired' => 'The authorization token used has expired',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Error thrown when sending a test email:',

];
