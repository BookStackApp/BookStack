<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'No teniu permís per a accedir a la pàgina sol·licitada.',
    'permissionJson' => 'No teniu permís per a executar l\'acció sol·licitada.',

    // Auth
    'error_user_exists_different_creds' => 'Ja hi ha un usuari amb l\'adreça electrònica :email però amb credencials diferents.',
    'email_already_confirmed' => 'L\'adreça electrònica ja està confirmada. Proveu d\'iniciar la sessió.',
    'email_confirmation_invalid' => 'Aquest testimoni de confirmació no és vàlid o ja ha estat utilitzat. Proveu de tornar-vos a registrar.',
    'email_confirmation_expired' => 'El testimoni de confirmació ha caducat. S\'ha enviat un nou correu electrònic de confirmació.',
    'email_confirmation_awaiting' => 'Cal confirmar l\'adreça electrònica del compte que utilitzeu',
    'ldap_fail_anonymous' => 'L\'accés a l\'LDAP ha fallat fent servir un lligam anònim',
    'ldap_fail_authed' => 'L\'accés a l\'LDAP ha fallat fent servir els detalls de DN i contrasenya proporcionats',
    'ldap_extension_not_installed' => 'L\'extensió de l\'LDAP de PHP no està instal·lada',
    'ldap_cannot_connect' => 'No s\'ha pogut connectar amb el servidor de l\'LDAP, la connexió inicial ha fallat',
    'saml_already_logged_in' => 'Ja heu iniciat la sessió',
    'saml_user_not_registered' => 'L\'usuari :name no està registrat i els registres automàtics estan desactivats',
    'saml_no_email_address' => 'No s\'ha pogut trobar cap adreça electrònica, per a aquest usuari, en les dades proporcionades pel sistema d\'autenticació extern',
    'saml_invalid_response_id' => 'La petició del sistema d\'autenticació extern no és reconeguda per un procés iniciat per aquesta aplicació. Aquest problema podria ser causat per navegar endarrere després d\'iniciar la sessió.',
    'saml_fail_authed' => 'L\'inici de sessió fent servir :system ha fallat, el sistema no ha proporcionat una autorització satisfactòria',
    'oidc_already_logged_in' => 'Already logged in',
    'oidc_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'oidc_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'oidc_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'social_no_action_defined' => 'No hi ha cap acció definida',
    'social_login_bad_response' => "S'ha rebut un error mentre s'iniciava la sessió amb :socialAccount: \n:error",
    'social_account_in_use' => 'Aquest compte de :socialAccount ja està en ús, proveu d\'iniciar la sessió mitjançant l\'opció de :socialAccount.',
    'social_account_email_in_use' => 'L\'adreça electrònica :email ja està en ús. Si ja teniu un compte, podeu connectar-hi el vostre compte de :socialAccount a la configuració del vostre perfil.',
    'social_account_existing' => 'Aquest compte de :socialAccount ja està associat al vostre perfil.',
    'social_account_already_used_existing' => 'Aquest compte de :socialAccount ja el fa servir un altre usuari.',
    'social_account_not_used' => 'Aquest compte de :socialAccount no està associat a cap usuari. Associeu-lo a la configuració del vostre perfil. ',
    'social_account_register_instructions' => 'Si encara no teniu cap compte, podeu registrar-vos fent servir l\'opció de :socialAccount.',
    'social_driver_not_found' => 'No s\'ha trobat el controlador social',
    'social_driver_not_configured' => 'La configuració social de :socialAccount no és correcta.',
    'invite_token_expired' => 'Aquest enllaç d\'invitació ha caducat. Podeu provar de restablir la contrasenya del vostre compte.',

    // System
    'path_not_writable' => 'No s\'ha pogut pujar al camí del fitxer :filePath. Assegureu-vos que el servidor hi té permisos d\'escriptura.',
    'cannot_get_image_from_url' => 'No s\'ha pogut obtenir la imatge de :url',
    'cannot_create_thumbs' => 'El servidor no pot crear miniatures. Reviseu que tingueu instal·lada l\'extensió GD del PHP.',
    'server_upload_limit' => 'El servidor no permet pujades d\'aquesta mida. Proveu-ho amb una mida de fitxer més petita.',
    'server_post_limit' => 'The server cannot receive the provided amount of data. Try again with less data or a smaller file.',
    'uploaded'  => 'El servidor no permet pujades d\'aquesta mida. Proveu-ho amb una mida de fitxer més petita.',

    // Drawing & Images
    'image_upload_error' => 'S\'ha produït un error en pujar la imatge',
    'image_upload_type_error' => 'El tipus d\'imatge que heu pujat no és vàlid',
    'image_upload_replace_type' => 'Image file replacements must be of the same type',
    'image_upload_memory_limit' => 'Failed to handle image upload and/or create thumbnails due to system resource limits.',
    'image_thumbnail_memory_limit' => 'Failed to create image size variations due to system resource limits.',
    'image_gallery_thumbnail_memory_limit' => 'Failed to create gallery thumbnails due to system resource limits.',
    'drawing_data_not_found' => 'Drawing data could not be loaded. The drawing file might no longer exist or you may not have permission to access it.',

    // Attachments
    'attachment_not_found' => 'No s\'ha trobat l\'adjunció',
    'attachment_upload_error' => 'An error occurred uploading the attachment file',

    // Pages
    'page_draft_autosave_fail' => 'No s\'ha pogut desar l\'esborrany. Assegureu-vos que tingueu connexió a Internet abans de desar la pàgina',
    'page_draft_delete_fail' => 'Failed to delete page draft and fetch current page saved content',
    'page_custom_home_deletion' => 'No es pot suprimir una pàgina mentre estigui definida com a pàgina d\'inici',

    // Entities
    'entity_not_found' => 'No s\'ha trobat l\'entitat',
    'bookshelf_not_found' => 'Shelf not found',
    'book_not_found' => 'No s\'ha trobat el llibre',
    'page_not_found' => 'No s\'ha trobat la pàgina',
    'chapter_not_found' => 'No s\'ha trobat el capítol',
    'selected_book_not_found' => 'No s\'ha trobat el llibre seleccionat',
    'selected_book_chapter_not_found' => 'No s\'ha trobat el llibre o el capítol seleccionat',
    'guests_cannot_save_drafts' => 'Els convidats no poden desar esborranys',

    // Users
    'users_cannot_delete_only_admin' => 'No podeu suprimir l\'únic administrador',
    'users_cannot_delete_guest' => 'No podeu suprimir l\'usuari convidat',

    // Roles
    'role_cannot_be_edited' => 'Aquest rol no es pot editar',
    'role_system_cannot_be_deleted' => 'Aquest rol és un rol del sistema i no es pot suprimir',
    'role_registration_default_cannot_delete' => 'No es pot suprimir aquest rol mentre estigui definit com a rol per defecte dels registres',
    'role_cannot_remove_only_admin' => 'Aquest usuari és l\'únic usuari assignat al rol d\'administrador. Assigneu el rol d\'administrador a un altre usuari abans de provar de suprimir aquest.',

    // Comments
    'comment_list' => 'S\'ha produït un error en obtenir els comentaris.',
    'cannot_add_comment_to_draft' => 'No podeu afegir comentaris a un esborrany.',
    'comment_add' => 'S\'ha produït un error en afegir o actualitzar el comentari.',
    'comment_delete' => 'S\'ha produït un error en suprimir el comentari.',
    'empty_comment' => 'No podeu afegir un comentari buit.',

    // Error pages
    '404_page_not_found' => 'No s\'ha trobat la pàgina',
    'sorry_page_not_found' => 'No hem pogut trobar la pàgina que cerqueu.',
    'sorry_page_not_found_permission_warning' => 'Si esperàveu que existís, és possible que no tingueu permisos per a veure-la.',
    'image_not_found' => 'Image Not Found',
    'image_not_found_subtitle' => 'Sorry, The image file you were looking for could not be found.',
    'image_not_found_details' => 'If you expected this image to exist it might have been deleted.',
    'return_home' => 'Torna a l\'inici',
    'error_occurred' => 'S\'ha produït un error',
    'app_down' => ':appName està fora de servei en aquests moments',
    'back_soon' => 'Tornarà a estar disponible aviat.',

    // API errors
    'api_no_authorization_found' => 'No s\'ha trobat cap testimoni d\'autorització a la petició',
    'api_bad_authorization_format' => 'S\'ha trobat un testimoni d\'autorització a la petició però el format sembla erroni',
    'api_user_token_not_found' => 'No s\'ha trobat cap testimoni d\'API per al testimoni d\'autorització proporcionat',
    'api_incorrect_token_secret' => 'El secret proporcionat per al testimoni d\'API proporcionat és incorrecte',
    'api_user_no_api_permission' => 'El propietari del testimoni d\'API utilitzat no té permís per a fer crides a l\'API',
    'api_user_token_expired' => 'El testimoni d\'autorització utilitzat ha caducat',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'S\'ha produït un error en enviar un correu electrònic de prova:',

    // HTTP errors
    'http_ssr_url_no_match' => 'The URL does not match the configured allowed SSR hosts',
];
