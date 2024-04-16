<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => '생성된 페이지',
    'page_create_notification'    => '페이지가 성공적으로 생성되었습니다.',
    'page_update'                 => '페이지 업데이트됨',
    'page_update_notification'    => '페이지가 성공적으로 업데이트되었습니다.',
    'page_delete'                 => '삭제된 페이지',
    'page_delete_notification'    => '페이지가 성공적으로 삭제되었습니다.',
    'page_restore'                => '복구된 페이지',
    'page_restore_notification'   => '페이지가 성공적으로 복원되었습니다.',
    'page_move'                   => '이동된 페이지',
    'page_move_notification'      => '페이지가 성공적으로 이동되었습니다.',

    // Chapters
    'chapter_create'              => '챕터 만들기',
    'chapter_create_notification' => '챕터가 성공적으로 생성되었습니다.',
    'chapter_update'              => '업데이트된 챕터',
    'chapter_update_notification' => '챕터가 성공적으로 업데이트되었습니다.',
    'chapter_delete'              => '삭제된 챕터',
    'chapter_delete_notification' => '챕터가 성공적으로 삭제되었습니다.',
    'chapter_move'                => '이동된 챕터',
    'chapter_move_notification' => '챕터를 성공적으로 이동했습니다.',

    // Books
    'book_create'                 => '생성된 책',
    'book_create_notification'    => '책이 성공적으로 생성되었습니다.',
    'book_create_from_chapter'              => '챕터를 책으로 변환',
    'book_create_from_chapter_notification' => '챕터가 책으로 성공적으로 변환되었습니다.',
    'book_update'                 => '업데이트된 책',
    'book_update_notification'    => '책이 성공적으로 업데이트되었습니다.',
    'book_delete'                 => '삭제된 책',
    'book_delete_notification'    => '책이 성공적으로 삭제되었습니다.',
    'book_sort'                   => '정렬된 책',
    'book_sort_notification'      => '책이 성공적으로 재정렬되었습니다.',

    // Bookshelves
    'bookshelf_create'            => '책장 만들기',
    'bookshelf_create_notification'    => '책장을 성공적으로 생성했습니다.',
    'bookshelf_create_from_book'    => '책을 책장으로 변환함',
    'bookshelf_create_from_book_notification'    => '책을 성공적으로 책장으로 변환하였습니다.',
    'bookshelf_update'                 => '책장 업데이트됨',
    'bookshelf_update_notification'    => '책장이 성공적으로 업데이트 되었습니다.',
    'bookshelf_delete'                 => '삭제된 책장',
    'bookshelf_delete_notification'    => '책장이 성공적으로 삭제되었습니다.',

    // Revisions
    'revision_restore' => '복구된 리비전',
    'revision_delete' => '삭제된 리비전',
    'revision_delete_notification' => '리비전을 성공적으로 삭제하였습니다.',

    // Favourites
    'favourite_add_notification' => '":name" 을 북마크에 추가하였습니다.',
    'favourite_remove_notification' => '":name" 가 북마크에서 삭제되었습니다.',

    // Watching
    'watch_update_level_notification' => '주시 환경설정이 성공적으로 업데이트되었습니다.',

    // Auth
    'auth_login' => '로그인 됨',
    'auth_register' => '신규 사용자 등록',
    'auth_password_reset_request' => '사용자 비밀번호 초기화 요청',
    'auth_password_reset_update' => '사용자 비밀번호 초기화',
    'mfa_setup_method' => '구성된 MFA 방법',
    'mfa_setup_method_notification' => '다중 인증 설정함',
    'mfa_remove_method' => 'MFA 메서드 제거',
    'mfa_remove_method_notification' => '다중 인증 해제함',

    // Settings
    'settings_update' => '설정 변경',
    'settings_update_notification' => '설졍 변경 성공',
    'maintenance_action_run' => '유지 관리 작업 실행',

    // Webhooks
    'webhook_create' => '웹 훅 만들기',
    'webhook_create_notification' => '웹 훅 생성함',
    'webhook_update' => '웹 훅 수정하기',
    'webhook_update_notification' => '웹 훅 수정함',
    'webhook_delete' => '웹 훅 지우기',
    'webhook_delete_notification' => '웹 훅 삭제함',

    // Users
    'user_create' => '사용자 생성',
    'user_create_notification' => '사용자 생성 성공',
    'user_update' => '사용자 갱신',
    'user_update_notification' => '사용자가 업데이트되었습니다',
    'user_delete' => '사용자 삭제',
    'user_delete_notification' => '사용자가 삭제되었습니다',

    // API Tokens
    'api_token_create' => 'created API token',
    'api_token_create_notification' => 'API 토큰이 성공적으로 생성되었습니다.',
    'api_token_update' => 'updated API token',
    'api_token_update_notification' => 'API 토큰이 성공적으로 업데이트되었습니다.',
    'api_token_delete' => 'deleted API token',
    'api_token_delete_notification' => 'API 토큰이 성공적으로 삭제되었습니다.',

    // Roles
    'role_create' => '역활 생성',
    'role_create_notification' => '역할이 생성되었습니다',
    'role_update' => '역활 갱신',
    'role_update_notification' => '역할이 수정되었습니다',
    'role_delete' => '역활 삭제',
    'role_delete_notification' => '역할이 삭제되었습니다',

    // Recycle Bin
    'recycle_bin_empty' => '비운 휴지통',
    'recycle_bin_restore' => '휴지통에서 복원됨',
    'recycle_bin_destroy' => '휴지통에서 제거됨',

    // Comments
    'commented_on'                => '댓글 쓰기',
    'comment_create'              => '댓글 생성',
    'comment_update'              => '댓글 변경',
    'comment_delete'              => '댓글 삭제',

    // Other
    'permissions_update'          => '권한 수정함',
];
