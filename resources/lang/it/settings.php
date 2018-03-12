<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Impostazioni',
    'settings_save' => 'Salva Impostazioni',
    'settings_save_success' => 'Impostazioni salvate',

    /**
     * App settings
     */

    'app_settings' => 'Impostazioni App',
    'app_name' => 'Nome applicazione',
    'app_name_desc' => 'Questo nome è mostrato nell\'header e in tutte le mail.',
    'app_name_header' => 'Mostrare il nome nell\'header',
    'app_public_viewing' => 'Consentire la visione pubblica?',
    'app_secure_images' => 'Abilitare una sicurezza maggiore per le immagini caricate?',
    'app_secure_images_desc' => 'Per una ragione di prestazioni, tutte le immagini sono pubbliche. Questa opzione aaggiunge una stringa, difficile da indovinare, random negli url delle immagini. Assicurati che il listing delle cartelle non sia abilitato per prevenire un accesso semplice.',
    'app_editor' => 'Editor pagine',
    'app_editor_desc' => 'Seleziona quale editor verrà usato da tutti gli utenti per modificare le pagine.',
    'app_custom_html' => 'Contenuto Head HTML Custom',
    'app_custom_html_desc' => 'Qualsiasi contenuto aggiunto qui verrà inserito alla fine della sezione <head> di tutte le pagine. Questo è utile per sovrascrivere lo stile o aggiungere il codice per gli analytics.',
    'app_logo' => 'Logo applicazione',
    'app_logo_desc' => 'Questa immagine dovrebbe essere 43px in altezza. <br>Immagini più grandi verranno scalate.',
    'app_primary_color' => 'Colore primario applicazione',
    'app_primary_color_desc' => 'Deve essere un valore hex. <br>Lascia vuoto per reimpostare il colore di default.',
    'app_homepage' => 'Homepage Applicazione',
    'app_homepage_desc' => 'Seleziona una pagina da mostrare nella home anzichè quella di default. I permessi della pagina sono ignorati per quella selezionata.',
    'app_homepage_default' => 'Homepage di default scelta',
    'app_disable_comments' => 'Disattiva commenti',
    'app_disable_comments_desc' => 'Disabilita i commenti su tutte le pagine nell\'applicazione. I commenti esistenti non sono mostrati. ',

    /**
     * Registration settings
     */

    'reg_settings' => 'Impostazioni Registrazione',
    'reg_allow' => 'Consentire Registrazione?',
    'reg_default_role' => 'Ruolo predefinito dopo la registrazione',
    'reg_confirm_email' => 'Richiedere la conferma della mail?',
    'reg_confirm_email_desc' => 'Se la restrizione per dominio è usata la conferma della mail sarà richiesta e la scelta ignorata.',
    'reg_confirm_restrict_domain' => 'Restringi la registrazione al dominio',
    'reg_confirm_restrict_domain_desc' => "Inserisci una lista separata da virgola di domini di email a cui vorresti restringere la registrazione. Agli utenti verrà inviata una mail per confermare il loro indirizzo prima che possano interagire con l'applicazione. <br> Nota che gli utenti saranno in grado di cambiare il loro indirizzo dopo aver completato la registrazione.",
    'reg_confirm_restrict_domain_placeholder' => 'Nessuna restrizione impostata',

    /**
     * Role settings
     */

    'roles' => 'Ruoli',
    'role_user_roles' => 'Ruoli Utente',
    'role_create' => 'Crea Nuovo Ruolo',
    'role_create_success' => 'Ruolo creato correttamente',
    'role_delete' => 'Elimina Ruolo',
    'role_delete_confirm' => 'Questo eliminerà il ruolo con il nome \':roleName\'.',
    'role_delete_users_assigned' => 'Questo ruolo ha :userCount utenti assegnati. Se vuoi migrare gli utenti da questo ruolo selezionane uno nuovo sotto.',
    'role_delete_no_migration' => "Non migrare gli utenti",
    'role_delete_sure' => 'Sei sicuro di voler eliminare questo ruolo?',
    'role_delete_success' => 'Ruolo eliminato correttamente',
    'role_edit' => 'Modifica Ruolo',
    'role_details' => 'Dettagli Ruolo',
    'role_name' => 'Nome Ruolo',
    'role_desc' => 'Breve Descrizione del Ruolo',
    'role_system' => 'Permessi di Sistema',
    'role_manage_users' => 'Gestire gli utenti',
    'role_manage_roles' => 'Gestire ruoli e permessi di essi',
    'role_manage_entity_permissions' => 'Gestire tutti i permessi di libri, capitoli e pagine',
    'role_manage_own_entity_permissions' => 'Gestire i permessi sui propri libri, capitoli e pagine',
    'role_manage_settings' => 'Gestire impostazioni app',
    'role_asset' => 'Permessi Entità',
    'role_asset_desc' => "Questi permessi controllano l'accesso di default alle entità. I permessi nei Libri, Capitoli e Pagine sovrascriveranno questi.",
    'role_all' => 'Tutti',
    'role_own' => 'Propri',
    'role_controlled_by_asset' => "Controllato dall'entità in cui sono caricati",
    'role_save' => 'Salva Ruolo',
    'role_update_success' => 'Ruolo aggiornato correttamente',
    'role_users' => 'Utenti in questo ruolo',
    'role_users_none' => 'Nessun utente assegnato a questo ruolo',

    /**
     * Users
     */

    'users' => 'Utenti',
    'user_profile' => 'Profilo Utente',
    'users_add_new' => 'Aggiungi Nuovo Utente',
    'users_search' => 'Cerca Utenti',
    'users_role' => 'Ruoli Utente',
    'users_external_auth_id' => 'ID Autenticazioni Esterna',
    'users_password_warning' => 'Riempi solo se desideri cambiare la tua password:',
    'users_system_public' => 'Questo utente rappresente qualsiasi ospite che visita il sito. Non può essere usato per effettuare il login ma è assegnato automaticamente.',
    'users_delete' => 'Elimina Utente',
    'users_delete_named' => "Elimina l'utente :userName",
    'users_delete_warning' => 'Questo eliminerà completamente l\'utente \':userName\' dal sistema.',
    'users_delete_confirm' => 'Sei sicuro di voler eliminare questo utente?',
    'users_delete_success' => 'Utenti rimossi correttamente',
    'users_edit' => 'Modifica Utente',
    'users_edit_profile' => 'Modifica Profilo',
    'users_edit_success' => 'Utente aggiornato correttamente',
    'users_avatar' => 'Avatar Utente',
    'users_avatar_desc' => "Quest'immagine dovrebbe essere approssimativamente 256px quadrata.",
    'users_preferred_language' => 'Lingua Preferita',
    'users_social_accounts' => 'Account Social',
    'users_social_accounts_info' => 'Qui puoi connettere gli altri account per un accesso più veloce e semplice. Disconnettere un account qui non rimuoverà le altre sessioni. Revoca l\'accesso dal tuo profilo negli account social connessi.',
    'users_social_connect' => 'Connetti Account',
    'users_social_disconnect' => 'Disconnetti Account',
    'users_social_connected' => 'L\'account :socialAccount è stato connesso correttamente al tuo profilo.',
    'users_social_disconnected' => 'L\'account :socialAccount è stato disconnesso correttamente dal tuo profilo.',
];
