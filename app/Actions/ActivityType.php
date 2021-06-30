<?php

namespace BookStack\Actions;

class ActivityType
{
    const PAGE_CREATE = 'page_create';
    const PAGE_UPDATE = 'page_update';
    const PAGE_DELETE = 'page_delete';
    const PAGE_RESTORE = 'page_restore';
    const PAGE_MOVE = 'page_move';

    const CHAPTER_CREATE = 'chapter_create';
    const CHAPTER_UPDATE = 'chapter_update';
    const CHAPTER_DELETE = 'chapter_delete';
    const CHAPTER_MOVE = 'chapter_move';

    const BOOK_CREATE = 'book_create';
    const BOOK_UPDATE = 'book_update';
    const BOOK_DELETE = 'book_delete';
    const BOOK_SORT = 'book_sort';

    const BOOKSHELF_CREATE = 'bookshelf_create';
    const BOOKSHELF_UPDATE = 'bookshelf_update';
    const BOOKSHELF_DELETE = 'bookshelf_delete';

    const COMMENTED_ON = 'commented_on';
    const PERMISSIONS_UPDATE = 'permissions_update';

    const SETTINGS_UPDATE = 'settings_update';
    const MAINTENANCE_ACTION_RUN = 'maintenance_action_run';

    const RECYCLE_BIN_EMPTY = 'recycle_bin_empty';
    const RECYCLE_BIN_RESTORE = 'recycle_bin_restore';
    const RECYCLE_BIN_DESTROY = 'recycle_bin_destroy';

    const USER_CREATE = 'user_create';
    const USER_UPDATE = 'user_update';
    const USER_DELETE = 'user_delete';

    const API_TOKEN_CREATE = 'api_token_create';
    const API_TOKEN_UPDATE = 'api_token_update';
    const API_TOKEN_DELETE = 'api_token_delete';

    const ROLE_CREATE = 'role_create';
    const ROLE_UPDATE = 'role_update';
    const ROLE_DELETE = 'role_delete';

    const AUTH_PASSWORD_RESET = 'auth_password_reset_request';
    const AUTH_PASSWORD_RESET_UPDATE = 'auth_password_reset_update';
    const AUTH_LOGIN = 'auth_login';
    const AUTH_REGISTER = 'auth_register';

    const MFA_SETUP_METHOD = 'mfa_setup_method';
}
