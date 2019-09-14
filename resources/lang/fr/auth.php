<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'failed' => 'Ces informations ne correspondent à aucun compte.',
    'throttle' => "Trop d'essais, veuillez réessayer dans :seconds secondes.",

    /**
     * Login & Register
     */
    'sign_up' => "S'inscrire",
    'log_in' => 'Se connecter',
    'log_in_with' => 'Se connecter avec :socialDriver',
    'sign_up_with' => 'S\'inscrire avec :socialDriver',
    'logout' => 'Se déconnecter',

    'name' => 'Nom',
    'username' => "Nom d'utilisateur",
    'email' => 'E-mail',
    'password' => 'Mot de passe',
    'password_confirm' => 'Confirmez le mot de passe',
    'password_hint' => 'Doit faire plus de 7 caractères',
    'forgot_password' => 'Mot de passe oublié ?',
    'remember_me' => 'Se souvenir de moi',
    'ldap_email_hint' => "Merci d'entrer une adresse e-mail pour ce compte",
    'create_account' => 'Créer un compte',
    'already_have_account' => 'Vous avez déjà un compte?',
    'dont_have_account' => 'Vous n\'avez pas de compte?',
    'social_login' => 'Social Login',
    'social_registration' => 'Enregistrement Social',
    'social_registration_text' => "S'inscrire et se connecter avec un réseau social",

    'register_thanks' => 'Merci pour votre enregistrement',
    'register_confirm' => 'Vérifiez vos e-mails et cliquez sur le lien de confirmation pour rejoindre :appName.',
    'registrations_disabled' => "L'inscription est désactivée pour le moment",
    'registration_email_domain_invalid' => 'Cette adresse e-mail ne peut pas accéder à l\'application',
    'register_success' => 'Merci pour votre inscription. Vous êtes maintenant inscrit(e) et connecté(e)',


    /**
     * Password Reset
     */
    'reset_password' => 'Reset Password',
    'reset_password_send_instructions' => 'Entrez votre adresse e-mail ci-dessous et un e-mail avec un lien de réinitialisation de mot de passe vous sera envoyé',
    'reset_password_send_button' => 'Envoyer un lien de réinitialisation',
    'reset_password_sent_success' => 'Un lien de réinitialisation a été envoyé à :email.',
    'reset_password_success' => 'Votre mot de passe a été réinitialisé avec succès.',

    'email_reset_subject' => 'Réinitialisez votre mot de passe pour :appName',
    'email_reset_text' => 'Vous recevez cet e-mail parce que nous avons reçu une demande de réinitialisation pour votre compte',
    'email_reset_not_requested' => 'Si vous n\'avez pas effectué cette demande, vous pouvez ignorer cet e-mail.',


    /**
     * Email Confirmation
     */
    'email_confirm_subject' => 'Confirmez votre adresse e-mail pour :appName',
    'email_confirm_greeting' => 'Merci d\'avoir rejoint :appName !',
    'email_confirm_text' => 'Merci de confirmer en cliquant sur le lien ci-dessous :',
    'email_confirm_action' => 'Confirmez votre adresse e-mail',
    'email_confirm_send_error' => 'La confirmation par e-mail est requise mais le système n\'a pas pu envoyer l\'e-mail. Contactez l\'administrateur système.',
    'email_confirm_success' => 'Votre adresse e-mail a été confirmée !',
    'email_confirm_resent' => 'L\'e-mail de confirmation a été ré-envoyé. Vérifiez votre boîte de récéption.',

    'email_not_confirmed' => 'Adresse e-mail non confirmée',
    'email_not_confirmed_text' => 'Votre adresse e-mail n\'a pas été confirmée.',
    'email_not_confirmed_click_link' => 'Merci de cliquer sur le lien dans l\'e-mail qui vous a été envoyé après l\'enregistrement.',
    'email_not_confirmed_resend' => 'Si vous ne retrouvez plus l\'e-mail, vous pouvez renvoyer un e-mail de confirmation en utilisant le formulaire ci-dessous.',
    'email_not_confirmed_resend_button' => 'Renvoyez l\'e-mail de confirmation',
];
