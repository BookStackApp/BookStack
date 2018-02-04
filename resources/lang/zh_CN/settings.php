<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => '设置',
    'settings_save' => '保存设置',
    'settings_save_success' => '设置已保存',

    /**
     * App settings
     */

    'app_settings' => 'App设置',
    'app_name' => 'App名',
    'app_name_desc' => '此名称将在网页头部和Email中显示。',
    'app_name_header' => '在网页头部显示应用名？',
    'app_public_viewing' => '允许公众查看？',
    'app_secure_images' => '启用更高安全性的图片上传？',
    'app_secure_images_desc' => '出于性能原因，所有图像都是公开的。这个选项会在图像的网址前添加一个随机的，难以猜测的字符串，从而使直接访问变得困难。',
    'app_editor' => '页面编辑器',
    'app_editor_desc' => '选择所有用户将使用哪个编辑器来编辑页面。',
    'app_custom_html' => '自定义HTML头部内容',
    'app_custom_html_desc' => '此处添加的任何内容都将插入到每个页面的<head>部分的底部，这对于覆盖样式或添加分析代码很方便。',
    'app_logo' => 'App Logo',
    'app_logo_desc' => '这个图片的高度应该为43px。<br>大图片将会被缩小。',
    'app_primary_color' => 'App主色',
    'app_primary_color_desc' => '这应该是一个十六进制值。<br>保留为空以重置为默认颜色。',
    'app_homepage' => 'App主页',
    'app_homepage_desc' => '选择要在主页上显示的页面来替换默认的视图，选定页面的访问权限将被忽略。',
    'app_homepage_default' => '默认主页视图选择',
    'app_disable_comments' => '禁用评论',
    'app_disable_comments_desc' => '在App的所有页面上禁用评论，现有评论也不会显示出来。',

    /**
     * Registration settings
     */

    'reg_settings' => '注册设置',
    'reg_allow' => '允许注册？',
    'reg_default_role' => '注册后的默认用户角色',
    'reg_confirm_email' => '需要Email验证？',
    'reg_confirm_email_desc' => '如果使用域名限制，则需要Email验证，并且该值将被忽略。',
    'reg_confirm_restrict_domain' => '域名限制',
    'reg_confirm_restrict_domain_desc' => '输入您想要限制注册的Email域名列表，用逗号隔开。在被允许与应用程序交互之前，用户将被发送一封Email来确认他们的地址。<br>注意用户在注册成功后可以修改他们的Email地址。',
    'reg_confirm_restrict_domain_placeholder' => '尚未设置限制',

    /**
     * Role settings
     */

    'roles' => '角色',
    'role_user_roles' => '用户角色',
    'role_create' => '创建角色',
    'role_create_success' => '角色创建成功',
    'role_delete' => '删除角色',
    'role_delete_confirm' => '这将会删除名为 \':roleName\' 的角色.',
    'role_delete_users_assigned' => '有:userCount位用户属于此角色。如果您想将此角色中的用户迁移，请在下面选择一个新角色。',
    'role_delete_no_migration' => "不要迁移用户",
    'role_delete_sure' => '您确定要删除这个角色？',
    'role_delete_success' => '角色删除成功',
    'role_edit' => '编辑角色',
    'role_details' => '角色详细信息',
    'role_name' => '角色名',
    'role_desc' => '角色简述',
    'role_system' => '系统权限',
    'role_manage_users' => '管理用户',
    'role_manage_roles' => '管理角色与角色权限',
    'role_manage_entity_permissions' => '管理所有图书，章节和页面的权限',
    'role_manage_own_entity_permissions' => '管理自己的图书，章节和页面的权限',
    'role_manage_settings' => '管理App设置',
    'role_asset' => '资源许可',
    'role_asset_desc' => '对系统内资源的默认访问许可将由这些权限控制。单独设置在书籍，章节和页面上的权限将覆盖这里的权限设定。',
    'role_all' => '全部的',
    'role_own' => '拥有的',
    'role_controlled_by_asset' => '由其所在的资源来控制',
    'role_save' => '保存角色',
    'role_update_success' => '角色更新成功',
    'role_users' => '此角色的用户',
    'role_users_none' => '目前没有用户被分配到这个角色',

    /**
     * Users
     */

    'users' => '用户',
    'user_profile' => '用户资料',
    'users_add_new' => '添加用户',
    'users_search' => '搜索用户',
    'users_role' => '用户角色',
    'users_external_auth_id' => '外部身份认证ID',
    'users_password_warning' => '如果您想更改密码，请填写以下内容：',
    'users_system_public' => '此用户代表访问您的App的任何访客。它不能用于登录，而是自动分配。',
    'users_books_view_type' => '图书浏览布局偏好',
    'users_delete' => '删除用户',
    'users_delete_named' => '删除用户 :userName',
    'users_delete_warning' => '这将从系统中完全删除名为 \':userName\' 的用户。',
    'users_delete_confirm' => '您确定要删除这个用户？',
    'users_delete_success' => '用户删除成功。',
    'users_edit' => '编辑用户',
    'users_edit_profile' => '编辑资料',
    'users_edit_success' => '用户更新成功',
    'users_avatar' => '用户头像',
    'users_avatar_desc' => '当前图片应该为约256px的正方形。',
    'users_preferred_language' => '语言',
    'users_social_accounts' => '社交账户',
    'users_social_accounts_info' => '在这里，您可以绑定您的其他帐户，以便更快更轻松地登录。如果您选择解除绑定，之后将不能通过此社交账户登录，请设置社交账户来取消本App的访问权限。',
    'users_social_connect' => '绑定账户',
    'users_social_disconnect' => '解除绑定账户',
    'users_social_connected' => ':socialAccount 账户已经成功绑定到您的资料。',
    'users_social_disconnected' => ':socialAccount 账户已经成功解除绑定。',
];
