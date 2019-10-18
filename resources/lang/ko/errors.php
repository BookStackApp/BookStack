<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => '요청한 페이지에 권한이 없습니다.',
    'permissionJson' => '요청한 작업을 수행 할 권한이 없습니다.',

    // Auth
    'error_user_exists_different_creds' => '전자 메일 :email을 가진 사용자가 이미 존재하지만 자격 증명이 다릅니다.',
    'email_already_confirmed' => '이메일이 이미 확인되었습니다. 로그인 해주세요.',
    'email_confirmation_invalid' => '이 확인 토큰이 유효하지 않거나 이미 사용되었습니다. 다시 등록하세요.',
    'email_confirmation_expired' => '확인 토큰이 만료되었습니다. 새 확인 이메일이 전송되었습니다.',
    'ldap_fail_anonymous' => '익명 바인드를 이용한 LDAP 액세스에 실패하였습니다.',
    'ldap_fail_authed' => '주어진 dn 및 비밀번호 세부 정보를 사용하여 LDAP 액세스하는 것이 실패했습니다.',
    'ldap_extension_not_installed' => 'LDAP PHP 확장기능이 설치되지 않았습니다.',
    'ldap_cannot_connect' => 'LDAP 서버에 연결할 수 없습니다. 초기 연결에 실패했습니다.',
    'social_no_action_defined' => '동작이 정의되지 않았습니다.',
    'social_login_bad_response' => ":socialAccount 로그인에 실패하였습니다 : \n:error",
    'social_account_in_use' => '이 :socialAccount 계정이 이미 사용 중입니다. :socialAccount 옵션을 통해 로그인하십시오.',
    'social_account_email_in_use' => ' 이메일 :email이 이미 사용 중입니다. 이미 계정이있는 경우 프로필 설정에서 :socialAccount 계정을 연결할 수 있습니다.',
    'social_account_existing' => ':socialAccount가 이미 프로필에 첨부되어 있습니다.',
    'social_account_already_used_existing' => '이 :socialAccount 계정은 이미 다른 사용자가 사용하고 있습니다.',
    'social_account_not_used' => '이 :socialAccount 계정이 모든 사용자에게 연결되어 있지 않습니다. 프로필 설정에 첨부하십시오. ',
    'social_account_register_instructions' => '아직 계정이없는 경우 :socialAccount 옵션을 사용하여 계정을 등록 할 수 있습니다.',
    'social_driver_not_found' => '소셜 드라이버를 찾을 수 없음',
    'social_driver_not_configured' => '귀하의 :socialAccount 소셜 설정이 올바르게 구성되지 않았습니다.',
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

    // System
    'path_not_writable' => '파일 경로 :filePath에 업로드 할 수 없습니다. 서버에 쓰기 기능이 활성화 되어있는지 확인하세요.',
    'cannot_get_image_from_url' => ':url에서 이미지를 가져올 수 없습니다.',
    'cannot_create_thumbs' => '서버에서 썸네일을 생성할 수 없습니다. GD PHP확장기능이 설치되어있는지 확인하세요.',
    'server_upload_limit' => '해당 크기의 파일을 업로드하는것이 서버에서 제한됩니다. 파일 사이즈를 작게 줄이거나 서버 설정을 변경하세요.',
    'uploaded'  => '해당 크기의 파일을 업로드하는것이 서버에서 제한됩니다. 파일 사이즈를 작게 줄이거나 서버 설정을 변경하세요.',
    'image_upload_error' => '이미지를 업로드하는 중에 오류가 발생했습니다.',
    'image_upload_type_error' => '업로드중인 이미지 유형이 잘못되었습니다.',
    'file_upload_timeout' => '파일 업로드가 시간 초과되었습니다.',

    // Attachments
    'attachment_page_mismatch' => '첨부 파일 업데이트 중 페이지 불일치하였습니다.',
    'attachment_not_found' => '첨부 파일을 찾을 수 없습니다.',

    // Pages
    'page_draft_autosave_fail' => '초안을 저장하지 못했습니다. 이 페이지를 저장하기 전에 인터넷에 연결되어 있는지 확인하십시오.',
    'page_custom_home_deletion' => '홈페이지로 설정되어있는 페이지는 삭제할 수 없습니다.',

    // Entities
    'entity_not_found' => '개체(Entity)를 찾을 수 없음.',
    'bookshelf_not_found' => '책꽂이를 찾을 수 없음.',
    'book_not_found' => '책을 찾을 수 없음.',
    'page_not_found' => '페이지를 찾을 수 없음.',
    'chapter_not_found' => '챕터를 찾을 수 없음.',
    'selected_book_not_found' => '선택한 책을 찾을 수 없습니다.',
    'selected_book_chapter_not_found' => '선택한 책 또는 챕터를 찾을 수 없습니다.',
    'guests_cannot_save_drafts' => '게스트는 임시저장을 할 수 없습니다.',

    // Users
    'users_cannot_delete_only_admin' => '어드민 계정은 삭제할 수 없습니다.',
    'users_cannot_delete_guest' => '게스트 사용자는 삭제할 수 없습니다.',

    // Roles
    'role_cannot_be_edited' => '역할을 수정할 수 없습니다.',
    'role_system_cannot_be_deleted' => '이 역할은 시스템 역할입니다. 삭제할 수 없습니다.',
    'role_registration_default_cannot_delete' => '이 역할은 기본 등록 역할로 설정되어있는 동안 삭제할 수 없습니다.',
    'role_cannot_remove_only_admin' => 'This user is the only user assigned to the administrator role. Assign the administrator role to another user before attempting to remove it here.',

    // Comments
    'comment_list' => '댓글을 가져 오는 중에 오류가 발생했습니다.',
    'cannot_add_comment_to_draft' => '초안에 주석을 추가 할 수 없습니다.',
    'comment_add' => '댓글을 추가 / 업데이트하는 중에 오류가 발생했습니다.',
    'comment_delete' => '댓글을 삭제하는 중에 오류가 발생했습니다.',
    'empty_comment' => '빈 주석을 추가 할 수 없습니다.',

    // Error pages
    '404_page_not_found' => '페이지를 찾을 수 없습니다.',
    'sorry_page_not_found' => '죄송합니다, 찾고 있던 페이지를 찾을 수 없습니다.',
    'return_home' => 'home으로 가기',
    'error_occurred' => '오류가 발생하였습니다.',
    'app_down' => ':appName가 다운되었습니다.',
    'back_soon' => '곧 복구될 예정입니다.',

];
