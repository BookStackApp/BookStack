<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'No teniu permís per a accedir a la pàgina sol·licitada.',
    'permissionJson' => 'No teniu permís per a executar l’acció sol·licitada.',

    // Auth
    'error_user_exists_different_creds' => 'Ja existeix un usuari amb el correu electrònic :email però amb unes credencials diferents.',
    'auth_pre_register_theme_prevention' => 'El compte d\'usuari no s\'ha pogut registrar amb els detalls proporcionats',
    'email_already_confirmed' => 'L’adreça electrònica ja està confirmada. Proveu d’iniciar la sessió.',
    'email_confirmation_invalid' => 'Aquest testimoni de confirmació no és vàlid o ja s’ha utilitzat. Proveu de tornar-vos a registrar.',
    'email_confirmation_expired' => 'Aquest testimoni de confirmació ha caducat. S’ha enviat un altre correu electrònic de confirmació.',
    'email_confirmation_awaiting' => 'Cal confirmar l’adreça electrònica del compte que utilitzeu',
    'ldap_fail_anonymous' => 'L’accés LDAP anònim ha fallat',
    'ldap_fail_authed' => 'L’accés LDAP amb el nom distintiu i la contrasenya proporcionats ha fallat',
    'ldap_extension_not_installed' => 'L’extensió PHP de l’LDAP no està instal·lada',
    'ldap_cannot_connect' => 'No s’ha pogut connectar amb el servidor LDAP perquè la connexió inicial ha fallat',
    'saml_already_logged_in' => 'Ja heu iniciat sessió',
    'saml_no_email_address' => 'No s’ha pogut trobar una adreça electrònica per a aquest usuari a les dades proporcionades pel sistema d’autenticació extern',
    'saml_invalid_response_id' => 'Un procés iniciat per aquesta aplicació no reconeix la sol·licitud del sistema d’autenticació extern. Haver navegat enrere després d’iniciar sessió en podria ser la causa.',
    'saml_fail_authed' => 'No s’ha pogut iniciar sessió amb :system perquè el sistema no ha proporcionat una autorització satisfactòria',
    'oidc_already_logged_in' => 'Ja heu iniciat sessió',
    'oidc_no_email_address' => 'No s’ha pogut trobar una adreça electrònica per a aquest usuari a les dades proporcionades pel sistema d’autenticació extern',
    'oidc_fail_authed' => 'No s’ha pogut iniciar sessió amb :system perquè el sistema no ha proporcionat una autorització satisfactòria',
    'social_no_action_defined' => 'No s’ha definit cap acció',
    'social_login_bad_response' => "S’ha produït un error en l’inici de sessió amb :socialAccount: \n:error",
    'social_account_in_use' => 'Aquest compte de :socialAccount ja s’està utilitzant. Proveu d’iniciar sessió amb :socialAccount.',
    'social_account_email_in_use' => 'L’adreça electrònica :email ja està en ús. Si ja teniu un compte, podeu connectar-hi el vostre compte de :socialAccount a la configuració del vostre perfil.',
    'social_account_existing' => 'Aquest compte de :socialAccount ja està associat al vostre perfil.',
    'social_account_already_used_existing' => 'Aquest compte de :socialAccount ja està associat a un altre usuari.',
    'social_account_not_used' => 'Aquest compte de :socialAccount no està associat a cap usuari. Associeu-lo a la configuració del vostre perfil. ',
    'social_account_register_instructions' => 'Si encara no teniu un compte, podeu registrar-vos amb l’opció :socialAccount.',
    'social_driver_not_found' => 'No s’ha trobat el controlador social',
    'social_driver_not_configured' => 'La configuració de :socialAccount no és correcta.',
    'invite_token_expired' => 'Aquest enllaç d’invitació ha caducat. Podeu provar de restablir la contrasenya del vostre compte.',
    'login_user_not_found' => 'No s\'ha pogut trobar un usuari per aquesta acció.',

    // System
    'path_not_writable' => 'No s’ha pogut pujar a :filePath. Assegureu-vos que teniu permisos d’escriptura al servidor.',
    'cannot_get_image_from_url' => 'No s’ha pogut obtenir la imatge des de :url',
    'cannot_create_thumbs' => 'El servidor no pot crear miniatures. Assegureu-vos que s’ha instal·lat l’extensió de GD PHP.',
    'server_upload_limit' => 'El servidor no permet pujades d’aquesta mida. Proveu-ho amb una mida més petita.',
    'server_post_limit' => 'El servidor no pot rebre la quantitat de dades proporcionades. Proveu-ho amb menys dades o amb un fitxer més petit.',
    'uploaded'  => 'El servidor no permet pujades d’aquesta mida. Proveu-ho amb un fitxer més petit.',

    // Drawing & Images
    'image_upload_error' => 'S’ha produït un error en pujar la imatge.',
    'image_upload_type_error' => 'El tipus d’imatge no és vàlid.',
    'image_upload_replace_type' => 'Els fitxers d’imatge s’han de substituir per un del mateix tipus',
    'image_upload_memory_limit' => 'No s’ha pogut pujar la imatge o crear-ne les miniatures a causa dels límits dels recursos del sistema.',
    'image_thumbnail_memory_limit' => 'No s’han pogut crear mides d’imatge diferents a causa dels límits dels recursos del sistema.',
    'image_gallery_thumbnail_memory_limit' => 'No s’han pogut crear les miniatures de la galeria a causa dels límits dels recursos del sistema.',
    'drawing_data_not_found' => 'No s’han pogut pujar les dades del dibuix. És possible que el fitxer del dibuix ja no existeixi o que no tingueu permís per a accedir-hi.',

    // Attachments
    'attachment_not_found' => 'No s’ha trobat el fitxer adjunt',
    'attachment_upload_error' => 'S’ha produït un error en pujar el fitxer adjunt',

    // Pages
    'page_draft_autosave_fail' => 'No s’ha pogut desar l’esborrany. Assegureu-vos que esteu connectat a internet per a desar la pàgina.',
    'page_draft_delete_fail' => 'No s’ha pogut suprimir l’esborrany de la pàgina i obtenir el contingut de la pàgina desada actual.',
    'page_custom_home_deletion' => 'No es pot suprimir una pàgina que està definida com a pàgina d’inici.',

    // Entities
    'entity_not_found' => 'No s’ha trobat l’entitat.',
    'bookshelf_not_found' => 'No s’ha trobat el prestatge.',
    'book_not_found' => 'No s’ha trobat el llibre.',
    'page_not_found' => 'No s’ha trobat la pàgina.',
    'chapter_not_found' => 'No s’ha trobat el capítol.',
    'selected_book_not_found' => 'No s’ha trobat el llibre seleccionat.',
    'selected_book_chapter_not_found' => 'No s’ha trobat el llibre o el capítol seleccionat.',
    'guests_cannot_save_drafts' => 'Els convidats no poden desar esborranys.',

    // Users
    'users_cannot_delete_only_admin' => 'No podeu suprimir l’administrador únic.',
    'users_cannot_delete_guest' => 'No podeu suprimir l’usuari convidat.',
    'users_could_not_send_invite' => 'No s\'ha pogut crear l\'usuari, ja que no s\'ha pogut enviar el correu d\'invitació',

    // Roles
    'role_cannot_be_edited' => 'No es pot editar aquest rol.',
    'role_system_cannot_be_deleted' => 'No es pot suprimir aquest rol perquè és un rol del sistema.',
    'role_registration_default_cannot_delete' => 'No es pot suprimir aquest rol mentre estigui configurat com a rol de registre per defecte.',
    'role_cannot_remove_only_admin' => 'Aquest és l’únic usuari assignat al rol d’administrador. Assigneu el rol d’administrador a un altre usuari abans de provar de suprimir-lo.',

    // Comments
    'comment_list' => 'S’ha produït un error en obtenir els comentaris.',
    'cannot_add_comment_to_draft' => 'No es poden afegir comentaris en un esborrany.',
    'comment_add' => 'S’ha produït un error en afegir o actualitzar el comentari.',
    'comment_delete' => 'S’ha produït un error en suprimir el comentari.',
    'empty_comment' => 'No es pot afegir un comentari buit.',

    // Error pages
    '404_page_not_found' => 'No s’ha trobat la pàgina',
    'sorry_page_not_found' => 'No s’ha trobat la pàgina que heu cercat.',
    'sorry_page_not_found_permission_warning' => 'Si la pàgina existeix, és possible que no tingueu permís per a accedir-hi.',
    'image_not_found' => 'No s’ha trobat la imatge',
    'image_not_found_subtitle' => 'No s’ha trobat la imatge que heu cercat.',
    'image_not_found_details' => 'És possible que s’hagi suprimit.',
    'return_home' => 'Torna a la pàgina d’inici',
    'error_occurred' => 'S’ha produït un error.',
    'app_down' => ':appName està fora de servei.',
    'back_soon' => 'Aviat ho arreglarem.',

    // Import
    'import_zip_cant_read' => 'No es pot llegir el fitxer ZIP.',
    'import_zip_cant_decode_data' => 'No s\'ha pogut trobar i descodificar el fitxer data.json en el fitxer ZIP.',
    'import_zip_no_data' => 'Les dades del fitxer ZIP no contenen cap llibre, capítol o contingut de pàgina.',
    'import_validation_failed' => 'Error en validar la importació del ZIP amb els errors:',
    'import_zip_failed_notification' => 'Error en importar l\'arxiu ZIP.',
    'import_perms_books' => 'Li falten els permisos necessaris per crear llibres.',
    'import_perms_chapters' => 'Li falten els permisos necessaris per crear capítols.',
    'import_perms_pages' => 'Li falten els permisos necessaris per crear pàgines.',
    'import_perms_images' => 'Li falten els permisos necessaris per crear imatges.',
    'import_perms_attachments' => 'Li falten els permisos necessaris per crear adjunts.',

    // API errors
    'api_no_authorization_found' => 'No s’ha trobat cap testimoni d’autorització en aquesta sol·licitud.',
    'api_bad_authorization_format' => 'S’ha trobat un testimoni d’autorització en aquesta sol·licitud però no tenia el format correcte.',
    'api_user_token_not_found' => 'No s’ha trobat cap testimoni d’API per al testimoni d’autorització proporcionat.',
    'api_incorrect_token_secret' => 'El secret proporcionat per al testimoni d’API utilitzat no és correcte.',
    'api_user_no_api_permission' => 'El propietari del testimoni API utilitzat no té permís per a fer crides a l’API.',
    'api_user_token_expired' => 'El testimoni d’autorització utilitzat ha caducat.',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'S’ha produït un error en enviar el correu electrònic de prova:',

    // HTTP errors
    'http_ssr_url_no_match' => 'L’URL no coincideix amb els amfitrions SSR configurats permesos.',
];
