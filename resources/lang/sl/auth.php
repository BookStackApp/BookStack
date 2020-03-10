<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Poverilnice se ne ujemajo s podatki v naši bazi.',
    'throttle' => 'Prekoračili ste število možnih prijav. Poskusite znova čez :seconds sekund.',

    // Login & Register
    'sign_up' => 'Registracija',
    'log_in' => 'Prijavi se',
    'log_in_with' => 'Prijavi se z :socialDriver',
    'sign_up_with' => 'Registriraj se z :socialDriver',
    'logout' => 'Odjavi se',

    'name' => 'Ime',
    'username' => 'Uporabniško ime',
    'email' => 'E-pošta',
    'password' => 'Geslo',
    'password_confirm' => 'Potrdi geslo',
    'password_hint' => 'Mora vebovati vsaj 8 znakov',
    'forgot_password' => 'Pozabljeno geslo?',
    'remember_me' => 'Zapomni si me',
    'ldap_email_hint' => 'Prosimo vpišite e-poštni naslov za ta račun.',
    'create_account' => 'Ustvari račun',
    'already_have_account' => 'Že imate račun?',
    'dont_have_account' => 'Nimate računa?',
    'social_login' => 'Prijava z računi družbenih omrežij',
    'social_registration' => 'Registracija z družbenim omrežjem',
    'social_registration_text' => 'Registrirajte in prijavite se za uporabo drugih možnosti.',

    'register_thanks' => 'Hvala za registracijo!',
    'register_confirm' => 'Prosimo preverite vaš e-poštni predal in kliknite na potrditveni gumb za dostop :appName.',
    'registrations_disabled' => 'Registracija trenutno ni mogoča',
    'registration_email_domain_invalid' => 'Ta e-poštna domena nima dostopa do te aplikacije',
    'register_success' => 'Hvala za registracijo! Sedaj ste registrirani in prijavljeni.',


    // Password Reset
    'reset_password' => 'Ponastavi geslo',
    'reset_password_send_instructions' => 'Spodaj vpišite vaš e-poštni naslov in prejeli boste e-pošto s povezavo za ponastavitev gesla.',
    'reset_password_send_button' => 'Pošlji povezavo za ponastavitev',
    'reset_password_sent_success' => 'Povezava za ponastavitev gesla je bila poslana na :email.',
    'reset_password_success' => 'Vaše geslo je bilo uspešno spremenjeno.',
    'email_reset_subject' => 'Ponastavi svoje :appName geslo',
    'email_reset_text' => 'To e-poštno sporočilo ste prejeli, ker smo prejeli zahtevo za ponastavitev gesla za vaš račun.',
    'email_reset_not_requested' => 'Če niste zahtevali ponastavitve gesla, vam ni potrebno ničesar storiti.',


    // Email Confirmation
    'email_confirm_subject' => 'Potrdi svojo e-pošto za :appName',
    'email_confirm_greeting' => 'Hvala ker ste se pridružili :appName!',
    'email_confirm_text' => 'Potrdite svoj e-naslov s klikom spodnjega gumba:',
    'email_confirm_action' => 'Potrdi e-pošto',
    'email_confirm_send_error' => 'E-poštna potrditev je zahtevana ampak sistem ni mogel poslati e-pošte. Kontaktirajte administratorja, da zagotovite, da je e-pošta pravilno nastavljena.',
    'email_confirm_success' => 'Vaš e-naslov je bil potrjen!',
    'email_confirm_resent' => 'Poslali smo vam potrditveno sporočilo. Prosimo preverite svojo elektronsko pošto.',

    'email_not_confirmed' => 'Elektronski naslov ni potrjen',
    'email_not_confirmed_text' => 'Vaš e-naslov še ni bil potrjen.',
    'email_not_confirmed_click_link' => 'Prosimo kliknite na link v e-poštnem sporočilu, ki ste ga prejeli kmalu po registraciji.',
    'email_not_confirmed_resend' => 'Če ne najdete e-pošte jo lahko ponovno pošljete s potrditvijo obrazca.',
    'email_not_confirmed_resend_button' => 'Ponovno pošlji potrditveno e-pošto',

    // User Invite
    'user_invite_email_subject' => 'Povabljen si bil da se pridružiš :appName!',
    'user_invite_email_greeting' => 'Račun je bil ustvarjen zate na :appName.',
    'user_invite_email_text' => 'Klikni na spodnji gumb, da si nastaviš geslo in dobiš dostop:',
    'user_invite_email_action' => 'Nastavi geslo za račun',
    'user_invite_page_welcome' => 'Dobrodošli na :appName!',
    'user_invite_page_text' => 'Za zaključiti in pridobiti dostop si morate nastaviti geslo, ki bo uporabljeno za prijavo v :appName.',
    'user_invite_page_confirm_button' => 'Potrdi geslo',
    'user_invite_success' => 'Geslo nastavljeno, sedaj imaš dostop do :appName!'
];