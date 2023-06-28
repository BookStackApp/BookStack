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

    // Drawing & Images
    'image_upload_error' => 'A apărut o eroare la încărcarea imaginii',
    'image_upload_type_error' => 'Tipul de imagine încărcat nu este valid',
    'image_upload_replace_type' => 'Inlocuirea fisierului de imagine trebuie sa fie de acelasi tip',
    'drawing_data_not_found' => 'Drawing data could not be loaded. The drawing file might no longer exist or you may not have permission to access it.',

    // Attachments
    'attachment_not_found' => 'Atașamentul nu a fost găsit',
    'attachment_upload_error' => 'A apărut o eroare la încărcarea atașamentului',

    // Pages
    'page_draft_autosave_fail' => 'Nu s-a reușit salvarea ciornei. Asigură-te că ai conexiune la internet înainte de a salva această pagină',
    'page_draft_delete_fail' => 'Nu s-a putut șterge ciorna paginii și prelua pagina curentă salvată',
    'page_custom_home_deletion' => 'Nu se poate șterge o pagină în timp ce este setată ca primă pagină',

    // Entities
    'entity_not_found' => 'Entitate negăsită',
    'bookshelf_not_found' => 'Raftul nu a fost găsit',
    'book_not_found' => 'Carte negăsită',
    'page_not_found' => 'Pagină negăsită',
    'chapter_not_found' => 'Capitol negăsit',
    'selected_book_not_found' => 'Cartea selectată nu a fost găsită',
    'selected_book_chapter_not_found' => 'Cartea selectată sau capitolul nu a fost găsit',
    'guests_cannot_save_drafts' => 'Vizitatorii nu pot salva ciorne',

    // Users
    'users_cannot_delete_only_admin' => 'Nu poți șterge singurul administrator',
    'users_cannot_delete_guest' => 'Nu se poate șterge utilizatorul "Vizitator"',

    // Roles
    'role_cannot_be_edited' => 'Acest rol nu poate fi editat',
    'role_system_cannot_be_deleted' => 'Acest rol este un rol de sistem și nu poate fi șters',
    'role_registration_default_cannot_delete' => 'Acest rol nu poate fi șters când este setat ca rol implicit de înregistrare',
    'role_cannot_remove_only_admin' => 'Acest utilizator este singurul utilizator atribuit rolului de administrator. Atribuiți rolul de administrator unui alt utilizator înainte de a-l elimina aici.',

    // Comments
    'comment_list' => 'A apărut o eroare la preluarea comentariilor.',
    'cannot_add_comment_to_draft' => 'Nu poți adăuga comentarii la o ciornă.',
    'comment_add' => 'A apărut o eroare la adăugarea / actualizarea comentariului.',
    'comment_delete' => 'A apărut o eroare la ștergerea comentariului.',
    'empty_comment' => 'Nu se poate adăuga un comentariu gol.',

    // Error pages
    '404_page_not_found' => 'Pagina nu a fost găsită',
    'sorry_page_not_found' => 'Ne pare rău, pagina pe care o cauți nu a putut fi găsită.',
    'sorry_page_not_found_permission_warning' => 'Dacă te aștepți ca această pagină să existe, s-ar putea să nu ai permisiunea de a o vizualiza.',
    'image_not_found' => 'Imagine negăsită',
    'image_not_found_subtitle' => 'Ne pare rău, fișierul de imagine pe care îl cauți nu a putut fi găsit.',
    'image_not_found_details' => 'Dacă te aștepți ca această imagine să existe, e posibil să fie ștearsă.',
    'return_home' => 'Întoarce-te acasă',
    'error_occurred' => 'A apărut o eroare',
    'app_down' => ':appName nu funcționează acum',
    'back_soon' => 'Va reveni în curând.',

    // API errors
    'api_no_authorization_found' => 'Nu s-a găsit niciun token de autorizare la cerere',
    'api_bad_authorization_format' => 'A fost găsit un token de autorizare, dar formatul este incorect',
    'api_user_token_not_found' => 'Nu a fost găsit niciun token API potrivit pentru codul de autorizare furnizat',
    'api_incorrect_token_secret' => 'Secretul furnizat pentru token-ul API folosit este incorect',
    'api_user_no_api_permission' => 'Proprietarul token-ului API folosit nu are permisiunea de a efectua apeluri API',
    'api_user_token_expired' => 'Token-ul de autorizare utilizat a expirat',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Eroare la trimiterea unui e-mail de test:',

];
