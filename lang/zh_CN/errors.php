<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => '您无权访问所请求的页面。',
    'permissionJson' => '您无权执行所请求的操作。',

    // Auth
    'error_user_exists_different_creds' => 'Email为 :email 的用户已经存在，但具有不同的凭据。',
    'email_already_confirmed' => 'Email已被确认，请尝试登录。',
    'email_confirmation_invalid' => '此确认令牌无效或已被使用，请重新注册。',
    'email_confirmation_expired' => '确认令牌已过期，已发送新的确认电子邮件。',
    'email_confirmation_awaiting' => '需要认证账户的电子邮箱地址',
    'ldap_fail_anonymous' => '使用匿名绑定的LDAP访问失败。',
    'ldap_fail_authed' => '带有标识名称和密码的LDAP访问失败。',
    'ldap_extension_not_installed' => '未安装LDAP PHP扩展程序',
    'ldap_cannot_connect' => '无法连接到ldap服务器，初始连接失败',
    'saml_already_logged_in' => '您已经登陆了',
    'saml_user_not_registered' => '用户 :name 未注册且自动注册功能已被禁用',
    'saml_no_email_address' => '无法找到有效Email地址，此用户数据由外部身份验证系统托管',
    'saml_invalid_response_id' => '来自外部身份验证系统的请求没有被本应用程序认证，在登录后返回上一页可能会导致此问题。',
    'saml_fail_authed' => '使用 :system 登录失败，登录系统未返回成功登录授权信息。',
    'oidc_already_logged_in' => '您已经登陆了',
    'oidc_user_not_registered' => '用户 :name 尚未注册，自助注册功能已被禁用',
    'oidc_no_email_address' => '无法找到有效的 Email 地址，此用户数据由外部身份验证系统托管',
    'oidc_fail_authed' => '使用 :system 登录失败，登录系统未返回成功登录授权信息',
    'social_no_action_defined' => '没有定义行为',
    'social_login_bad_response' => "在 :socialAccount 登录时遇到错误：\n:error",
    'social_account_in_use' => ':socialAccount 账户已被使用，请尝试通过 :socialAccount 选项登录。',
    'social_account_email_in_use' => 'Email :email 已经被使用。如果您已有账户，则可以在个人资料设置中绑定您的 :socialAccount。',
    'social_account_existing' => ':socialAccount已经被绑定到您的账户。',
    'social_account_already_used_existing' => ':socialAccount账户已经被其他用户使用。',
    'social_account_not_used' => ':socialAccount账户没有绑定到任何用户，请在您的个人资料设置中绑定。',
    'social_account_register_instructions' => '如果您还没有账户，您可以使用 :socialAccount 选项注册账户。',
    'social_driver_not_found' => '未找到社交驱动程序',
    'social_driver_not_configured' => '您的:socialAccount社交设置不正确。',
    'invite_token_expired' => '此邀请链接已过期。 您可以尝试重置您的账户密码。',

    // System
    'path_not_writable' => '无法上传到文件路径“:filePath”，请确保它可写入服务器。',
    'cannot_get_image_from_url' => '无法从 :url 中获取图片',
    'cannot_create_thumbs' => '服务器无法创建缩略图，请检查您是否安装了GD PHP扩展。',
    'server_upload_limit' => '服务器不允许上传此大小的文件。 请尝试较小的文件。',
    'server_post_limit' => '服务器无法接收所提供的数据量。请尝试使用较少的数据或较小的文件。',
    'uploaded'  => '服务器不允许上传此大小的文件。 请尝试较小的文件。',

    // Drawing & Images
    'image_upload_error' => '上传图片时发生错误',
    'image_upload_type_error' => '上传的图像类型无效',
    'image_upload_replace_type' => '图片文件替换必须为相同的类型',
    'image_upload_memory_limit' => '由于系统资源限制，无法处理图像上传和/或创建缩略图。',
    'image_thumbnail_memory_limit' => '由于系统资源限制，无法创建图像大小变化。',
    'image_gallery_thumbnail_memory_limit' => '由于系统资源限制，无法创建相册缩略图。',
    'drawing_data_not_found' => '无法加载绘图数据。绘图文件可能不再存在，或者您可能没有权限访问它。',

    // Attachments
    'attachment_not_found' => '找不到附件',
    'attachment_upload_error' => '上传附件时出错',

    // Pages
    'page_draft_autosave_fail' => '无法保存草稿，确保您在保存页面之前已经连接到互联网',
    'page_draft_delete_fail' => '无法删除页面草稿并获取当前页面已保存的内容',
    'page_custom_home_deletion' => '无法删除一个被设置为主页的页面',

    // Entities
    'entity_not_found' => '未找到项目',
    'bookshelf_not_found' => '未找到书架',
    'book_not_found' => '未找到图书',
    'page_not_found' => '未找到页面',
    'chapter_not_found' => '未找到章节',
    'selected_book_not_found' => '选中的书未找到',
    'selected_book_chapter_not_found' => '未找到所选的图书或章节',
    'guests_cannot_save_drafts' => '访客不能保存草稿',

    // Users
    'users_cannot_delete_only_admin' => '您不能删除唯一的管理员账户',
    'users_cannot_delete_guest' => '您不能删除访客用户',

    // Roles
    'role_cannot_be_edited' => '无法编辑该角色',
    'role_system_cannot_be_deleted' => '无法删除系统角色',
    'role_registration_default_cannot_delete' => '无法删除设置为默认注册的角色',
    'role_cannot_remove_only_admin' => '该用户是分配给管理员角色的唯一用户。 在尝试在此处删除管理员角色之前，请将其分配给其他用户。',

    // Comments
    'comment_list' => '提取评论时出现错误。',
    'cannot_add_comment_to_draft' => '您不能为草稿添加评论。',
    'comment_add' => '添加/更新评论时发生错误。',
    'comment_delete' => '删除评论时发生错误。',
    'empty_comment' => '不能添加空的评论。',

    // Error pages
    '404_page_not_found' => '无法找到页面',
    'sorry_page_not_found' => '对不起，无法找到您想访问的页面。',
    'sorry_page_not_found_permission_warning' => '如果您确认这个页面存在，则代表您可能没有查看权限。',
    'image_not_found' => '未找到图片',
    'image_not_found_subtitle' => '对不起，无法找到您想访问的图片。',
    'image_not_found_details' => '原本放在这里的图片已被删除。',
    'return_home' => '返回主页',
    'error_occurred' => '出现错误',
    'app_down' => ':appName现在正在关闭',
    'back_soon' => '请耐心等待网站的恢复。',

    // API errors
    'api_no_authorization_found' => '未在请求中找到授权令牌',
    'api_bad_authorization_format' => '已在请求中找到授权令牌，但格式貌似不正确',
    'api_user_token_not_found' => '未找到与提供的授权令牌匹配的 API 令牌',
    'api_incorrect_token_secret' => '给已给出的API所提供的密钥不正确',
    'api_user_no_api_permission' => '使用过的 API 令牌的所有者没有进行API 调用的权限',
    'api_user_token_expired' => '所使用的身份令牌已过期',

    // Settings & Maintenance
    'maintenance_test_email_failure' => '发送测试电子邮件时出现错误：',

    // HTTP errors
    'http_ssr_url_no_match' => 'URL 与已配置的 SSR 主机不匹配',
];
