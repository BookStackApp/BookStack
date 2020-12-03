<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => '권한이 없습니다.',
    'permissionJson' => '권한이 없습니다.',

    // Auth
    'error_user_exists_different_creds' => ':email(을)를 가진 다른 사용자가 있습니다.',
    'email_already_confirmed' => '확인이 끝난 메일 주소입니다. 로그인하세요.',
    'email_confirmation_invalid' => '이 링크는 더 이상 유효하지 않습니다. 다시 가입하세요.',
    'email_confirmation_expired' => '이 링크는 더 이상 유효하지 않습니다. 메일을 다시 보냈습니다.',
    'email_confirmation_awaiting' => '사용 중인 계정의 이메일 주소를 확인해 주어야 합니다.',
    'ldap_fail_anonymous' => '익명 정보로 LDAP 서버에 접근할 수 없습니다.',
    'ldap_fail_authed' => '이 정보로 LDAP 서버에 접근할 수 없습니다.',
    'ldap_extension_not_installed' => 'PHP에 LDAP 확장 도구를 설치하세요.',
    'ldap_cannot_connect' => 'LDAP 서버에 연결할 수 없습니다.',
    'saml_already_logged_in' => '이미 로그인되어있습니다.',
    'saml_user_not_registered' => '사용자 이름이 등록되지 않았으며 자동 계정 등록이 활성화되지 않았습니다.',
    'saml_no_email_address' => '이 사용자에 대하여 외부 인증시스템에 의해 제공된 데이타 중 이메일 주소를 찾을 수 없습니다.',
    'saml_invalid_response_id' => '이 응용프로그램에 의해 시작된 프로세스에 의하면 외부 인증시스템으로 온 요청이 인식되지 않습니다. 인증 후에 뒤로가기 기능을 사용했을 경우 이런 현상이 발생할 수 있습니다.',
    'saml_fail_authed' => '시스템 로그인에 실패하였습니다. ( 해당 시스템이 인증성공값을 제공하지 않았습니다. )',
    'social_no_action_defined' => '무슨 활동인지 알 수 없습니다.',
    'social_login_bad_response' => ":socialAccount에 로그인할 수 없습니다. : \\n:error",
    'social_account_in_use' => ':socialAccount(을)를 가진 사용자가 있습니다. :socialAccount로 로그인하세요.',
    'social_account_email_in_use' => ':email(을)를 가진 사용자가 있습니다. 쓰고 있는 계정을 :socialAccount에 연결하세요.',
    'social_account_existing' => ':socialAccount(와)과 연결 상태입니다.',
    'social_account_already_used_existing' => ':socialAccount(와)과 연결한 다른 계정이 있습니다.',
    'social_account_not_used' => ':socialAccount(와)과 연결한 계정이 없습니다. 쓰고 있는 계정을 연결하세요.',
    'social_account_register_instructions' => '계정이 없어도 :socialAccount로 가입할 수 있습니다.',
    'social_driver_not_found' => '가입할 수 없습니다.',
    'social_driver_not_configured' => ':socialAccount가 유효하지 않습니다.',
    'invite_token_expired' => '이 링크는 더 이상 유효하지 않습니다. 비밀번호를 바꾸세요.',

    // System
    'path_not_writable' => ':filePath에 쓰는 것을 서버에서 허용하지 않습니다.',
    'cannot_get_image_from_url' => ':url에서 이미지를 불러올 수 없습니다.',
    'cannot_create_thumbs' => '섬네일을 못 만들었습니다. PHP에 GD 확장 도구를 설치하세요.',
    'server_upload_limit' => '파일 크기가 서버에서 허용하는 수치를 넘습니다.',
    'uploaded'  => '파일 크기가 서버에서 허용하는 수치를 넘습니다.',
    'image_upload_error' => '이미지를 올리다 문제가 생겼습니다.',
    'image_upload_type_error' => '유효하지 않은 이미지 형식입니다.',
    'file_upload_timeout' => '파일을 올리는 데 걸리는 시간이 서버에서 허용하는 수치를 넘습니다.',

    // Attachments
    'attachment_not_found' => '첨부 파일이 없습니다.',

    // Pages
    'page_draft_autosave_fail' => '초안 문서를 유실했습니다. 인터넷 연결 상태를 확인하세요.',
    'page_custom_home_deletion' => '처음 페이지는 지울 수 없습니다.',

    // Entities
    'entity_not_found' => '항목이 없습니다.',
    'bookshelf_not_found' => '서가가 없습니다.',
    'book_not_found' => '책자가 없습니다.',
    'page_not_found' => '문서가 없습니다.',
    'chapter_not_found' => '챕터가 없습니다.',
    'selected_book_not_found' => '고른 책자가 없습니다.',
    'selected_book_chapter_not_found' => '고른 책자나 챕터가 없습니다.',
    'guests_cannot_save_drafts' => 'Guest는 초안 문서를 보관할 수 없습니다.',

    // Users
    'users_cannot_delete_only_admin' => 'Admin을 삭제할 수 없습니다.',
    'users_cannot_delete_guest' => 'Guest를 삭제할 수 없습니다.',

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
    '404_page_not_found' => '404 Not Found',
    'sorry_page_not_found' => '문서를 못 찾았습니다.',
    'sorry_page_not_found_permission_warning' => '이 페이지가 존재하기를 기대했다면, 볼 수 있는 권한이 없을 수 있다.',
    'return_home' => '처음으로 돌아가기',
    'error_occurred' => '문제가 생겼습니다.',
    'app_down' => ':appName에 문제가 있는 것 같습니다',
    'back_soon' => '곧 되돌아갑니다.',

    // API errors
    'api_no_authorization_found' => '요청에서 인증 토큰을 찾을 수 없다.',
    'api_bad_authorization_format' => '요청에서 인증 토큰을 찾았지만, 형식이 잘못된 것 같다.',
    'api_user_token_not_found' => '제공된 인증 토큰과 일치하는 API 토큰을 찾을 수 없다.',
    'api_incorrect_token_secret' => '사용한 API 토큰에 대해 제공한 시크릿이 맞지 않는다.',
    'api_user_no_api_permission' => '사용한 API 토큰의 소유자가, API 호출을 할 수 있는 권한이 없다.',
    'api_user_token_expired' => '사용된 인증 토큰이 만료되었다.',

    // Settings & Maintenance
    'maintenance_test_email_failure' => '테스트 이메일 발송할 때 발생한 오류:',

];
