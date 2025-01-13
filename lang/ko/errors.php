<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => '요청된 페이지에 액세스할 수 있는 권한이 없습니다.',
    'permissionJson' => '요청된 작업을 수행할 수 있는 권한이 없습니다.',

    // Auth
    'error_user_exists_different_creds' => '이메일 :email 이 이미 존재하지만 다른 자격 증명을 가진 사용자입니다.',
    'auth_pre_register_theme_prevention' => '제공된 세부 정보로 사용자 계정을 등록할 수 없습니다',
    'email_already_confirmed' => '이메일이 이미 확인되었으니 로그인해 보세요.',
    'email_confirmation_invalid' => '이 확인 토큰이 유효하지 않거나 이미 사용되었습니다. 다시 등록해 주세요.',
    'email_confirmation_expired' => '확인 토큰이 만료되었습니다. 새 확인 이메일이 전송되었습니다.',
    'email_confirmation_awaiting' => '사용 중인 계정의 이메일 주소를 확인해야 합니다.',
    'ldap_fail_anonymous' => '익명 바인딩을 사용하여 LDAP 액세스에 실패했습니다.',
    'ldap_fail_authed' => '주어진 dn 및 암호 세부 정보를 사용하여 LDAP 액세스에 실패했습니다.',
    'ldap_extension_not_installed' => 'LDAP PHP 확장이 설치되지 않았습니다.',
    'ldap_cannot_connect' => 'LDAP 서버에 연결할 수 없음, 초기 연결 실패',
    'saml_already_logged_in' => '이미 로그인했습니다.',
    'saml_no_email_address' => '외부 인증 시스템에서 제공한 데이터에서 이 사용자의 이메일 주소를 찾을 수 없습니다.',
    'saml_invalid_response_id' => '이 애플리케이션에서 시작한 프로세스에서 외부 인증 시스템의 요청을 인식하지 못합니다. 로그인 후 다시 이동하면 이 문제가 발생할 수 있습니다.',
    'saml_fail_authed' => ':system 을 사용하여 로그인, 시스템이 성공적인 인증을 제공하지 않음',
    'oidc_already_logged_in' => '이미 로그인했습니다.',
    'oidc_no_email_address' => '외부 인증 시스템에서 제공한 데이터에서 이 사용자의 이메일 주소를 찾을 수 없습니다.',
    'oidc_fail_authed' => ':system 을 사용하여 로그인, 시스템이 성공적인 인증을 제공하지 않음',
    'social_no_action_defined' => '정의된 동작 없음',
    'social_login_bad_response' => ":socialAccount 로 로그인 동안 에러가 발생했습니다: \n:error",
    'social_account_in_use' => ':socialAccount(을)를 가진 사용자가 있습니다. :socialAccount 옵션을 통해 로그인해 보세요.',
    'social_account_email_in_use' => '이메일 :email 은(는) 이미 사용 중입니다. 이미 계정이 있는 경우 프로필 설정에서 :socialAccount 계정을 연결할 수 있습니다.',
    'social_account_existing' => '이 :socialAccount 는 이미 프로필에 연결되어 있습니다.',
    'social_account_already_used_existing' => '이 :socialAccount 계정은 다른 사용자가 이미 사용하고 있습니다.',
    'social_account_not_used' => '이 :socialAccount 계정은 어떤 사용자와도 연결되어 있지 않습니다. 프로필 설정에서 첨부하세요. ',
    'social_account_register_instructions' => '아직 계정이 없는 경우 :socialAccount 옵션을 사용하여 계정을 등록할 수 있습니다.',
    'social_driver_not_found' => '소셜 드라이버를 찾을 수 없습니다.',
    'social_driver_not_configured' => '소셜 계정 :socialAccount 가(이) 올바르게 구성되지 않았습니다.',
    'invite_token_expired' => '이 초대 링크가 만료되었습니다. 대신 계정 비밀번호 재설정을 시도해 보세요.',
    'login_user_not_found' => '이 동작의 사용자를 찾을 수 없습니다.',

    // System
    'path_not_writable' => '파일 경로 :filePath 에 업로드할 수 없습니다. 서버에 저장이 가능한지 확인하세요.',
    'cannot_get_image_from_url' => ':url 에서 이미지를 가져올 수 없습니다.',
    'cannot_create_thumbs' => '서버에서 썸네일을 만들 수 없습니다. GD PHP 확장이 설치되어 있는지 확인하세요.',
    'server_upload_limit' => '서버에서 이 크기의 업로드를 허용하지 않습니다. 더 작은 파일 크기를 시도해 보세요.',
    'server_post_limit' => '서버가 제공된 데이터 양을 수신할 수 없습니다. 더 적은 데이터 또는 더 작은 파일로 다시 시도하세요.',
    'uploaded'  => '서버에서 이 크기의 업로드를 허용하지 않습니다. 더 작은 파일 크기를 시도해 보세요.',

    // Drawing & Images
    'image_upload_error' => '이미지를 업로드하는 동안 오류가 발생했습니다.',
    'image_upload_type_error' => '업로드 중인 이미지 유형이 유효하지 않습니다.',
    'image_upload_replace_type' => '이미지 파일 교체는 반드시 동일한 유형이어야 합니다.',
    'image_upload_memory_limit' => '시스템 리소스 제한으로 인해 이미지 업로드를 처리하거나 미리보기 이미지를 만들지 못했습니다.',
    'image_thumbnail_memory_limit' => '시스템 리소스 제한으로 인해 이미지 크기 변형을 만들지 못했습니다.',
    'image_gallery_thumbnail_memory_limit' => '시스템 리소스 제한으로 인해 갤러리 썸네일을 만들지 못했습니다.',
    'drawing_data_not_found' => '드로잉 데이터를 로드할 수 없습니다. 드로잉 파일이 더 이상 존재하지 않거나 해당 파일에 액세스할 수 있는 권한이 없을 수 있습니다.',

    // Attachments
    'attachment_not_found' => '첨부 파일을 찾을 수 없습니다.',
    'attachment_upload_error' => '첨부 파일을 업로드하는 동안 오류가 발생했습니다.',

    // Pages
    'page_draft_autosave_fail' => '초안을 저장하지 못했습니다. 이 페이지를 저장하기 전에 인터넷에 연결되어 있는지 확인하세요.',
    'page_draft_delete_fail' => '페이지 초안을 삭제하고 현재 페이지에 저장된 콘텐츠를 가져오지 못했습니다.',
    'page_custom_home_deletion' => '페이지가 홈페이지로 설정되어 있는 동안에는 삭제할 수 없습니다.',

    // Entities
    'entity_not_found' => '항목이 없습니다.',
    'bookshelf_not_found' => '책장을 찾을 수 없음',
    'book_not_found' => '책이 없습니다.',
    'page_not_found' => '문서가 없습니다.',
    'chapter_not_found' => '챕터가 없습니다.',
    'selected_book_not_found' => '고른 책이 없습니다.',
    'selected_book_chapter_not_found' => '고른 책이나 챕터가 없습니다.',
    'guests_cannot_save_drafts' => 'Guest는 초안 문서를 보관할 수 없습니다.',

    // Users
    'users_cannot_delete_only_admin' => 'Admin을 삭제할 수 없습니다.',
    'users_cannot_delete_guest' => 'Guest를 삭제할 수 없습니다.',
    'users_could_not_send_invite' => '초대 이메일을 보내는 데 실패하여 사용자를 생성할 수 없습니다.',

    // Roles
    'role_cannot_be_edited' => '권한을 수정할 수 없습니다.',
    'role_system_cannot_be_deleted' => '시스템 권한을 지울 수 없습니다.',
    'role_registration_default_cannot_delete' => '가입한 사용자의 기본 권한을 지울 수 있어야 합니다.',
    'role_cannot_remove_only_admin' => 'Admin을 가진 사용자가 적어도 한 명 있어야 합니다.',

    // Comments
    'comment_list' => '댓글을 가져오다 문제가 생겼습니다.',
    'cannot_add_comment_to_draft' => '초안 문서에 댓글을 달 수 없습니다.',
    'comment_add' => '댓글을 등록하다 문제가 생겼습니다.',
    'comment_delete' => '댓글을 지우다 문제가 생겼습니다.',
    'empty_comment' => '빈 댓글은 등록할 수 없습니다.',

    // Error pages
    '404_page_not_found' => '페이지를 찾을 수 없습니다.',
    'sorry_page_not_found' => '문서를 못 찾았습니다.',
    'sorry_page_not_found_permission_warning' => '문서를 볼 권한이 없습니다.',
    'image_not_found' => '이미지를 찾을 수 없습니다',
    'image_not_found_subtitle' => '이미지를 못 찾았습니다.',
    'image_not_found_details' => '이미지가 지워졌을 수 있습니다.',
    'return_home' => '처음으로 돌아가기',
    'error_occurred' => '문제가 생겼습니다.',
    'app_down' => ':appName에 문제가 생겼습니다.',
    'back_soon' => '곧 돌아갑니다.',

    // Import
    'import_zip_cant_read' => 'ZIP 파일을 읽을 수 없습니다.',
    'import_zip_cant_decode_data' => 'ZIP data.json 콘텐츠를 찾아서 디코딩할 수 없습니다.',
    'import_zip_no_data' => '컨텐츠 ZIP 파일 데이터에 데이터가 비어있습니다.',
    'import_validation_failed' => '컨텐츠 ZIP 파일을 가져오려다 실패했습니다. 이유:',
    'import_zip_failed_notification' => '컨텐츠 ZIP 파일을 가져오지 못했습니다.',
    'import_perms_books' => '책을 만드는 데 필요한 권한이 없습니다.',
    'import_perms_chapters' => '챕터를 만드는 데 필요한 권한이 없습니다.',
    'import_perms_pages' => '페이지를 만드는 데 필요한 권한이 없습니다.',
    'import_perms_images' => '이미지를 만드는 데 필요한 권한이 없습니다.',
    'import_perms_attachments' => '첨부 파일을 만드는 데 필요한 권한이 없습니다.',

    // API errors
    'api_no_authorization_found' => '요청에서 인증 토큰을 찾을 수 없습니다.',
    'api_bad_authorization_format' => '요청에서 인증 토큰을 찾았으나 형식에 문제가 있습니다.',
    'api_user_token_not_found' => '인증 토큰과 일치하는 API 토큰을 찾을 수 없습니다.',
    'api_incorrect_token_secret' => 'API 토큰이 제공한 암호에 문제가 있습니다.',
    'api_user_no_api_permission' => 'API 토큰의 소유자가 API를 호출할 권한이 없습니다.',
    'api_user_token_expired' => '인증 토큰이 만료되었습니다.',

    // Settings & Maintenance
    'maintenance_test_email_failure' => '메일을 발송하는 도중 문제가 생겼습니다:',

    // HTTP errors
    'http_ssr_url_no_match' => 'URL이 구성된 허용된 SSR 호스트와 일치하지 않습니다.',
];
