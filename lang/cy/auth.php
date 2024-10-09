<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Nid yw\'r manylion hyn yn cyfateb i\'n cofnodion.',
    'throttle' => 'Gormod o ymdrechion mewngofnodi. Rhowch gynnig arall arni o gwmpas :seconds eiliadau.',

    // Login & Register
    'sign_up' => 'Cofrestru',
    'log_in' => 'Mewngofnodi',
    'log_in_with' => 'Mewngofnodi efo :socialDriver',
    'sign_up_with' => 'Cofrestru efo :socialDriver',
    'logout' => 'Allgofnodi',

    'name' => 'Enw',
    'username' => 'Enw defnyddiwr',
    'email' => 'Ebost',
    'password' => 'Cyfrinair',
    'password_confirm' => 'Cadarnhau cyfrinair',
    'password_hint' => 'Rhaid bod o leiaf 8 nod',
    'forgot_password' => 'Wedi anghofio cyfrinair?',
    'remember_me' => 'Cofiwch fi',
    'ldap_email_hint' => 'Rhowch e-bost i\'w ddefnyddio ar gyfer y cyfrif hwn.',
    'create_account' => 'Creu cyfrif',
    'already_have_account' => 'Oes gennych chi gyfrif yn barod?',
    'dont_have_account' => 'Dim cyfrif?',
    'social_login' => 'Mewngofnodi cymdeithasol',
    'social_registration' => 'Cofrestru cymdeithasol',
    'social_registration_text' => 'Cofrestru a mewngofnodi gan ddefnyddio dyfais arall.',

    'register_thanks' => 'Diolch am cofrestru!',
    'register_confirm' => 'Gwiriwch eich e-bost a chliciwch ar y botwm cadarnhau i gael mynediad i: appName.',
    'registrations_disabled' => 'Mae cofrestriadau wedi\'u hanalluogi ar hyn o bryd',
    'registration_email_domain_invalid' => 'Nid oes gan y parth e-bost hwnnw fynediad i\'r rhaglen hon',
    'register_success' => 'Diolch am arwyddo! Rydych bellach wedi cofrestru ac wedi mewngofnodi.',

    // Login auto-initiation
    'auto_init_starting' => 'Wrthi\'n ceisio mewngofnodi',
    'auto_init_starting_desc' => 'Rydym yn cysylltu â\'ch system ddilysu i ddechrau\'r broses fewngofnodi. Os nad oes cynnydd ar ôl 5 eiliad, gallwch geisio clicio ar y ddolen isod.',
    'auto_init_start_link' => 'Parhau gyda dilysu',

    // Password Reset
    'reset_password' => 'Ailosod cyfrinair',
    'reset_password_send_instructions' => 'Rhowch eich e-bost isod ac anfonir e-bost atoch gyda dolen ailosod cyfrinair.',
    'reset_password_send_button' => 'Anfon Dolen Ailosod',
    'reset_password_sent' => 'Bydd dolen ailosod cyfrinair yn cael ei hanfon at :email os ceir hyd i’r cyfeiriad e-bost hwn yn y system.',
    'reset_password_success' => 'Mae eich cyfrinair wedi\'i ailosod yn llwyddiannus.',
    'email_reset_subject' => 'Ailosod eich gair pass :appName',
    'email_reset_text' => 'Anfonwyd yr e-bost hwn atoch oherwydd ein bod wedi cael cais ailosod cyfrinair ar gyfer eich cyfrif.',
    'email_reset_not_requested' => 'Os nad oeddwch chi\'n ceisio ailosod eich gair pass, does dim byd arall i wneud.',

    // Email Confirmation
    'email_confirm_subject' => 'Cadarnhewch eich e-bost chi a :appName',
    'email_confirm_greeting' => 'Diolch am ymuno â :appName!',
    'email_confirm_text' => 'Os gwelwch yn dda cadarnhewch eich e-bost chi gan clicio ar y botwm isod:',
    'email_confirm_action' => 'Cadarnhau E-bost',
    'email_confirm_send_error' => 'Mae angen cadarnhad e-bost ond ni allai\'r system anfon yr e-bost. Cysylltwch â\'r gweinyddwr i sicrhau bod yr e-bost wedi\'i osod yn gywir.',
    'email_confirm_success' => 'Mae eich e-bost wedi\'i gadarnhau! Dylech nawr allu mewngofnodi gan ddefnyddio\'r cyfeiriad e-bost hwn.',
    'email_confirm_resent' => 'Ail-anfonwyd cadarnhad e-bost, gwiriwch eich mewnflwch.',
    'email_confirm_thanks' => 'Diolch am gadarnhau!',
    'email_confirm_thanks_desc' => 'Arhoswch eiliad wrth i’ch cadarnhad gael ei drin. Os na chewch eich ailgyfeirio ar ôl 3 eiliad, pwyswch y ddolen "Parhau" isod i symud ymlaen.',

    'email_not_confirmed' => 'Cyfeiriad E-bost heb ei Gadarnhau',
    'email_not_confirmed_text' => 'Dyw eich cyfeiriad e-bost chi ddim wedi cael ei gadarnhau eto.',
    'email_not_confirmed_click_link' => 'Cliciwch ar y ddolen yn yr e-bost a anfonwyd yn fuan ar ôl i chi gofrestru.',
    'email_not_confirmed_resend' => 'Os na allwch ddod o hyd i\'r e-bost, gallwch ail-anfon yr e-bost cadarnhad trwy gyflwyno\'r ffurflen isod.',
    'email_not_confirmed_resend_button' => 'Ail-anfon E-bost Cadarnhad',

    // User Invite
    'user_invite_email_subject' => 'Rydych chi wedi cael gwahoddiad i ymuno :appName!',
    'user_invite_email_greeting' => 'Mae cyfrif wedi cae ei greu i chi ar :appName.',
    'user_invite_email_text' => 'Cliciwch ar y botwm isod i osod cyfrinair cyfrif a chael mynediad:',
    'user_invite_email_action' => 'Gosod Cyfrinair Cyfrif',
    'user_invite_page_welcome' => 'Croeso i :appName!',
    'user_invite_page_text' => 'I gwblhau eich cyfrif a chael mynediad mae angen i chi osod cyfrinair a fydd yn cael ei ddefnyddio i fewngofnodi i :appName ar ymweliadau yn y dyfodol.',
    'user_invite_page_confirm_button' => 'Cadarnhau cyfrinair',
    'user_invite_success_login' => 'Cyfrinair wedi’i osod, dylech nawr allu mewngofnodi gan ddefnyddio\'r cyfrinair a osodwyd i gael mynediad i :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Gosod Dilysu Aml-Ffactor',
    'mfa_setup_desc' => 'Gosod dilysu aml-ffactor fel haen ychwanegol o ddiogelwch ar gyfer eich cyfrif defnyddiwr.',
    'mfa_setup_configured' => 'Wedi\'i ail-ffurfweddu\'n barod',
    'mfa_setup_reconfigure' => 'Ail-ffurfweddu',
    'mfa_setup_remove_confirmation' => 'Ydych chi\'n siŵr eich bod am gael gwared ar y dull dilysu aml-ffactor hwn?',
    'mfa_setup_action' => 'Gosodiad',
    'mfa_backup_codes_usage_limit_warning' => 'Mae gennych lai na 5 cod wrth gefn yn weddill, crëwch a storio cyfres newydd cyn i chi redeg allan o godau i osgoi cael eich cloi allan o\'ch cyfrif.',
    'mfa_option_totp_title' => 'Ap Ffôn Symudol',
    'mfa_option_totp_desc' => 'I ddefnyddio dilysu aml-ffactor bydd angen dyfais symudol arnoch sy\'n cefnogi TOTP megis Google Authenticator, Authy neu Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Codau wrth Gefn',
    'mfa_option_backup_codes_desc' => 'Mae’n cynhyrchu cyfres o godau wrth gefn un-amser y byddwch chi\'n eu defnyddio i fewngofnodi i wirio pwy ydych chi. Gwnewch yn siŵr eich bod yn storio\'r rhain mewn lle saff a diogel.',
    'mfa_gen_confirm_and_enable' => 'Cadarnhau a Galluogi',
    'mfa_gen_backup_codes_title' => 'Gosodiad Codau wrth Gefn',
    'mfa_gen_backup_codes_desc' => 'Storiwch y rhestr isod o godau mewn lle diogel. Wrth ddefnyddio’r system bydd modd i chi ddefnyddio un o\'r codau fel ail fecanwaith dilysu.',
    'mfa_gen_backup_codes_download' => 'Llwytho Codau i Lawr',
    'mfa_gen_backup_codes_usage_warning' => 'Gellir defnyddio pob cod unwaith yn unig',
    'mfa_gen_totp_title' => 'Gosod Ap Symudol',
    'mfa_gen_totp_desc' => 'I ddefnyddio dilysu aml-ffactor bydd angen dyfais symudol arnoch sy\'n cefnogi TOTP megis Google Authenticator, Authy neu Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Sganiwch y cod QR isod gan ddefnyddio\'ch ap dilysu dewisol i ddechrau.',
    'mfa_gen_totp_verify_setup' => 'Gwirio Gosodiad',
    'mfa_gen_totp_verify_setup_desc' => 'Gwiriwch fod popeth yn gweithio trwy roi cod, a gynhyrchwyd gan eich ap dilysu, yn y blwch mewnbwn isod:',
    'mfa_gen_totp_provide_code_here' => 'Rhowch y cod a gynhyrchwyd gan eich ap yma',
    'mfa_verify_access' => 'Gwirio Mynediad',
    'mfa_verify_access_desc' => 'Mae eich cyfrif defnyddiwr yn gofyn i chi gadarnhau pwy ydych chi trwy lefel ychwanegol o ddilysu cyn i chi gael mynediad. Gwiriwch gan ddefnyddio un o\'ch dulliau ffurfweddu i barhau.',
    'mfa_verify_no_methods' => 'Dim Dulliau wedi\'u Ffurfweddu',
    'mfa_verify_no_methods_desc' => 'Ni ellid dod o hyd i unrhyw ddulliau dilysu aml-ffactor ar gyfer eich cyfrif. Bydd angen i chi osod o leiaf un dull cyn i chi gael mynediad.',
    'mfa_verify_use_totp' => 'Gwirio gan ap ffôn',
    'mfa_verify_use_backup_codes' => 'Gwirio gan god wrth gefn',
    'mfa_verify_backup_code' => 'Cod wrth Gefn',
    'mfa_verify_backup_code_desc' => 'Rhowch un o\'ch codau wrth gefn sy\'n weddill isod:',
    'mfa_verify_backup_code_enter_here' => 'Cofnodi cod wrth gefn yma',
    'mfa_verify_totp_desc' => 'Rhowch y cod, a gynhyrchwyd gan ddefnyddio\'ch ap symudol, isod:',
    'mfa_setup_login_notification' => 'Dull aml-ffactor wedi\'i ffurfweddu, nawr mewngofnodwch eto gan ddefnyddio\'r dull wedi\'i ffurfweddu.',
];
