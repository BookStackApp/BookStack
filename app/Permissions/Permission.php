<?php

namespace BookStack\Permissions;

/**
 * Enum to represent the permissions which may be used in checks.
 * These generally align with RolePermission names, although some are abstract or truncated as some checks
 * are performed across a range of different items which may be subject to inheritance and other complications.
 *
 * We use and still allow the string values in usage to allow for compatibility with scenarios where
 * users have customised their instance with additional permissions via the theme system.
 * This enum primarily exists for alignment within the codebase.
 *
 * Permissions with all/own suffixes may also be represented as a higher-level alias without the own/all
 * suffix, which are used and assessed in the permission system logic.
 */
enum Permission: string
{
    // Generic Actions
    // Used for more abstract entity permission checks
    case View = 'view';
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';

    // System Permissions
    case AccessApi = 'access-api';
    case ContentExport = 'content-export';
    case ContentImport = 'content-import';
    case EditorChange = 'editor-change';
    case ReceiveNotifications = 'receive-notifications';
    case RestrictionsManage = 'restrictions-manage';
    case RestrictionsManageAll = 'restrictions-manage-all';
    case RestrictionsManageOwn = 'restrictions-manage-own';
    case SettingsManage = 'settings-manage';
    case TemplatesManage = 'templates-manage';
    case UserRolesManage = 'user-roles-manage';
    case UsersManage = 'users-manage';

    // Non-entity content permissions
    case AttachmentCreate = 'attachment-create';
    case AttachmentCreateAll = 'attachment-create-all';
    case AttachmentCreateOwn = 'attachment-create-own';
    case AttachmentDelete = 'attachment-delete';
    case AttachmentDeleteAll = 'attachment-delete-all';
    case AttachmentDeleteOwn = 'attachment-delete-own';
    case AttachmentUpdate = 'attachment-update';
    case AttachmentUpdateAll = 'attachment-update-all';
    case AttachmentUpdateOwn = 'attachment-update-own';

    case CommentCreateAll = 'comment-create-all';
    case CommentDelete = 'comment-delete';
    case CommentDeleteAll = 'comment-delete-all';
    case CommentDeleteOwn = 'comment-delete-own';
    case CommentUpdate = 'comment-update';
    case CommentUpdateAll = 'comment-update-all';
    case CommentUpdateOwn = 'comment-update-own';

    case ImageCreateAll = 'image-create-all';
    case ImageCreateOwn = 'image-create-own';
    case ImageDelete = 'image-delete';
    case ImageDeleteAll = 'image-delete-all';
    case ImageDeleteOwn = 'image-delete-own';
    case ImageUpdate = 'image-update';
    case ImageUpdateAll = 'image-update-all';
    case ImageUpdateOwn = 'image-update-own';

    // Entity content permissions
    case BookCreate = 'book-create';
    case BookCreateAll = 'book-create-all';
    case BookCreateOwn = 'book-create-own';
    case BookDelete = 'book-delete';
    case BookDeleteAll = 'book-delete-all';
    case BookDeleteOwn = 'book-delete-own';
    case BookUpdate = 'book-update';
    case BookUpdateAll = 'book-update-all';
    case BookUpdateOwn = 'book-update-own';
    case BookView = 'book-view';
    case BookViewAll = 'book-view-all';
    case BookViewOwn = 'book-view-own';

    case BookshelfCreate = 'bookshelf-create';
    case BookshelfCreateAll = 'bookshelf-create-all';
    case BookshelfCreateOwn = 'bookshelf-create-own';
    case BookshelfDelete = 'bookshelf-delete';
    case BookshelfDeleteAll = 'bookshelf-delete-all';
    case BookshelfDeleteOwn = 'bookshelf-delete-own';
    case BookshelfUpdate = 'bookshelf-update';
    case BookshelfUpdateAll = 'bookshelf-update-all';
    case BookshelfUpdateOwn = 'bookshelf-update-own';
    case BookshelfView = 'bookshelf-view';
    case BookshelfViewAll = 'bookshelf-view-all';
    case BookshelfViewOwn = 'bookshelf-view-own';

    case ChapterCreate = 'chapter-create';
    case ChapterCreateAll = 'chapter-create-all';
    case ChapterCreateOwn = 'chapter-create-own';
    case ChapterDelete = 'chapter-delete';
    case ChapterDeleteAll = 'chapter-delete-all';
    case ChapterDeleteOwn = 'chapter-delete-own';
    case ChapterUpdate = 'chapter-update';
    case ChapterUpdateAll = 'chapter-update-all';
    case ChapterUpdateOwn = 'chapter-update-own';
    case ChapterView = 'chapter-view';
    case ChapterViewAll = 'chapter-view-all';
    case ChapterViewOwn = 'chapter-view-own';

    case PageCreate = 'page-create';
    case PageCreateAll = 'page-create-all';
    case PageCreateOwn = 'page-create-own';
    case PageDelete = 'page-delete';
    case PageDeleteAll = 'page-delete-all';
    case PageDeleteOwn = 'page-delete-own';
    case PageUpdate = 'page-update';
    case PageUpdateAll = 'page-update-all';
    case PageUpdateOwn = 'page-update-own';
    case PageView = 'page-view';
    case PageViewAll = 'page-view-all';
    case PageViewOwn = 'page-view-own';

    /**
     * Get the generic permissions which may be queried for entities.
     */
    public static function genericForEntity(): array
    {
        return [
            self::View,
            self::Create,
            self::Update,
            self::Delete,
        ];
    }

    /**
     * Return the application permission-check middleware-string for this permission.
     * Uses registered CheckUserHasPermission middleware.
     */
    public function middleware(): string
    {
        return 'can:' . $this->value;
    }
}
