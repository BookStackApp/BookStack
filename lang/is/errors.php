<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Þú hefur ekki heimild til að skoða þessa síðu.',
    'permissionJson' => 'Þú hefur ekki heimild til að framkvæma þessa aðgerð.',

    // Auth
    'error_user_exists_different_creds' => 'Notandi með netfangið :email er nú þegar til.',
    'auth_pre_register_theme_prevention' => 'Ekki var hægt að búa til aðgang með þessum upplýsingum',
    'email_already_confirmed' => 'Netfang hefur þegar verið staðfest. Prófaðu að skrá þig inn.',
    'email_confirmation_invalid' => 'Þessi staðfestingar tóki er ekki gildur eða hefur þegar verið notaður. Reyndu að skrá þig aftur.',
    'email_confirmation_expired' => 'Staðfestingar tóki hefur runnið út. Nýr staðfestinga tölvupóstur hefur verið sendur.',
    'email_confirmation_awaiting' => 'Eftir á að staðfest þetta netfang',
    'ldap_fail_anonymous' => 'LDAP auðkenning virkaði ekki',
    'ldap_fail_authed' => 'LDAP auðkenning virkaði ekki með að nota uppgefið dn & password',
    'ldap_extension_not_installed' => 'LDAP PHP viðbót ekki uppsett',
    'ldap_cannot_connect' => 'Næ ekki að tengjast Ldap þjóni. Fyrsta tenging mistókst',
    'saml_already_logged_in' => 'Þegar innskráður',
    'saml_no_email_address' => 'Fann ekki netfang fyrir þennan notanda í auðkenningar þjónustu',
    'saml_invalid_response_id' => 'Beiðnin frá ytri auðkenningaraðila er óþekkt af kerfinu. Að fara tilbaka eftir innskráningu gæti valdið þessu vandamáli.',
    'saml_fail_authed' => 'Innskráning sem notaði :system tókst ekki. Kerfið gaf ekki út gilda auðkenningu',
    'oidc_already_logged_in' => 'Þegar skráður inn',
    'oidc_no_email_address' => 'Fann ekki netfang fyrir þennan notanda í ytri auðkenningar þjónustu',
    'oidc_fail_authed' => 'Innskráning sem notaði :system tókst ekki. Kerfið gaf ekki út gilda auðkenningu',
    'social_no_action_defined' => 'Engin aðgerð skilgreind',
    'social_login_bad_response' => "Villa kom upp við auðkenninga á :socialAccount login",
    'social_account_in_use' => 'Þessi :socialAccount er þegar í notkun. Reyndu að skrá þig inn með :socialAccount.',
    'social_account_email_in_use' => 'Netfangið :email er þegar í notkun. Ef þú ert nú þegar með aðgang getur þú tengt :socialAccount við hann í prófíl stillingum.',
    'social_account_existing' => 'Þessi :socialAccount er nú þegar tengdur við prófílinn þinn.',
    'social_account_already_used_existing' => 'Þessi :socialAccount reikningur er nú þegar í notkun hjá öðrum notanda.',
    'social_account_not_used' => 'Þessi :socialAccount er ekki tengdur neinum notanda. Þú getur tengt hann við þig í prófíl stillingar. ',
    'social_account_register_instructions' => 'Ef þú ert ekki nú þegar með aðgang, getur þú skrá þig með :socialAccount',
    'social_driver_not_found' => 'Samfélagsviðbót fannst ekki',
    'social_driver_not_configured' => 'Þínar :socialAccount er ekki rétt stilltar.',
    'invite_token_expired' => 'Þess boðshlekkur er útrunninn, Prófa að endurstilla lykilorðið þitt.',
    'login_user_not_found' => 'Enginn notandi fannst fyrir þessa aðgerð.',

    // System
    'path_not_writable' => 'Ekki var hægt að hlaða upp á slóðinni :filePath. Vertu viss um að slóðin sé skrifanleg.',
    'cannot_get_image_from_url' => 'Get ekki sótt mynd frá :url',
    'cannot_create_thumbs' => 'Netþjónninn getur ekki búið til smámyndir. Vertu viss um að þú hafir GD PHP viðbótina uppsetta.',
    'server_upload_limit' => 'Þessi netþjónn leyfir ekki uphal af þessari stærð. Prófaðu minni skrá.',
    'server_post_limit' => 'Netþjóninn getur ekki tekið á móti þessu magni gagna. Reyndu aftur með færri eða smærri gögnum.',
    'uploaded'  => 'Þessi netþjónn leyfir ekki uphal af þessari stærð. Prófaðu minni skrá.',

    // Drawing & Images
    'image_upload_error' => 'Villa kom upp við að hlaða upp mynd',
    'image_upload_type_error' => 'Gerð myndar er ógild',
    'image_upload_replace_type' => 'Myndin sem á að nota við útskipti þarf að vera sömu gerðar',
    'image_upload_memory_limit' => 'Ekki var hægt að taka við upphali og eða búa til smámyndir þar sem ekki eru auðlindir til staðar.',
    'image_thumbnail_memory_limit' => 'Ekki var hægt að búa til nokkrar stærðir myndarinnar vegna skorts á auðlindum.',
    'image_gallery_thumbnail_memory_limit' => 'Ekki var hægt að búa til smámyndayfirlit vegna skorts á auðlindum.',
    'drawing_data_not_found' => 'Ekki tóks að hlaða inn teikningagögnum. Það gæti vantað skránna eða að þú hafir ekki réttindi að henni.',

    // Attachments
    'attachment_not_found' => 'Viðhengi fannst ekki',
    'attachment_upload_error' => 'Það kom upp villa við að hlaða upp viðhenginu',

    // Pages
    'page_draft_autosave_fail' => 'Gat ekki vistað uppkast. Gættu að þú hafir tengingu við internetið áður en þú vistar þessa síðu',
    'page_draft_delete_fail' => 'Ekki var hægt að eyða uppkasti og sækja fyrra innihald síðunar',
    'page_custom_home_deletion' => 'Ekki er hægt að eyða síðu á meðan hún er valin sem sjálfgefin upphafssíða',

    // Entities
    'entity_not_found' => 'Entity fannst ekki',
    'bookshelf_not_found' => 'Hilla fannst ekki',
    'book_not_found' => 'Bók fannst ekki',
    'page_not_found' => 'Síða fannst ekki',
    'chapter_not_found' => 'Kafli fannst ekki',
    'selected_book_not_found' => 'Valin bók fannst ekki',
    'selected_book_chapter_not_found' => 'Valin bók eða kafli fannst ekki',
    'guests_cannot_save_drafts' => 'Gestir geta ekki vistað drög',

    // Users
    'users_cannot_delete_only_admin' => 'Þú getur ekki eytt, bara kerfisstjóri',
    'users_cannot_delete_guest' => 'Þú getur ekki eytt gesta notanda',
    'users_could_not_send_invite' => 'Gat ekki stofnað notanda þar sem ekki tókst að senda staðfestingar tölvupóst',

    // Roles
    'role_cannot_be_edited' => 'Ekki er hægt að breyta þessu hlutverki',
    'role_system_cannot_be_deleted' => 'Þetta er kerfis hlutverk og því ekki hægt að eyða því',
    'role_registration_default_cannot_delete' => 'Ekki er hægt að eyða þessu hlutverki þar sem það er sjálfgefið kerfishlutverk við skráningu',
    'role_cannot_remove_only_admin' => 'Þessi notandi er sá eini sem er með kerfisstjóra hlutverk. Bættu hlutverkinu við annann notanda áður en þú reynir að fjarlægja það héðan.',

    // Comments
    'comment_list' => 'Villa kom upp við að sækja athugasemdir.',
    'cannot_add_comment_to_draft' => 'Þú getur ekki sett athugasemdir við drög.',
    'comment_add' => 'Villa kom upp við að bæta við eða breyta athugasemdinni.',
    'comment_delete' => 'Villa kom upp við að eyða athugasemdinni.',
    'empty_comment' => 'Get ekki bætt við tómri athugasemd.',

    // Error pages
    '404_page_not_found' => 'Síða fannst ekki',
    'sorry_page_not_found' => 'Síðan sem þú varst að leita að fannst því miður ekki.',
    'sorry_page_not_found_permission_warning' => 'Ef þú átt von á að þessi síða sé til gæti verið að þú hafir ekki aðgang að henni.',
    'image_not_found' => 'Fann ekki mynd',
    'image_not_found_subtitle' => 'Myndin sem þú varst að leita að fannst því miður ekki.',
    'image_not_found_details' => 'Ef þú heldur að þessi mynda hafi verið til, þá gæti henni hafa verið eytt.',
    'return_home' => 'Fara á forsíðu',
    'error_occurred' => 'Það kom upp villa',
    'app_down' => ':appName er niðri í augnablikinu',
    'back_soon' => 'Verð komin upp aftur fljótlega.',

    // Import
    'import_zip_cant_read' => 'Gat ekki lesið ZIP skrá.',
    'import_zip_cant_decode_data' => 'Fann ekki ZIP data.json innihald.',
    'import_zip_no_data' => 'ZIP skráin inniheldur ekkert efni.',
    'import_validation_failed' => 'ZIP skráin stóðst ekki staðfestingu og skilaði villu:',
    'import_zip_failed_notification' => 'Gat ekki lesið inn ZIP skrá.',
    'import_perms_books' => 'Þú hefur ekki heimild til að búa til bækur.',
    'import_perms_chapters' => 'Þú hefur ekki heimild til að búa til kafla.',
    'import_perms_pages' => 'Þú hefur ekki heimild til að búa til síður.',
    'import_perms_images' => 'Þú hefur ekki heimild til að búa til myndir.',
    'import_perms_attachments' => 'Þú hefur ekki heimild til að búa til viðhengi.',

    // API errors
    'api_no_authorization_found' => 'Engin auðkenningar tóki fannst í aðgerðinni',
    'api_bad_authorization_format' => 'Auðkenningar tóki fannst með aðgerðinni en snið hans er rangt',
    'api_user_token_not_found' => 'Engin API tóki fannst á móti þessum auðkenningar tóka',
    'api_incorrect_token_secret' => 'Leyndarmálið sem gefið var upp fyrir API tókann er rangt',
    'api_user_no_api_permission' => 'Eigandi API tókans hefur ekki heimild til að gera API köll',
    'api_user_token_expired' => 'Auðkenningar tókin er útrunninn',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Villa kom upp viðað reyna senda prufu tölvupóst:',

    // HTTP errors
    'http_ssr_url_no_match' => 'Þetta vistfang stemmir ekki við leyfða SSR biðlara',
];
