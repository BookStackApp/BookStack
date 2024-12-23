<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Þeesi auðkenning er ekki á skrá.',
    'throttle' => 'Of margar tilraunir til innskráningar. Reyndu aftur eftir :seconds sekúndur.',

    // Login & Register
    'sign_up' => 'Nýskrá',
    'log_in' => 'Innskrá',
    'log_in_with' => 'Innskrá með :socialDriver',
    'sign_up_with' => 'Búa til aðgang með :socialDriver',
    'logout' => 'Skrá út',

    'name' => 'Nafn',
    'username' => 'Notandanafn',
    'email' => 'Netfang',
    'password' => 'Lykilorð',
    'password_confirm' => 'Staðfestu lykilorð',
    'password_hint' => 'Verður að vera minnst 8 stafir',
    'forgot_password' => 'Gleymt lykilorð?',
    'remember_me' => 'Geyma innskráningarupplýsingar',
    'ldap_email_hint' => 'Settu inn netfang til að nota þennan aðgang.',
    'create_account' => 'Stofna aðgang',
    'already_have_account' => 'Þegar með notandaaðgang?',
    'dont_have_account' => 'Ekki með aðgang?',
    'social_login' => 'Innskráning með samfélagsmiðli',
    'social_registration' => 'Skráning samfélagsmiðils',
    'social_registration_text' => 'Skráðu þig og innskrá með annari þjónustu.',

    'register_thanks' => 'Takk fyrir að skrá þig!',
    'register_confirm' => 'Skoðaðu tölvupóstinn þinn og smelltu á staðfestingarhlekkinn :appName.',
    'registrations_disabled' => 'Skráningar eru óvirkar í augnablikinu',
    'registration_email_domain_invalid' => 'Þetta lén hefur ekki aðgang að þessu forriti',
    'register_success' => 'Takk fyrir að skrá þig, nú ertu innskráðursem notandi.',

    // Login auto-initiation
    'auto_init_starting' => 'Reyni innskráningu',
    'auto_init_starting_desc' => 'Reyni að tengjast auðkenningarþjónustu, ef ekkert gerist innan 5 sekúndna getur þú smellt á hlekkinn hér að neðan.',
    'auto_init_start_link' => 'Halda áfram með auðkenningu',

    // Password Reset
    'reset_password' => 'Endurstilla lykilorð',
    'reset_password_send_instructions' => 'Settu netfangið þitt hér að neðan og þú færð tölvupóst með endurstillingar hlekk.',
    'reset_password_send_button' => 'Senda hlekk',
    'reset_password_sent' => 'Endurstillingar hlekkur hefur verið sendur í tölvupósti :email ef netfangið er á skrá.',
    'reset_password_success' => 'Lykilorðið þitt hefur verið endurstillt.',
    'email_reset_subject' => 'Endurstilla :appName lykilorðið þitt',
    'email_reset_text' => 'Þú fékkst þennan tölvupóst því að beðið var um endurstillingu lykilorðs á þínum aðgangi.',
    'email_reset_not_requested' => 'Ef þú baðst ekki um endurstillingu lykilorðs þarftu ekki að gera neitt.',

    // Email Confirmation
    'email_confirm_subject' => 'Staðfestu netfangið þitt á :appName',
    'email_confirm_greeting' => 'Takk fyrir að skrá þig á :appName!',
    'email_confirm_text' => 'Vinsamlegast staðfestu netfangið þitt með því að smella á hnappin hér fyrir neðan:',
    'email_confirm_action' => 'Staðfesta netfang',
    'email_confirm_send_error' => 'Staðfesting netfangs er nauðsynleg en kerfið gat ekki sent póst, vinsamlegast hafið samband við kerfisstjóra.',
    'email_confirm_success' => 'Netfang þitt hefur verið staðfest, þú ættir nú að geta skráð þig inn með þessu netfangi.',
    'email_confirm_resent' => 'Staðfestingar tölvupóstur hefur verið sendur, kíktu í póshólfið þitt.',
    'email_confirm_thanks' => 'Takk fyrir að staðfesta!',
    'email_confirm_thanks_desc' => 'Hinkraðu smá á meðan staðfestingin þín er í vinnslu, ef ekkert gerist eftir 3 sekúndur, smelltu á "Halda áfram" hlekkinn hér fyrir neðan.',

    'email_not_confirmed' => 'Netfang hefur ekki verið staðfest',
    'email_not_confirmed_text' => 'Netfangið þitt hefur ekki enn verið staðfest.',
    'email_not_confirmed_click_link' => 'Vinsamlegast smelltu á hlekkinn sem barst þér í tölvupósti eftir skráningu.',
    'email_not_confirmed_resend' => 'Ef þú finnur ekki tölvupóstinn sem var sendur á þig, getur þú fengið hann endursendann með því að fylla út formið hér að neðan.',
    'email_not_confirmed_resend_button' => 'Endursenda staðfestingarpóst',

    // User Invite
    'user_invite_email_subject' => 'Þér hefur verið boðið að tengjast :appName!',
    'user_invite_email_greeting' => 'Það hefur verið stofnaður aðgangur fyrir ig á :appName.',
    'user_invite_email_text' => 'Smelltu á hnappinn fyrir neðan til að setja upp lykilorð og fá aðgang:',
    'user_invite_email_action' => 'Settu inn lykilorð',
    'user_invite_page_welcome' => 'Velkominn á :appName!',
    'user_invite_page_text' => 'Til að ljúka við uppsetningu og fá aðgang að :appName verður þú að velja þér lykilorð.',
    'user_invite_page_confirm_button' => 'Staðfestu lykilorð',
    'user_invite_success_login' => 'Lykilorð klárt, nú ættir þú að geta skráð þig inn á :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setja upp tvöfalda auðkenningu',
    'mfa_setup_desc' => 'Tvöföld euðkenning er viðbótar vörn til að tryggja aðganginn þinn.',
    'mfa_setup_configured' => 'Þegar uppsett',
    'mfa_setup_reconfigure' => 'Endurstilla',
    'mfa_setup_remove_confirmation' => 'Ertu viss um að þú viljið fjarlæga þessa auðkenningarleið?',
    'mfa_setup_action' => 'Uppsetning',
    'mfa_backup_codes_usage_limit_warning' => 'Þú átt færri en 5 tilraunir eftir. Búðu til og geymdu hjá þér fleiri tilraunir svo þú læsist ekki úti.',
    'mfa_option_totp_title' => 'App',
    'mfa_option_totp_desc' => 'Til að virkja tvöfalda auðkenningu verður þú að hafa app í símanum sem styður TOPT, til dæmis Google Authenticator, Authy eða Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Varakóðar',
    'mfa_option_backup_codes_desc' => 'Býr til sett af einskiptis kóðum sem þú getur notað til að auðkenna þig með. Geymdu þessa kóða á öruggum stað.',
    'mfa_gen_confirm_and_enable' => 'Staðfesta og virkja',
    'mfa_gen_backup_codes_title' => 'Stillingar varakóða',
    'mfa_gen_backup_codes_desc' => 'Geymdu listann af kóðum á öruggum stað. Þú getur notað þessa kóða sem auka auðkenningu.',
    'mfa_gen_backup_codes_download' => 'Hala niður kóðum',
    'mfa_gen_backup_codes_usage_warning' => 'Hver kóði getur bara verið notaður einu sinni',
    'mfa_gen_totp_title' => 'Uppsetning Apps',
    'mfa_gen_totp_desc' => 'Til að virkja tvöfalda auðkenningu verður þú að hafa app í símanum sem styður TOPT, til dæmis Google Authenticator, Authy eða Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Skannaðu QR kóðann með appinu sem þú notar fyrir tvöfalda auðkenningu.',
    'mfa_gen_totp_verify_setup' => 'Staðfesta uppsetningu',
    'mfa_gen_totp_verify_setup_desc' => 'Staðfestu að allt virki með því að setja inn kóða úr síma appinu þínu hér fyrir neðan:',
    'mfa_gen_totp_provide_code_here' => 'Sláðu inn kóða úr auðkennningar appi',
    'mfa_verify_access' => 'Staðfesta aðgang',
    'mfa_verify_access_desc' => 'Aðgangurinn þinn þarf viðbótar auðkenningu, veldu auðkenningarleið.',
    'mfa_verify_no_methods' => 'Engar aðferðir stilltar',
    'mfa_verify_no_methods_desc' => 'Engin aukaauðkenningar aðferð fannst. Þú verður að setja upp minnst eina viðbótarauðkenningu til að halda áfram.',
    'mfa_verify_use_totp' => 'Staðfestu með farsíma appi',
    'mfa_verify_use_backup_codes' => 'Staðfesta með varakóða',
    'mfa_verify_backup_code' => 'Varakóði',
    'mfa_verify_backup_code_desc' => 'Settu inn einn af varakóðunum þínum hér að neðan:',
    'mfa_verify_backup_code_enter_here' => 'Sláðu inn varakóða hér',
    'mfa_verify_totp_desc' => 'Sláðu inn kóðann úr auðkenningar appinu úr símanum þínum:',
    'mfa_setup_login_notification' => 'Tvöföld auðkenning stillt. Skráðu þig nú inn með euðkenningarleiðinni.',
];
