<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Ces informations ne correspondent à aucun compte.',
    'throttle' => 'Trop d\'essais, veuillez réessayer dans :seconds secondes.',

    // Login & Register
    'sign_up' => 'S\'inscrire',
    'log_in' => 'Se connecter',
    'log_in_with' => 'Se connecter avec :socialDriver',
    'sign_up_with' => 'S\'inscrire avec :socialDriver',
    'logout' => 'Se déconnecter',

    'name' => 'Nom',
    'username' => 'Nom d\'utilisateur',
    'email' => 'E-mail',
    'password' => 'Mot de passe',
    'password_confirm' => 'Confirmez le mot de passe',
    'password_hint' => 'Doit faire plus de 7 caractères',
    'forgot_password' => 'Mot de passe oublié ?',
    'remember_me' => 'Se souvenir de moi',
    'ldap_email_hint' => 'Merci d\'entrer une adresse e-mail pour ce compte.',
    'create_account' => 'Créer un compte',
    'already_have_account' => 'Vous avez déjà un compte ?',
    'dont_have_account' => 'Vous n\'avez pas de compte ?',
    'social_login' => 'Connexion avec un réseau social',
    'social_registration' => 'Inscription avec un réseau social',
    'social_registration_text' => 'S\'inscrire et se connecter avec un réseau social.',

    'register_thanks' => 'Merci pour votre inscription !',
    'register_confirm' => 'Vérifiez vos e-mails et cliquez sur le lien de confirmation pour rejoindre :appName.',
    'registrations_disabled' => 'Les inscriptions sont désactivées pour le moment',
    'registration_email_domain_invalid' => 'Cette adresse e-mail ne peut pas accéder à l\'application',
    'register_success' => 'Merci pour votre inscription. Vous êtes maintenant inscrit(e) et connecté(e)',

    // Password Reset
    'reset_password' => 'Réinitialiser le mot de passe',
    'reset_password_send_instructions' => 'Entrez votre adresse e-mail ci-dessous et un e-mail avec un lien de réinitialisation de mot de passe vous sera envoyé.',
    'reset_password_send_button' => 'Envoyer un lien de réinitialisation',
    'reset_password_sent' => 'Un lien de réinitialisation du mot de passe sera envoyé à :email si cette adresse e-mail est trouvée dans le système.',
    'reset_password_success' => 'Votre mot de passe a été réinitialisé avec succès.',
    'email_reset_subject' => 'Réinitialisez votre mot de passe pour :appName',
    'email_reset_text' => 'Vous recevez cet e-mail parce que nous avons reçu une demande de réinitialisation pour votre compte.',
    'email_reset_not_requested' => 'Si vous n\'avez pas effectué cette demande, vous pouvez ignorer cet e-mail.',

    // Email Confirmation
    'email_confirm_subject' => 'Confirmez votre adresse e-mail pour :appName',
    'email_confirm_greeting' => 'Merci d\'avoir rejoint :appName !',
    'email_confirm_text' => 'Merci de confirmer en cliquant sur le lien ci-dessous :',
    'email_confirm_action' => 'Confirmez votre adresse e-mail',
    'email_confirm_send_error' => 'La confirmation par e-mail est requise mais le système n\'a pas pu envoyer l\'e-mail. Contactez l\'administrateur système.',
    'email_confirm_success' => 'Votre adresse e-mail a été confirmée ! Vous devriez maintenant pouvoir vous connecter en utilisant cette adresse e-mail.',
    'email_confirm_resent' => 'L\'e-mail de confirmation a été ré-envoyé. Vérifiez votre boîte de réception.',

    'email_not_confirmed' => 'Adresse e-mail non confirmée',
    'email_not_confirmed_text' => 'Votre adresse e-mail n\'a pas été confirmée.',
    'email_not_confirmed_click_link' => 'Merci de cliquer sur le lien dans l\'e-mail qui vous a été envoyé après l\'enregistrement.',
    'email_not_confirmed_resend' => 'Si vous ne retrouvez plus l\'e-mail, vous pouvez renvoyer un e-mail de confirmation en utilisant le formulaire ci-dessous.',
    'email_not_confirmed_resend_button' => 'Renvoyer l\'e-mail de confirmation',

    // User Invite
    'user_invite_email_subject' => 'Vous avez été invité(e) à rejoindre :appName !',
    'user_invite_email_greeting' => 'Un compte vous a été créé sur :appName.',
    'user_invite_email_text' => 'Cliquez sur le bouton ci-dessous pour renseigner le mot de passe et récupérer l\'accès :',
    'user_invite_email_action' => 'Renseignez le mot de passe de votre compte',
    'user_invite_page_welcome' => 'Bienvenue dans :appName !',
    'user_invite_page_text' => 'Pour finaliser votre compte et recevoir l\'accès, vous devez renseigner le mot de passe qui sera utilisé pour la connexion à :appName les prochaines fois.',
    'user_invite_page_confirm_button' => 'Confirmez le mot de passe',
    'user_invite_success_login' => 'Mot de passe défini, vous devriez maintenant pouvoir vous connecter en utilisant votre mot de passe défini pour accéder à :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Authentification multi-facteurs',
    'mfa_setup_desc' => 'Configurer l\'authentification multi-facteurs ajoute une couche supplémentaire de sécurité à votre compte utilisateur.',
    'mfa_setup_configured' => 'Déjà configuré',
    'mfa_setup_reconfigure' => 'Reconfigurer',
    'mfa_setup_remove_confirmation' => 'Êtes-vous sûr de vouloir supprimer cette méthode d\'authentification multi-facteurs ?',
    'mfa_setup_action' => 'Configuration',
    'mfa_backup_codes_usage_limit_warning' => 'Il vous reste moins de 5 codes de secours, veuillez générer et stocker un nouveau jeu de codes afin d\'éviter tout verrouillage de votre compte.',
    'mfa_option_totp_title' => 'Application mobile',
    'mfa_option_totp_desc' => 'Pour utiliser l\'authentification multi-facteurs, vous aurez besoin d\'une application mobile qui supporte TOTP comme Google Authenticator, Authy ou Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Codes de secours',
    'mfa_option_backup_codes_desc' => 'Stockez en toute sécurité un jeu de codes de secours que vous pourrez utiliser pour vérifier votre identité.',
    'mfa_gen_confirm_and_enable' => 'Confirmer et activer',
    'mfa_gen_backup_codes_title' => 'Configuration des codes de secours',
    'mfa_gen_backup_codes_desc' => 'Stockez la liste des codes ci-dessous dans un endroit sûr. Lorsque vous accédez au système, vous pourrez utiliser l\'un des codes comme un deuxième mécanisme d\'authentification.',
    'mfa_gen_backup_codes_download' => 'Télécharger les codes',
    'mfa_gen_backup_codes_usage_warning' => 'Chaque code ne peut être utilisé qu\'une seule fois',
    'mfa_gen_totp_title' => 'Configuration de l\'application mobile',
    'mfa_gen_totp_desc' => 'Pour utiliser l\'authentification multi-facteurs, vous aurez besoin d\'une application mobile qui supporte TOTP comme Google Authenticator, Authy ou Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scannez le QR code ci-dessous avec votre application d\'authentification préférée pour débuter.',
    'mfa_gen_totp_verify_setup' => 'Vérifier la configuration',
    'mfa_gen_totp_verify_setup_desc' => 'Vérifiez que tout fonctionne en utilisant un code généré par votre application d\'authentification, dans la zone ci-dessous :',
    'mfa_gen_totp_provide_code_here' => 'Fournissez le code généré par votre application ici',
    'mfa_verify_access' => 'Vérifier l\'accès',
    'mfa_verify_access_desc' => 'Votre compte d\'utilisateur vous demande de confirmer votre identité par un niveau supplémentaire de vérification avant que vous n\'ayez accès. Vérifiez-la en utilisant l\'une de vos méthodes configurées pour continuer.',
    'mfa_verify_no_methods' => 'Aucune méthode configurée',
    'mfa_verify_no_methods_desc' => 'Aucune méthode d\'authentification multi-facteurs n\'a pu être trouvée pour votre compte. Vous devez configurer au moins une méthode avant d\'obtenir l\'accès.',
    'mfa_verify_use_totp' => 'Vérifier à l\'aide d\'une application mobile',
    'mfa_verify_use_backup_codes' => 'Vérifier en utilisant un code de secours',
    'mfa_verify_backup_code' => 'Code de secours',
    'mfa_verify_backup_code_desc' => 'Entrez l\'un de vos codes de secours restants ci-dessous :',
    'mfa_verify_backup_code_enter_here' => 'Saisissez un code de secours ici',
    'mfa_verify_totp_desc' => 'Entrez ci-dessous le code généré à l\'aide de votre application mobile :',
    'mfa_setup_login_notification' => 'Méthode multi-facteurs configurée. Veuillez maintenant vous reconnecter en utilisant la méthode configurée.',
];
