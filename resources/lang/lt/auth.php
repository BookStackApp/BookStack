<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Šie įgaliojimai neatitinka mūsų įrašų.',
    'throttle' => 'Per daug prisijungimo bandymų. Prašome pabandyti dar kartą po :seconds sekundžių.',

    // Login & Register
    'sign_up' => 'Užsiregistruoti',
    'log_in' => 'Prisijungti',
    'log_in_with' => 'Prisijungti su :socialDriver',
    'sign_up_with' => 'Užsiregistruoti su :socialDriver',
    'logout' => 'Atsijungti',

    'name' => 'Pavadinimas',
    'username' => 'Vartotojo vardas',
    'email' => 'Elektroninis paštas',
    'password' => 'Slaptažodis',
    'password_confirm' => 'Patvirtinti slaptažodį',
    'password_hint' => 'Privalo būti daugiau nei 7 simboliai',
    'forgot_password' => 'Pamiršote slaptažodį?',
    'remember_me' => 'Prisimink mane',
    'ldap_email_hint' => 'Prašome įvesti elektroninį paštą, kad galėtume naudotis šia paskyra.',
    'create_account' => 'Sukurti paskyrą',
    'already_have_account' => 'Jau turite paskyrą?',
    'dont_have_account' => 'Neturite paskyros?',
    'social_login' => 'Socialinis prisijungimas',
    'social_registration' => 'Socialinė registracija',
    'social_registration_text' => 'Užsiregistruoti ir prisijungti naudojantis kita paslauga.',

    'register_thanks' => 'Ačiū, kad užsiregistravote!',
    'register_confirm' => 'Prašome patikrinti savo elektroninį paštą ir paspausti patvirtinimo mygtuką, kad gautumėte leidimą į :appName.',
    'registrations_disabled' => 'Registracijos šiuo metu negalimos',
    'registration_email_domain_invalid' => 'Elektroninio pašto domenas neturi prieigos prie šios programos',
    'register_success' => 'Ačiū už prisijungimą! Dabar jūs užsiregistravote ir prisijungėte.',


    // Password Reset
    'reset_password' => 'Pakeisti slaptažodį',
    'reset_password_send_instructions' => 'Įveskite savo elektroninį paštą žemiau ir jums bus išsiųstas elektroninis laiškas su slaptažodžio nustatymo nuoroda.',
    'reset_password_send_button' => 'Atsiųsti atsatymo nuorodą',
    'reset_password_sent' => 'Slaptažodžio nustatymo nuoroda bus išsiųsta :email jeigu elektroninio pašto adresas bus rastas sistemoje.',
    'reset_password_success' => 'Jūsų slaptažodis buvo sėkmingai atnaujintas.',
    'email_reset_subject' => 'Atnaujinti jūsų :appName slaptažodį',
    'email_reset_text' => 'Šį laišką gaunate, nes mes gavome slaptažodžio atnaujinimo užklausą iš jūsų paskyros.',
    'email_reset_not_requested' => 'Jeigu jums nereikia slaptažodžio atnaujinimo, tolimesnių veiksmų atlikti nereikia.',


    // Email Confirmation
    'email_confirm_subject' => 'Patvirtinkite savo elektroninį paštą :appName',
    'email_confirm_greeting' => 'Ačiū už prisijungimą prie :appName!',
    'email_confirm_text' => 'Prašome patvirtinti savo elektroninio pašto adresą paspaudus mygtuką žemiau:',
    'email_confirm_action' => 'Patvirtinkite elektroninį paštą',
    'email_confirm_send_error' => 'Būtinas elektroninio laiško patviritnimas, bet sistema negali išsiųsti laiško. Susisiekite su administratoriumi, kad užtikrintumėte, jog elektroninis paštas atsinaujino teisingai.',
    'email_confirm_success' => 'Jūsų elektroninis paštas buvo patvirtintas!',
    'email_confirm_resent' => 'Elektroninio pašto patvirtinimas persiųstas, prašome patikrinti pašto dėžutę.',

    'email_not_confirmed' => 'Elektroninis paštas nepatvirtintas',
    'email_not_confirmed_text' => 'Jūsų elektroninis paštas dar vis nepatvirtintas.',
    'email_not_confirmed_click_link' => 'Prašome paspausti nuorodą elektroniniame pašte, kuri buvo išsiųsta iš karto po registracijos.',
    'email_not_confirmed_resend' => 'Jeigu nerandate elektroninio laiško, galite dar kartą išsiųsti patvirtinimo elektroninį laišką, pateikdami žemiau esančią formą.',
    'email_not_confirmed_resend_button' => 'Persiųsti patvirtinimo laišką',

    // User Invite
    'user_invite_email_subject' => 'Jūs buvote pakviestas prisijungti prie :appName!',
    'user_invite_email_greeting' => 'Paskyra buvo sukurta jums :appName.',
    'user_invite_email_text' => 'Paspauskite mygtuką žemiau, kad sukurtumėte paskyros slaptažodį ir gautumėte prieigą:',
    'user_invite_email_action' => 'Sukurti paskyros slaptažodį',
    'user_invite_page_welcome' => 'Sveiki atvykę į :appName!',
    'user_invite_page_text' => 'Norėdami galutinai pabaigti paskyrą ir gauti prieigą jums reikia nustatyti slaptažodį, kuris bus naudojamas prisijungiant prie :appName ateities vizitų metu.',
    'user_invite_page_confirm_button' => 'Patvirtinti slaptažodį',
    'user_invite_success' => 'Slaptažodis nustatytas, dabar turite prieigą prie :appName!'
];