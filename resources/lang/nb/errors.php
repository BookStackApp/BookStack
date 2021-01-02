<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Du har ikke tilgang til å se denne siden.',
    'permissionJson' => 'Du har ikke tilgang til å utføre denne handlingen.',

    // Auth
    'error_user_exists_different_creds' => 'En konto med :email finnes allerede, men har andre detaljer.',
    'email_already_confirmed' => 'E-posten er allerede bekreftet, du kan forsøke å logge inn.',
    'email_confirmation_invalid' => 'Denne bekreftelseskoden er allerede benyttet eller utgått. Prøv å registrere på nytt.',
    'email_confirmation_expired' => 'Bekreftelseskoden er allerede utgått, en ny e-post er sendt.',
    'email_confirmation_awaiting' => 'Du må bekrefte e-posten for denne kontoen.',
    'ldap_fail_anonymous' => 'LDAP kan ikke benyttes med anonym tilgang for denne tjeneren.',
    'ldap_fail_authed' => 'LDAP tilgang feilet med angitt DN',
    'ldap_extension_not_installed' => 'LDAP PHP modulen er ikke installert.',
    'ldap_cannot_connect' => 'Klarer ikke koble til LDAP på denne adressen',
    'saml_already_logged_in' => 'Allerede logget inn',
    'saml_user_not_registered' => 'Kontoen med navn :name er ikke registert, registrering er også deaktivert.',
    'saml_no_email_address' => 'Denne kontoinformasjonen finnes ikke i det eksterne autentiseringssystemet.',
    'saml_invalid_response_id' => 'Forespørselen fra det eksterne autentiseringssystemet gjenkjennes ikke av en prosess som startes av dette programmet. Å navigere tilbake etter pålogging kan forårsake dette problemet.',
    'saml_fail_authed' => 'Innlogging gjennom :system feilet. Fikk ikke kontakt med autentiseringstjeneren.',
    'social_no_action_defined' => 'Ingen handlinger er definert',
    'social_login_bad_response' => "Feilmelding mottat fra :socialAccount innloggingstjeneste: \n:error",
    'social_account_in_use' => 'Denne :socialAccount kontoen er allerede registrert, Prøv å logge inn med :socialAccount alternativet.',
    'social_account_email_in_use' => 'E-posten :email er allerede i bruk. Har du allerede en konto hos :socialAccount kan dette angis fra profilsiden din.',
    'social_account_existing' => 'Denne :socialAccount er allerede koblet til din konto.',
    'social_account_already_used_existing' => 'This :socialAccount account is already used by another user.',
    'social_account_not_used' => 'Denne :socialAccount konten er ikke koblet til noen konto, angi denne i profilinnstillingene dine. ',
    'social_account_register_instructions' => 'Har du ikke en konto her ennå, kan du benytte :socialAccount alternativet for å registrere deg.',
    'social_driver_not_found' => 'Autentiseringstjeneste fra sosiale medier er ikke installert',
    'social_driver_not_configured' => 'Dine :socialAccount innstilliner er ikke angitt.',
    'invite_token_expired' => 'Invitasjonslenken har utgått, du kan forsøke å be om nytt passord istede.',

    // System
    'path_not_writable' => 'Filstien :filePath aksepterer ikke filer, du må sjekke filstitilganger i systemet.',
    'cannot_get_image_from_url' => 'Kan ikke hente bilde fra :url',
    'cannot_create_thumbs' => 'Kan ikke opprette miniatyrbilder. GD PHP er ikke installert.',
    'server_upload_limit' => 'Vedlegget er for stort, forsøk med et mindre vedlegg.',
    'uploaded'  => 'Tjenesten aksepterer ikke vedlegg som er så stor.',
    'image_upload_error' => 'Bildet kunne ikke lastes opp, forsøk igjen.',
    'image_upload_type_error' => 'Bildeformatet støttes ikke, forsøk med et annet format.',
    'file_upload_timeout' => 'Opplastingen gikk ut på tid.',

    // Attachments
    'attachment_not_found' => 'Vedlegget ble ikke funnet',

    // Pages
    'page_draft_autosave_fail' => 'Kunne ikke lagre utkastet, forsikre deg om at du er tilkoblet tjeneren (Har du nettilgang?)',
    'page_custom_home_deletion' => 'Kan ikke slette en side som er satt som forside.',

    // Entities
    'entity_not_found' => 'Entitet ble ikke funnet',
    'bookshelf_not_found' => 'Bokhyllen ble ikke funnet',
    'book_not_found' => 'Boken ble ikke funnet',
    'page_not_found' => 'Siden ble ikke funnet',
    'chapter_not_found' => 'Kapittel ble ikke funnet',
    'selected_book_not_found' => 'Den valgte boken eksisterer ikke',
    'selected_book_chapter_not_found' => 'Den valgte boken eller kapittelet eksisterer ikke',
    'guests_cannot_save_drafts' => 'Gjester kan ikke lagre utkast',

    // Users
    'users_cannot_delete_only_admin' => 'Du kan ikke kaste ut den eneste administratoren',
    'users_cannot_delete_guest' => 'Du kan ikke slette gjestebrukeren (Du kan deaktivere offentlig visning istede)',

    // Roles
    'role_cannot_be_edited' => 'Denne rollen kan ikke endres',
    'role_system_cannot_be_deleted' => 'Denne systemrollen kan ikke slettes',
    'role_registration_default_cannot_delete' => 'Du kan ikke slette en rolle som er satt som registreringsrolle (rollen nye kontoer får når de registrerer seg)',
    'role_cannot_remove_only_admin' => 'Denne brukeren er den eneste brukeren som er tildelt administratorrollen. Tilordne administratorrollen til en annen bruker før du prøver å fjerne den her.',

    // Comments
    'comment_list' => 'Det oppstod en feil under henting av kommentarene.',
    'cannot_add_comment_to_draft' => 'Du kan ikke legge til kommentarer i et utkast.',
    'comment_add' => 'Det oppsto en feil da kommentaren skulle legges til / oppdateres.',
    'comment_delete' => 'Det oppstod en feil under sletting av kommentaren.',
    'empty_comment' => 'Kan ikke legge til en tom kommentar.',

    // Error pages
    '404_page_not_found' => 'Siden finnes ikke',
    'sorry_page_not_found' => 'Beklager, siden du leter etter ble ikke funnet.',
    'sorry_page_not_found_permission_warning' => 'Hvis du forventet at denne siden skulle eksistere, har du kanskje ikke tillatelse til å se den.',
    'return_home' => 'Gå til hovedside',
    'error_occurred' => 'En feil oppsto',
    'app_down' => ':appName er nede for øyeblikket',
    'back_soon' => 'Den vil snart komme tilbake.',

    // API errors
    'api_no_authorization_found' => 'Ingen autorisasjonstoken ble funnet på forespørselen',
    'api_bad_authorization_format' => 'Det ble funnet et autorisasjonstoken på forespørselen, men formatet virket feil',
    'api_user_token_not_found' => 'Ingen samsvarende API-token ble funnet for det angitte autorisasjonstokenet',
    'api_incorrect_token_secret' => 'Hemmeligheten som er gitt for det gitte brukte API-tokenet er feil',
    'api_user_no_api_permission' => 'Eieren av det brukte API-tokenet har ikke tillatelse til å ringe API-samtaler',
    'api_user_token_expired' => 'Autorisasjonstokenet som er brukt, har utløpt',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Feil kastet når du sendte en test-e-post:',

];
