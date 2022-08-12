<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nu ai permisiunea de a accesa pagina solicitată.',
    'permissionJson' => 'Nu ai permisiunea de a efectua acțiunea solicitată.',

    // Auth
    'error_user_exists_different_creds' => 'Un utilizator cu adresa de e-mail :email există deja, dar cu acreditări diferite.',
    'email_already_confirmed' => 'E-mailul a fost deja confirmat, încearcă să te conectezi.',
    'email_confirmation_invalid' => 'Acest token de confirmare nu este valid sau a fost deja folosit, încercă să te înregistrezi din nou.',
    'email_confirmation_expired' => 'Token-ul de confirmare a expirat, a fost trimis un nou e-mail de confirmare.',
    'email_confirmation_awaiting' => 'Adresa de e-mail pentru contul utilizat trebuie să fie confirmată',
    'ldap_fail_anonymous' => 'Accesul LDAP a eșuat utilizând legătura anonimă',
    'ldap_fail_authed' => 'Accesul LDAP a eșuat folosind detaliile date dn și parolă',
    'ldap_extension_not_installed' => 'Extensia LDAP PHP nu este instalată',
    'ldap_cannot_connect' => 'Nu se poate conecta la serverul ldap, conexiunea inițială a eșuat',
    'saml_already_logged_in' => 'Deja conectat',
    'saml_user_not_registered' => 'Utilizatorul :name nu este înregistrat și înregistrarea automată este dezactivată',
    'saml_no_email_address' => 'Nu s-a putut găsi o adresă de e-mail, pentru acest utilizator, în datele furnizate de sistemul extern de autentificare',
    'saml_invalid_response_id' => 'Solicitarea de la sistemul extern de autentificare nu este recunoscută de un proces inițiat de această aplicație. Navigarea înapoi după o autentificare ar putea cauza această problemă.',
    'saml_fail_authed' => 'Autentificarea folosind :system a eșuat, sistemul nu a furnizat autorizare cu succes',
    'oidc_already_logged_in' => 'Deja conectat',
    'oidc_user_not_registered' => 'Utilizatorul :name nu este înregistrat și înregistrarea automată este dezactivată',
    'oidc_no_email_address' => 'Nu s-a putut găsi o adresă de e-mail, pentru acest utilizator, în datele furnizate de sistemul extern de autentificare',
    'oidc_fail_authed' => 'Autentificarea folosind :system a eșuat, sistemul nu a furnizat autorizare cu succes',
    'social_no_action_defined' => 'Nicio acțiune definită',
    'social_login_bad_response' => "Eroare primită în timpul autentificării :socialAccount : \n:error",
    'social_account_in_use' => 'Acest cont :socialAccount este deja utilizat, încercați să vă conectați prin intermediul opțiunii :socialCont.',
    'social_account_email_in_use' => 'E-mailul :email este deja în uz. Dacă ai deja un cont, te poți conecta la contul :socialAccount din setările profilului.',
    'social_account_existing' => 'Acest :socialAccount este deja atașat la profilul tău.',
    'social_account_already_used_existing' => 'Acest cont :socialAccount este deja utilizat de un alt utilizator.',
    'social_account_not_used' => 'Acest cont :socialAccount nu este legat de niciun utilizator. Te rugăm să îl atașezi în setările profilului. ',
    'social_account_register_instructions' => 'Dacă nu ai încă un cont, poți înregistra un cont utilizând opțiunea :socialCont.',
    'social_driver_not_found' => 'Driver social negăsit',
    'social_driver_not_configured' => 'Setările tale sociale :socialAccount nu sunt configurate corect.',
    'invite_token_expired' => 'Acest link de invitație a expirat. Poți încerca să îți resetezi parola contului.',

    // System
    'path_not_writable' => 'Calea fișierului :filePath nu a putut fi încărcată. Asigurați-vă că poate fi scrisă pe server.',
    'cannot_get_image_from_url' => 'Nu se poate obține imaginea de la :url',
    'cannot_create_thumbs' => 'Serverul nu poate crea miniaturi. Verifică dacă este instalată extensia GD PHP.',
    'server_upload_limit' => 'Serverul nu permite încărcarea acestei dimensiuni. Te rog să încerci o dimensiune mai mică a fișierului.',
    'uploaded'  => 'Serverul nu permite încărcarea acestei dimensiuni. Te rog să încerci o dimensiune mai mică a fișierului.',
    'image_upload_error' => 'A apărut o eroare la încărcarea imaginii',
    'image_upload_type_error' => 'Tipul de imagine încărcat nu este valid',
    'file_upload_timeout' => 'Încărcarea fișierului a expirat.',

    // Attachments
    'attachment_not_found' => 'Atașamentul nu a fost găsit',

    // Pages
    'page_draft_autosave_fail' => 'Nu s-a reușit salvarea ciornei. Asigură-te că ai conexiune la internet înainte de a salva această pagină',
    'page_custom_home_deletion' => 'Nu se poate șterge o pagină în timp ce este setată ca primă pagină',

    // Entities
    'entity_not_found' => 'Entitate negăsită',
    'bookshelf_not_found' => 'Raft negăsit',
    'book_not_found' => 'Carte negăsită',
    'page_not_found' => 'Pagină negăsită',
    'chapter_not_found' => 'Capitol negăsit',
    'selected_book_not_found' => 'Cartea selectată nu a fost găsită',
    'selected_book_chapter_not_found' => 'Cartea selectată sau capitolul nu a fost găsit',
    'guests_cannot_save_drafts' => 'Vizitatorii nu pot salva ciorne',

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
