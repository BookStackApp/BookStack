<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Uppgifterna stämmer inte överrens med våra register.',
    'throttle' => 'För många inloggningsförsök. Prova igen om :seconds sekunder.',

    // Login & Register
    'sign_up' => 'Skapa konto',
    'log_in' => 'Logga in',
    'log_in_with' => 'Logga in med :socialDriver',
    'sign_up_with' => 'Registera dig med :socialDriver',
    'logout' => 'Logga ut',

    'name' => 'Namn',
    'username' => 'Användarnamn',
    'email' => 'E-post',
    'password' => 'Lösenord',
    'password_confirm' => 'Bekräfta lösenord',
    'password_hint' => 'Måste vara fler än 7 tecken',
    'forgot_password' => 'Glömt lösenord?',
    'remember_me' => 'Kom ihåg mig',
    'ldap_email_hint' => 'Vänligen ange en e-postadress att använda till kontot.',
    'create_account' => 'Skapa konto',
    'already_have_account' => 'Har du redan en användare?',
    'dont_have_account' => 'Har du ingen användare?',
    'social_login' => 'Logga in genom socialt medie',
    'social_registration' => 'Registrera dig genom socialt media',
    'social_registration_text' => 'Registrera dig och logga in genom en annan tjänst.',

    'register_thanks' => 'Tack för din registrering!',
    'register_confirm' => 'Vänligen kontrollera din mail och klicka på bekräftelselänken för att få tillgång till :appName.',
    'registrations_disabled' => 'Registrering är för närvarande avstängd',
    'registration_email_domain_invalid' => 'Den e-postadressen har inte tillgång till den här applikationen',
    'register_success' => 'Tack för din registrering! Du är nu registerad och inloggad.',


    // Password Reset
    'reset_password' => 'Återställ lösenord',
    'reset_password_send_instructions' => 'Ange din e-postadress nedan så skickar vi ett mail med en länk för att återställa ditt lösenord.',
    'reset_password_send_button' => 'Skicka återställningslänk',
    'reset_password_sent' => 'En länk för återställning av lösenord kommer att skickas till :email om den e-postadressen finns i systemet.',
    'reset_password_success' => 'Ditt lösenord har återställts.',
    'email_reset_subject' => 'Återställ ditt lösenord till :appName',
    'email_reset_text' => 'Du får detta mail eftersom vi fått en begäran om att återställa lösenordet till ditt konto.',
    'email_reset_not_requested' => 'Om du inte begärt att få ditt lösenord återställt behöver du inte göra någonting',


    // Email Confirmation
    'email_confirm_subject' => 'Bekräfta din e-post på :appName',
    'email_confirm_greeting' => 'Tack för att du gått med i :appName!',
    'email_confirm_text' => 'Vänligen bekräfta din e-postadress genom att klicka på knappen nedan:',
    'email_confirm_action' => 'Bekräfta e-post',
    'email_confirm_send_error' => 'E-posten behöver bekräftas men systemet kan inte skicka mail. Kontakta adminstratören för att kontrollera att allt är konfigurerat korrekt.',
    'email_confirm_success' => 'Din e-post har bekräftats',
    'email_confirm_resent' => 'Bekräftelsemailet har skickats på nytt, kolla din mail',

    'email_not_confirmed' => 'E-posadress ej bekräftad',
    'email_not_confirmed_text' => 'Din e-postadress har inte bekräftats ännu.',
    'email_not_confirmed_click_link' => 'Vänligen klicka på länken i det mail du fick strax efter att du registerade dig.',
    'email_not_confirmed_resend' => 'Om du inte hittar mailet kan du begära en ny bekräftelse genom att fylla i formuläret nedan.',
    'email_not_confirmed_resend_button' => 'Skicka bekräftelse på nytt',

    // User Invite
    'user_invite_email_subject' => 'Du har blivit inbjuden att gå med i :appName!',
    'user_invite_email_greeting' => 'Ett konto har skapats för dig i :appName.',
    'user_invite_email_text' => 'Klicka på knappen nedan för att ange ett lösenord och få tillgång:',
    'user_invite_email_action' => 'Ange kontolösenord',
    'user_invite_page_welcome' => 'Välkommen till :appName!',
    'user_invite_page_text' => 'För att slutföra ditt konto och få åtkomst måste du ange ett lösenord som kommer att användas för att logga in på :appName vid framtida besök.',
    'user_invite_page_confirm_button' => 'Bekräfta lösenord',
    'user_invite_success' => 'Lösenord satt, du har nu tillgång till :appName!'
];