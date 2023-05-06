<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Deze inloggegevens zijn niet bij ons bekend.',
    'throttle' => 'Te veel login pogingen! Probeer het opnieuw na :seconds seconden.',

    // Login & Register
    'sign_up' => 'Registreren',
    'log_in' => 'Inloggen',
    'log_in_with' => 'Login met :socialDriver',
    'sign_up_with' => 'Registreer met :socialDriver',
    'logout' => 'Uitloggen',

    'name' => 'Naam',
    'username' => 'Gebruikersnaam',
    'email' => 'E-mail',
    'password' => 'Wachtwoord',
    'password_confirm' => 'Wachtwoord Bevestigen',
    'password_hint' => 'Moet uit minstens 8 tekens bestaan',
    'forgot_password' => 'Wachtwoord vergeten?',
    'remember_me' => 'Mij onthouden',
    'ldap_email_hint' => 'Geef een e-mailadres op voor dit account.',
    'create_account' => 'Account aanmaken',
    'already_have_account' => 'Heb je al een account?',
    'dont_have_account' => 'Nog geen account?',
    'social_login' => 'Aanmelden via een sociaal netwerk',
    'social_registration' => 'Registratie via een sociaal netwerk',
    'social_registration_text' => 'Registreer en log in met een andere service.',

    'register_thanks' => 'Bedankt voor het registreren!',
    'register_confirm' => 'Controleer je e-mail en klik op de bevestigingsknop om toegang te krijgen tot :appName.',
    'registrations_disabled' => 'Registratie is momenteel niet mogelijk',
    'registration_email_domain_invalid' => 'Dit e-maildomein wordt niet toegelaten tot deze applicatie',
    'register_success' => 'Bedankt voor het aanmelden! Je bent nu geregistreerd en aangemeld.',

    // Login auto-initiation
    'auto_init_starting' => 'Poging tot inloggen',
    'auto_init_starting_desc' => 'We maken contact met uw authenticatiesysteem om het inlogproces te starten. Als er na 5 seconden geen vooruitgang is, kunt u proberen op de onderstaande link te klikken.',
    'auto_init_start_link' => 'Ga verder met authenticatie',

    // Password Reset
    'reset_password' => 'Wachtwoord herstellen',
    'reset_password_send_instructions' => 'Geef je e-mailadres op en er wordt een link gestuurd om je wachtwoord te herstellen.',
    'reset_password_send_button' => 'Link sturen',
    'reset_password_sent' => 'Een link om het wachtwoord te resetten zal verstuurd worden naar :email als dat e-mailadres in het systeem gevonden is.',
    'reset_password_success' => 'Je wachtwoord is succesvol hersteld.',
    'email_reset_subject' => 'Herstel je wachtwoord van :appName',
    'email_reset_text' => 'Je ontvangt deze e-mail omdat we een wachtwoordresetverzoek voor uw account hebben ontvangen.',
    'email_reset_not_requested' => 'Als je geen wachtwoord herstel hebt aangevraagd, hoef je niets te doen.',

    // Email Confirmation
    'email_confirm_subject' => 'Bevestig je e-mailadres op :appName',
    'email_confirm_greeting' => 'Bedankt voor je aanmelding op :appName!',
    'email_confirm_text' => 'Bevestig je e-mailadres door op onderstaande knop te drukken:',
    'email_confirm_action' => 'Bevestig je e-mail',
    'email_confirm_send_error' => 'Een e-mailbevestiging is vereist, maar het systeem kon de e-mail niet verzenden. Neem contact op met de beheerder.',
    'email_confirm_success' => 'Uw e-mailadres is bevestigd! U zou nu moeten kunnen inloggen met dit e-mailadres.',
    'email_confirm_resent' => 'Bevestigingsmail opnieuw verzonden, controleer je inbox.',
    'email_confirm_thanks' => 'Bedankt voor de bevestiging!',
    'email_confirm_thanks_desc' => 'Wacht even terwijl uw bevestiging wordt behandeld. Als u na 3 seconden niet wordt doorverwezen, drukt u op de onderstaande link "Doorgaan" om verder te gaan.',

    'email_not_confirmed' => 'E-mailadres nog niet bevestigd',
    'email_not_confirmed_text' => 'Je e-mailadres is nog niet bevestigd.',
    'email_not_confirmed_click_link' => 'Klik op de link in de e-mail die vlak na je registratie is verstuurd.',
    'email_not_confirmed_resend' => 'Als je deze e-mail niet kunt vinden kun je deze met onderstaande formulier opnieuw verzenden.',
    'email_not_confirmed_resend_button' => 'Bevestigingsmail opnieuw verzenden',

    // User Invite
    'user_invite_email_subject' => 'Je bent uitgenodigd voor :appName!',
    'user_invite_email_greeting' => 'Er is een account voor je aangemaakt op :appName.',
    'user_invite_email_text' => 'Klik op de onderstaande knop om een account wachtwoord in te stellen en toegang te krijgen:',
    'user_invite_email_action' => 'Account wachtwoord instellen',
    'user_invite_page_welcome' => 'Welkom bij :appName!',
    'user_invite_page_text' => 'Om je account af te ronden en toegang te krijgen moet je een wachtwoord instellen dat gebruikt wordt om in te loggen op :appName bij toekomstige bezoeken.',
    'user_invite_page_confirm_button' => 'Bevestig wachtwoord',
    'user_invite_success_login' => 'Wachtwoord ingesteld, u zou nu moeten kunnen inloggen met uw ingestelde wachtwoord om toegang te krijgen tot :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Multi-factor authenticatie instellen',
    'mfa_setup_desc' => 'Stel multi-factor authenticatie in als een extra beveiligingslaag voor uw gebruikersaccount.',
    'mfa_setup_configured' => 'Reeds geconfigureerd',
    'mfa_setup_reconfigure' => 'Herconfigureren',
    'mfa_setup_remove_confirmation' => 'Weet je zeker dat je deze multi-factor authenticatie methode wilt verwijderen?',
    'mfa_setup_action' => 'Instellen',
    'mfa_backup_codes_usage_limit_warning' => 'U heeft minder dan 5 back-upcodes resterend. Genereer en sla een nieuwe set op voordat je geen codes meer hebt om te voorkomen dat je buiten je account wordt gesloten.',
    'mfa_option_totp_title' => 'Mobiele app',
    'mfa_option_totp_desc' => 'Om multi-factor authenticatie te gebruiken heeft u een mobiele applicatie nodig die TOTP ondersteunt, zoals Google Authenticator, Authy of Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Back-up Codes',
    'mfa_option_backup_codes_desc' => 'Bewaar veilig een set eenmalige back-upcodes die u kunt invoeren om uw identiteit te verifiëren.',
    'mfa_gen_confirm_and_enable' => 'Bevestigen en inschakelen',
    'mfa_gen_backup_codes_title' => 'Reservekopiecodes instellen',
    'mfa_gen_backup_codes_desc' => 'De onderstaande lijst met codes opslaan op een veilige plaats. Bij de toegang tot het systeem kun je een van de codes gebruiken als tweede verificatiemechanisme.',
    'mfa_gen_backup_codes_download' => 'Download Codes',
    'mfa_gen_backup_codes_usage_warning' => 'Elke code kan slechts eenmaal gebruikt worden',
    'mfa_gen_totp_title' => 'Mobiele app installatie',
    'mfa_gen_totp_desc' => 'Om multi-factor authenticatie te gebruiken heeft u een mobiele applicatie nodig die TOTP ondersteunt, zoals Google Authenticator, Authy of Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan de onderstaande QR-code door gebruik te maken van uw favoriete authenticatie app om aan de slag te gaan.',
    'mfa_gen_totp_verify_setup' => 'Installatie verifiëren',
    'mfa_gen_totp_verify_setup_desc' => 'Controleer of alles werkt door het invoeren van een code, die wordt gegenereerd binnen uw authenticatie-app, in het onderstaande invoerveld:',
    'mfa_gen_totp_provide_code_here' => 'Geef uw app gegenereerde code hier',
    'mfa_verify_access' => 'Verifieer toegang',
    'mfa_verify_access_desc' => 'Uw gebruikersaccount vereist dat u uw identiteit bevestigt via een extra verificatieniveau voordat u toegang krijgt. Verifieer met een van de door u geconfigureerde methoden om verder te gaan.',
    'mfa_verify_no_methods' => 'Geen methoden geconfigureerd',
    'mfa_verify_no_methods_desc' => 'Er konden geen meervoudige verificatie methoden voor uw account gevonden worden. Je zult minstens één methode moeten instellen voordat u toegang krijgt.',
    'mfa_verify_use_totp' => 'Verifieer met een mobiele app',
    'mfa_verify_use_backup_codes' => 'Verifieer met een back-up code',
    'mfa_verify_backup_code' => 'Back-up code',
    'mfa_verify_backup_code_desc' => 'Voer een van uw resterende back-up codes hieronder in:',
    'mfa_verify_backup_code_enter_here' => 'Voer hier de back-up code in',
    'mfa_verify_totp_desc' => 'Voer de code, gegenereerd met uw mobiele app, hieronder in:',
    'mfa_setup_login_notification' => 'Meervoudige verificatie methode geconfigureerd, Gelieve opnieuw in te loggen met de geconfigureerde methode.',
];
