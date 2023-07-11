<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nemate dopuštenje za pristup traženoj stranici.',
    'permissionJson' => 'Nemate potrebno dopuštenje.',

    // Auth
    'error_user_exists_different_creds' => 'Korisnik s mailom :email već postoji, ali s drugom vjerodajnicom.',
    'email_already_confirmed' => 'Email je već potvrđen, pokušajte se logirati.',
    'email_confirmation_invalid' => 'Ova vjerodajnica nije valjana ili je već bila korištena. Pokušajte se ponovno registrirati.',
    'email_confirmation_expired' => 'Ova vjerodajnica je istekla. Poslan je novi email za pristup.',
    'email_confirmation_awaiting' => 'Email adresa za račun koji se koristi mora biti potvrđen',
    'ldap_fail_anonymous' => 'LDAP pristup nije uspio zbog anonimnosti',
    'ldap_fail_authed' => 'LDAP pristup nije uspio',
    'ldap_extension_not_installed' => 'LDAP PHP ekstenzija nije instalirana',
    'ldap_cannot_connect' => 'Nemoguće pristupiti ldap serveru, problem s mrežom',
    'saml_already_logged_in' => 'Već ste prijavljeni',
    'saml_user_not_registered' => 'Korisnik :name nije registriran i automatska registracija je onemogućena',
    'saml_no_email_address' => 'Nismo pronašli email adresu za ovog korisnika u vanjskim sustavima',
    'saml_invalid_response_id' => 'Sustav za autentifikaciju nije prepoznat. Ovaj problem možda je nastao zbog vraćanja nakon prijave.',
    'saml_fail_authed' => 'Prijava pomoću :system nije uspjela zbog neuspješne autorizacije',
    'oidc_already_logged_in' => 'Već ste prijavljeni',
    'oidc_user_not_registered' => 'Korisnik :name nije registriran i automatska registracija je onemogućena',
    'oidc_no_email_address' => 'Nije moguće pronaći adresu e-pošte za ovog korisnika u podacima koje pruža vanjski sustav za autentifikaciju',
    'oidc_fail_authed' => 'Prijavljivanje putem :system nije uspjelo. Sustav nije uspješno odobrio autorizaciju',
    'social_no_action_defined' => 'Nije definirana nijedna radnja',
    'social_login_bad_response' => "Greška primljena prilikom prijave putem :socialAccount: :error",
    'social_account_in_use' => 'Ovaj :socialAccount račun se već koristi. Pokušajte se prijaviti pomoću :socialAccount računa.',
    'social_account_email_in_use' => 'Ovaj mail :email se već koristi. Ako već imate naš račun možete se prijaviti pomoću :socialAccount računa u postavkama vašeg profila.',
    'social_account_existing' => 'Ovaj :socialAccount je već dodan u vaš profil.',
    'social_account_already_used_existing' => 'Ovaj :socialAccount već koristi drugi korisnik.',
    'social_account_not_used' => 'Ovaj :socialAccount račun ne koristi nijedan korisnik. Dodajte ga u postavke svog profila.',
    'social_account_register_instructions' => 'Ako nemate račun možete se registrirati pomoću :socialAccount opcija.',
    'social_driver_not_found' => 'Nije pronađeno',
    'social_driver_not_configured' => 'Postavke vašeg :socialAccount računa nisu ispravno postavljene.',
    'invite_token_expired' => 'Vaša pozivnica je istekla. Pokušajte ponovno postaviti lozinku.',

    // System
    'path_not_writable' => 'Datoteka :filePath ne može se prenijeti. Učinite je lakše prepoznatljivom vašem serveru.',
    'cannot_get_image_from_url' => 'Nemoguće preuzeti sliku sa :url',
    'cannot_create_thumbs' => 'Provjerite imate li instaliranu GD PHP ekstenziju.',
    'server_upload_limit' => 'Prevelika količina za server. Pokušajte prenijeti manju veličinu.',
    'uploaded'  => 'Prevelika količina za server. Pokušajte prenijeti manju veličinu.',

    // Drawing & Images
    'image_upload_error' => 'Problem s prenosom slike',
    'image_upload_type_error' => 'Nepodržani format slike',
    'image_upload_replace_type' => 'Zamjene slikovnih datoteka moraju biti iste vrste',
    'drawing_data_not_found' => 'Podaci o crtežu se ne mogu učitati. Datoteka crteža možda više ne postoji ili nemate dozvolu za pristupanje istoj.',

    // Attachments
    'attachment_not_found' => 'Prilozi nisu pronađeni',
    'attachment_upload_error' => 'Došlo je do pogreške prilikom prijenosa datoteke privitka',

    // Pages
    'page_draft_autosave_fail' => 'Problem sa spremanjem nacrta. Osigurajte stabilnu internetsku vezu.',
    'page_draft_delete_fail' => 'Nije uspjelo brisanje privremene verzije stranice i dohvaćanje trenutno spremljenog sadržaja stranice',
    'page_custom_home_deletion' => 'Stranica označena kao naslovnica ne može se izbrisati',

    // Entities
    'entity_not_found' => 'Nije pronađeno',
    'bookshelf_not_found' => 'Polica nije pronađena',
    'book_not_found' => 'Knjiga nije pronađena',
    'page_not_found' => 'Stranica nije pronađena',
    'chapter_not_found' => 'Poglavlje nije pronađeno',
    'selected_book_not_found' => 'Odabrana knjiga nije pronađena',
    'selected_book_chapter_not_found' => 'Odabrane knjige ili poglavlja nisu pronađena',
    'guests_cannot_save_drafts' => 'Gosti ne mogu spremiti nacrte',

    // Users
    'users_cannot_delete_only_admin' => 'Ne možete izbrisati',
    'users_cannot_delete_guest' => 'Ne možete izbrisati',

    // Roles
    'role_cannot_be_edited' => 'Ne može se urediti',
    'role_system_cannot_be_deleted' => 'Sistemske postavke ne možete izbrisati',
    'role_registration_default_cannot_delete' => 'Ne može se izbrisati',
    'role_cannot_remove_only_admin' => 'Učinite drugog korisnika administratorom prije uklanjanja ove administratorske uloge.',

    // Comments
    'comment_list' => 'Pogreška prilikom dohvaćanja komentara.',
    'cannot_add_comment_to_draft' => 'Ne možete ostaviti komentar na ovaj nacrt.',
    'comment_add' => 'Greška prilikom dodavanja ili ažuriranja komentara.',
    'comment_delete' => 'Greška prilikom brisanja komentara.',
    'empty_comment' => 'Ne možete ostaviti prazan komentar.',

    // Error pages
    '404_page_not_found' => 'Stranica nije pronađena',
    'sorry_page_not_found' => 'Žao nam je, stranica koju tražite nije pronađena.',
    'sorry_page_not_found_permission_warning' => 'Ako smatrate da ova stranica još postoji, ali je ne vidite, moguće je da nemate omogućen pristup.',
    'image_not_found' => 'Slika Nije Pronađena',
    'image_not_found_subtitle' => 'Žao nam je, slikovna datoteka koju tražite nije pronađena.',
    'image_not_found_details' => 'Ako ste očekivali da ova slika postoji, moguće je da je izbrisana.',
    'return_home' => 'Povratak na početno',
    'error_occurred' => 'Došlo je do pogreške',
    'app_down' => ':appName trenutno nije dostupna',
    'back_soon' => 'Uskoro će se vratiti.',

    // API errors
    'api_no_authorization_found' => 'Nije pronađena autorizacija',
    'api_bad_authorization_format' => 'Pogreška prilikom autorizacije',
    'api_user_token_not_found' => 'Format autorizacije nije podržan',
    'api_incorrect_token_secret' => 'Netočan API token',
    'api_user_no_api_permission' => 'Vlasnik API tokena nema potrebna dopuštenja',
    'api_user_token_expired' => 'Autorizacija je istekla',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Pogreška prilikom slanja testnog email:',

];
