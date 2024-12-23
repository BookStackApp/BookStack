<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Non hai il permesso di accedere alla pagina richiesta.',
    'permissionJson' => 'Non hai il permesso di eseguire l\'azione richiesta.',

    // Auth
    'error_user_exists_different_creds' => 'Un utente con la mail :email esiste già ma con credenziali differenti.',
    'auth_pre_register_theme_prevention' => 'Non è stato possibile registrare l\'account utente coi dettagli forniti',
    'email_already_confirmed' => 'La mail è già stata confermata, esegui il login.',
    'email_confirmation_invalid' => 'Questo token di conferma non è valido o già stato utilizzato, registrati nuovamente.',
    'email_confirmation_expired' => 'Il token di conferma è scaduto, è stata inviata una nuova mail di conferma.',
    'email_confirmation_awaiting' => 'L\'indirizzo email per l\'account in uso deve essere confermato',
    'ldap_fail_anonymous' => 'Accesso LDAP fallito usando bind anonimo',
    'ldap_fail_authed' => 'Accesso LDAP fallito usando il dn e la password inseriti',
    'ldap_extension_not_installed' => 'L\'estensione PHP LDAP non è installata',
    'ldap_cannot_connect' => 'Impossibile connettersi al server ldap, connessione iniziale fallita',
    'saml_already_logged_in' => 'Già loggato',
    'saml_no_email_address' => 'Impossibile trovare un indirizzo email per questo utente nei dati forniti dal sistema di autenticazione esterno',
    'saml_invalid_response_id' => 'La richiesta dal sistema di autenticazione esterno non è riconosciuta da un processo iniziato da questa applicazione. Tornare indietro dopo un login potrebbe causare questo problema.',
    'saml_fail_authed' => 'Accesso con :system non riuscito, il sistema non ha fornito l\'autorizzazione corretta',
    'oidc_already_logged_in' => 'Hai già effettuato il login',
    'oidc_no_email_address' => 'Impossibile trovare un indirizzo email, per questo utente, nei dati forniti dal sistema di autenticazione esterno',
    'oidc_fail_authed' => 'Accesso con :system non riuscito, il sistema non ha fornito l\'autorizzazione',
    'social_no_action_defined' => 'Nessuna azione definita',
    'social_login_bad_response' => "Ricevuto errore durante il login con :socialAccount : \n:error",
    'social_account_in_use' => 'Questo account :socialAccount è già utilizzato, prova a loggarti usando l\'opzione :socialAccount.',
    'social_account_email_in_use' => 'La mail :email è già in uso. Se hai già un account puoi connettere il tuo account :socialAccount dalle impostazioni del tuo profilo.',
    'social_account_existing' => 'Questo account :socialAccount è già connesso al tuo profilo.',
    'social_account_already_used_existing' => 'Questo account :socialAccount è già utilizzato da un altro utente.',
    'social_account_not_used' => 'Questo account :socialAccount non è collegato a nessun utente. Collegalo nelle impostazioni del profilo. ',
    'social_account_register_instructions' => 'Se non hai ancora un account, puoi registrarti usando l\'opzione :socialAccount.',
    'social_driver_not_found' => 'Driver social non trovato',
    'social_driver_not_configured' => 'Le impostazioni di :socialAccount non sono configurate correttamente.',
    'invite_token_expired' => 'Il link di invito è scaduto. Puoi provare a resettare la password del tuo account.',
    'login_user_not_found' => 'Impossibile trovare un utente per questa azione.',

    // System
    'path_not_writable' => 'Il percorso :filePath non è scrivibile. Controlla che abbia i permessi corretti.',
    'cannot_get_image_from_url' => 'Impossibile scaricare immagine da :url',
    'cannot_create_thumbs' => 'Il server non può creare miniature. Controlla che l\'estensione GD sia installata.',
    'server_upload_limit' => 'Il server non permette un upload di questa grandezza. Prova con un file più piccolo.',
    'server_post_limit' => 'Il server non può ricevere la quantità di dati fornita. Riprovare con meno dati o con un file più piccolo.',
    'uploaded'  => 'Il server non consente upload di questa grandezza. Prova un file più piccolo.',

    // Drawing & Images
    'image_upload_error' => 'Si è verificato un errore nel caricamento dell\'immagine',
    'image_upload_type_error' => 'Il tipo di immagine caricata non è valido',
    'image_upload_replace_type' => 'Le sostituzioni di file immagine devono essere dello stesso tipo',
    'image_upload_memory_limit' => 'Impossibile gestire il caricamento d\'immagini e/o creare miniature a causa dei limiti delle risorse di sistema.',
    'image_thumbnail_memory_limit' => 'Impossibile creare variazioni delle dimensioni dell\'immagine a causa dei limiti delle risorse di sistema.',
    'image_gallery_thumbnail_memory_limit' => 'Impossibile creare le miniature della galleria a causa dei limiti delle risorse di sistema.',
    'drawing_data_not_found' => 'Non è stato possibile caricare i dati del disegno. È possibile che il file del disegno non esista più o che non si abbia il permesso di accedervi.',

    // Attachments
    'attachment_not_found' => 'Allegato non trovato',
    'attachment_upload_error' => 'Si è verificato un errore durante il caricamento del file allegato',

    // Pages
    'page_draft_autosave_fail' => 'Impossibile salvare la bozza. Controlla di essere connesso ad internet prima di salvare questa pagina',
    'page_draft_delete_fail' => 'Impossibile eliminare la bozza di pagina e recuperare i contenuti salvati nella pagina corrente',
    'page_custom_home_deletion' => 'Impossibile eliminare una pagina quando è impostata come homepage',

    // Entities
    'entity_not_found' => 'Entità non trovata',
    'bookshelf_not_found' => 'Libreria non trovata',
    'book_not_found' => 'Libro non trovato',
    'page_not_found' => 'Pagina non trovata',
    'chapter_not_found' => 'Capitolo non trovato',
    'selected_book_not_found' => 'Il libro selezionato non è stato trovato',
    'selected_book_chapter_not_found' => 'Il libro o il capitolo selezionati non sono stati trovati',
    'guests_cannot_save_drafts' => 'Gli ospiti non possono salvare bozze',

    // Users
    'users_cannot_delete_only_admin' => 'Non puoi eliminare l\'unico admin',
    'users_cannot_delete_guest' => 'Non puoi eliminare l\'utente ospite',
    'users_could_not_send_invite' => 'Impossibile creare l\'utente poiché l\'invio dell\'email di invito non è riuscito',

    // Roles
    'role_cannot_be_edited' => 'Questo ruolo non può essere modificato',
    'role_system_cannot_be_deleted' => 'Questo ruolo è di sistema e non può essere eliminato',
    'role_registration_default_cannot_delete' => 'Questo ruolo non può essere eliminato finchè è impostato come ruolo di registrazione predefinito',
    'role_cannot_remove_only_admin' => 'Questo utente è l\'unico con assegnato il ruolo di amministratore. Assegna il ruolo di amministratore ad un altro utente prima di rimuoverlo.',

    // Comments
    'comment_list' => 'Si è verificato un errore durante il recupero dei commenti.',
    'cannot_add_comment_to_draft' => 'Non puoi aggiungere commenti a una bozza.',
    'comment_add' => 'Si è verificato un errore durante l\'aggiunta / l\'aggiornamento del commento.',
    'comment_delete' => 'Si è verificato un errore durante l’eliminazione del commento.',
    'empty_comment' => 'Impossibile aggiungere un commento vuoto.',

    // Error pages
    '404_page_not_found' => 'Pagina non trovata',
    'sorry_page_not_found' => 'Siamo spiacenti, la pagina che stavi cercando non è stata trovata.',
    'sorry_page_not_found_permission_warning' => 'Se pensi che questa pagina possa esistere, potresti non avere i permessi per visualizzarla.',
    'image_not_found' => 'Immagine non trovata',
    'image_not_found_subtitle' => 'Spiacente, l\'immagine che stai cercando non è stata trovata.',
    'image_not_found_details' => 'Se pensi che questa immagine possa esistere, potrebbe essere stata cancellata.',
    'return_home' => 'Ritorna alla home',
    'error_occurred' => 'Si è verificato un errore',
    'app_down' => ':appName è offline al momento',
    'back_soon' => 'Tornerà presto online.',

    // Import
    'import_zip_cant_read' => 'Impossibile leggere il file ZIP.',
    'import_zip_cant_decode_data' => 'Impossibile trovare e decodificare il contenuto ZIP data.json.',
    'import_zip_no_data' => 'I dati del file ZIP non hanno il contenuto previsto di libri, capitoli o pagine.',
    'import_validation_failed' => 'L\'importazione ZIP non è stata convalidata con errori:',
    'import_zip_failed_notification' => 'Impossibile importare il file ZIP.',
    'import_perms_books' => 'Non hai i permessi necessari per creare libri.',
    'import_perms_chapters' => 'Non hai i permessi necessari per creare capitoli.',
    'import_perms_pages' => 'Non hai i permessi necessari per creare pagine.',
    'import_perms_images' => 'Non hai i permessi necessari per creare immagini.',
    'import_perms_attachments' => 'Non hai il permesso necessario per creare allegati.',

    // API errors
    'api_no_authorization_found' => 'Nessun token di autorizzazione trovato nella richiesta',
    'api_bad_authorization_format' => 'Un token di autorizzazione è stato trovato nella richiesta, ma il formato sembra non corretto',
    'api_user_token_not_found' => 'Nessun token API valido è stato trovato nel token di autorizzazione fornito',
    'api_incorrect_token_secret' => 'Il token segreto fornito per il token API utilizzato non è corretto',
    'api_user_no_api_permission' => 'Il proprietario del token API utilizzato non ha il permesso di effettuare chiamate API',
    'api_user_token_expired' => 'Il token di autorizzazione utilizzato è scaduto',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Si è verificato un errore durante l\'invio di una e-mail di prova:',

    // HTTP errors
    'http_ssr_url_no_match' => 'L\'URL non corrisponde agli host SSR configurati',
];
