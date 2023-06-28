<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nid oes gennych ganiatâd i gael mynediad i\'r dudalen y gofynnwyd amdani.',
    'permissionJson' => 'Nid oes gennych ganiatâd i gyflawni\'r weithred y gofynnwyd amdani.',

    // Auth
    'error_user_exists_different_creds' => 'Mae defnyddiwr gyda\'r e-bost :email eisoes yn bodoli ond gyda nodweddion gwahanol.',
    'email_already_confirmed' => 'E-bost eisoes wedi\'i gadarnhau, Ceisiwch fewngofnodi.',
    'email_confirmation_invalid' => 'Nid yw\'r tocyn cadarnhau hwn yn ddilys neu mae eisoes wedi\'i ddefnyddio. Ceisiwch gofrestru eto.',
    'email_confirmation_expired' => 'Mae\'r tocyn cadarnhad wedi dod i ben, Mae e-bost cadarnhau newydd wedi\'i anfon.',
    'email_confirmation_awaiting' => 'Mae angen cadarnhau cyfeiriad e-bost y cyfrif a ddefnyddir',
    'ldap_fail_anonymous' => 'Methodd mynediad LDAP gan ddefnyddio rhwymiad dienw',
    'ldap_fail_authed' => 'Methodd mynediad LDAP gan ddefnyddio\'r manylion dn a chyfrinair a roddwyd',
    'ldap_extension_not_installed' => 'Estyniad PHP LDAP heb ei osod',
    'ldap_cannot_connect' => 'Methu cysylltu i weinydd ldap, cysylltiad cychwynnol wedi methu',
    'saml_already_logged_in' => 'Wedi mewngofnodi yn barod',
    'saml_user_not_registered' => 'Nid yw\'r defnyddiwr :name wedi\'i gofrestru ac mae cofrestriad awtomatig wedi\'i analluogi',
    'saml_no_email_address' => 'Methu dod o hyd i gyfeiriad e-bost, ar gyfer y defnyddiwr hwn, yn y data a ddarparwyd gan y system ddilysu allanol',
    'saml_invalid_response_id' => 'Nid yw\'r cais o\'r system ddilysu allanol yn cael ei gydnabod gan broses a ddechreuwyd gan y cais hwn. Gallai llywio yn ôl ar ôl mewngofnodi achosi\'r broblem hon.',
    'saml_fail_authed' => 'Wedi methu mewngofnodi gan ddefnyddio :system, ni roddodd y system awdurdodiad llwyddiannus',
    'oidc_already_logged_in' => 'Wedi mewngofnodi yn barod',
    'oidc_user_not_registered' => 'Nid yw\'r defnyddiwr :name wedi\'i gofrestru ac mae cofrestriad awtomatig wedi\'i analluogi',
    'oidc_no_email_address' => 'Methu dod o hyd i gyfeiriad e-bost, ar gyfer y defnyddiwr hwn, yn y data a ddarparwyd gan y system ddilysu allanol',
    'oidc_fail_authed' => 'Wedi methu mewngofnodi gan ddefnyddio :system, ni roddodd y system awdurdodiad llwyddiannus',
    'social_no_action_defined' => 'Dim gweithred wedi\'i diffinio',
    'social_login_bad_response' => "Gwall a dderbyniwyd yn ystod mewngofnodi :socialAccount:\n:error",
    'social_account_in_use' => 'Mae\'r cyfrif :socialAccount hwn eisoes yn cael ei ddefnyddio, Ceisiwch fewngofnodi trwy\'r opsiwn :socialAccount.',
    'social_account_email_in_use' => 'Mae\'r e-bost :email eisoes yn cael ei ddefnyddio. Os oes gennych gyfrif yn barod gallwch gysylltu eich cyfrif :socialAccount o osodiadau eich proffil.',
    'social_account_existing' => 'Mae\'r :socialAccount hwn eisoes ynghlwm wrth eich proffil.',
    'social_account_already_used_existing' => 'Mae\'r cyfrif :socialAccount hwn eisoes yn cael ei ddefnyddio gan ddefnyddiwr arall.',
    'social_account_not_used' => 'Nid yw\'r cyfrif :socialAccount hwn yn gysylltiedig ag unrhyw ddefnyddwyr. Atodwch ef yn eich gosodiadau proffil. ',
    'social_account_register_instructions' => 'Os nad oes gennych gyfrif eto, gallwch gofrestru cyfrif gan ddefnyddio\'r opsiwn :socialAccount.',
    'social_driver_not_found' => 'Gyrrwr cymdeithasol heb ei ganfod',
    'social_driver_not_configured' => 'Nid yw eich gosodiadau cymdeithasol :socialAccount wedi\'u ffurfweddu\'n gywir.',
    'invite_token_expired' => 'Mae\'r ddolen wahoddiad hon wedi dod i ben. Yn lle hynny, gallwch chi geisio ailosod cyfrinair eich cyfrif.',

    // System
    'path_not_writable' => 'Nid oedd modd uwchlwytho llwybr ffeil :filePath. Sicrhewch ei fod yn ysgrifenadwy i\'r gweinydd.',
    'cannot_get_image_from_url' => 'Methu cael delwedd o :url',
    'cannot_create_thumbs' => 'Ni all y gweinydd greu mân-luniau. Gwiriwch fod gennych yr estyniad GD PHP wedi\'i osod.',
    'server_upload_limit' => 'Nid yw\'r gweinydd yn caniatáu uwchlwythiadau o\'r maint hwn. Rhowch gynnig ar faint ffeil llai.',
    'uploaded'  => 'Nid yw\'r gweinydd yn caniatáu uwchlwythiadau o\'r maint hwn. Rhowch gynnig ar faint ffeil llai.',

    // Drawing & Images
    'image_upload_error' => 'Bu gwall wrth uwchlwytho\'r ddelwedd',
    'image_upload_type_error' => 'Mae\'r math o ddelwedd sy\'n cael ei huwchlwytho yn annilys',
    'image_upload_replace_type' => 'Image file replacements must be of the same type',
    'drawing_data_not_found' => 'Drawing data could not be loaded. The drawing file might no longer exist or you may not have permission to access it.',

    // Attachments
    'attachment_not_found' => 'Ni chanfuwyd yr atodiad',
    'attachment_upload_error' => 'An error occurred uploading the attachment file',

    // Pages
    'page_draft_autosave_fail' => 'Wedi methu cadw\'r drafft. Sicrhewch fod gennych gysylltiad rhyngrwyd cyn cadw\'r dudalen hon',
    'page_draft_delete_fail' => 'Failed to delete page draft and fetch current page saved content',
    'page_custom_home_deletion' => 'Methu dileu tudalen tra ei bod wedi\'i gosod fel hafan',

    // Entities
    'entity_not_found' => 'Endid heb ei ganfod',
    'bookshelf_not_found' => 'Shelf not found',
    'book_not_found' => 'Ni chanfuwyd y llyfr',
    'page_not_found' => 'Heb ganfod y dudalen',
    'chapter_not_found' => 'Pennod heb ei chanfod',
    'selected_book_not_found' => 'Ni ddaethpwyd o hyd i\'r llyfr a ddewiswyd',
    'selected_book_chapter_not_found' => 'Ni ddaethpwyd o hyd i\'r Llyfr neu\'r Bennod a ddewiswyd',
    'guests_cannot_save_drafts' => 'Ni all gwesteion arbed drafftiau',

    // Users
    'users_cannot_delete_only_admin' => 'Ni allwch ddileu\'r unig weinyddwr',
    'users_cannot_delete_guest' => 'Ni allwch ddileu\'r defnyddiwr gwadd',

    // Roles
    'role_cannot_be_edited' => 'Nid oes modd golygu\'r rôl hon',
    'role_system_cannot_be_deleted' => 'Rôl system yw\'r rôl hon ac ni ellir ei dileu',
    'role_registration_default_cannot_delete' => 'Ni ellir dileu\'r rôl hon tra ei bod wedi\'i gosod fel y rôl gofrestru ddiofyn',
    'role_cannot_remove_only_admin' => 'Y defnyddiwr hwn yw\'r unig ddefnyddiwr sydd wedi\'i neilltuo i rôl y gweinyddwr. Neilltuo rôl y gweinyddwr i ddefnyddiwr arall cyn ceisio ei dynnu yma.',

    // Comments
    'comment_list' => 'Digwyddodd gwall wrth nôl y sylwadau.',
    'cannot_add_comment_to_draft' => 'Ni allwch ychwanegu sylwadau at ddrafft.',
    'comment_add' => 'Digwyddodd gwall wrth ychwanegu / diweddaru\'r sylw.',
    'comment_delete' => 'Digwyddodd gwall wrth dileu\'r sylwad.',
    'empty_comment' => 'Methu ychwanegu sylw gwag.',

    // Error pages
    '404_page_not_found' => 'Heb ganfod y dudalen',
    'sorry_page_not_found' => 'Mae\'n ddrwg gennym, nid oedd modd dod o hyd i\'r dudalen roeddech yn chwilio amdani.',
    'sorry_page_not_found_permission_warning' => 'Os oeddech yn disgwyl i\'r dudalen hon fodoli, efallai na fyddai gennych ganiatâd i\'w gweld.',
    'image_not_found' => 'Heb ganfod y delwedd',
    'image_not_found_subtitle' => 'Mae\'n ddrwg gennym, ni fu modd dod o hyd i\'r ffeil delwedd roeddech yn chwilio amdani.',
    'image_not_found_details' => 'Os oeddech chi\'n disgwyl i\'r ddelwedd hon fodoli efallai ei bod wedi\'i dileu.',
    'return_home' => 'Dychwelyd i gartref',
    'error_occurred' => 'Digwyddodd Gwall',
    'app_down' => 'Mae :appName i lawr ar hyn o bryd',
    'back_soon' => 'Bydd yn ôl i fyny yn fuan.',

    // API errors
    'api_no_authorization_found' => 'Ni chanfuwyd tocyn awdurdodi ar y cais',
    'api_bad_authorization_format' => 'Canfuwyd tocyn awdurdodi ar y cais ond roedd yn ymddangos bod y fformat yn anghywir',
    'api_user_token_not_found' => 'Ni chanfuwyd tocyn API cyfatebol ar gyfer y tocyn awdurdodi a ddarparwyd',
    'api_incorrect_token_secret' => 'Mae\'r gyfrinach a ddarparwyd ar gyfer y tocyn API defnyddiedig a roddwyd yn anghywir',
    'api_user_no_api_permission' => 'Nid oes gan berchennog y tocyn API a ddefnyddiwyd ganiatâd i wneud galwadau API',
    'api_user_token_expired' => 'Mae\'r tocyn awdurdodi a ddefnyddiwyd wedi dod i ben',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Gwall a daflwyd wrth anfon e-bost prawf:',

];
