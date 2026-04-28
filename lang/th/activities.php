<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'สร้างหน้า',
    'page_create_notification'    => 'สร้างหน้าสำเร็จแล้ว',
    'page_update'                 => 'แก้ไขหน้า',
    'page_update_notification'    => 'แก้ไขหน้าสำเร็จแล้ว',
    'page_delete'                 => 'ลบหน้า',
    'page_delete_notification'    => 'ลบหน้าสำเร็จแล้ว',
    'page_restore'                => 'กู้คืนหน้า',
    'page_restore_notification'   => 'กู้คืนหน้าสำเร็จแล้ว',
    'page_move'                   => 'ย้ายหน้า',
    'page_move_notification'      => 'ย้ายหน้าสำเร็จแล้ว',

    // Chapters
    'chapter_create'              => 'สร้างบท',
    'chapter_create_notification' => 'สร้างบทสำเร็จแล้ว',
    'chapter_update'              => 'แก้ไขบท',
    'chapter_update_notification' => 'แก้ไขบทสำเร็จแล้ว',
    'chapter_delete'              => 'ลบบท',
    'chapter_delete_notification' => 'ลบบทสำเร็จแล้ว',
    'chapter_move'                => 'ย้ายบท',
    'chapter_move_notification' => 'ย้ายบทสำเร็จแล้ว',

    // Books
    'book_create'                 => 'สร้างหนังสือ',
    'book_create_notification'    => 'สร้างหนังสือสำเร็จแล้ว',
    'book_create_from_chapter'              => 'แปลงบทเป็นหนังสือ',
    'book_create_from_chapter_notification' => 'แปลงบทเป็นหนังสือสำเร็จแล้ว',
    'book_update'                 => 'แก้ไขหนังสือ',
    'book_update_notification'    => 'แก้ไขหนังสือสำเร็จแล้ว',
    'book_delete'                 => 'ลบหนังสือ',
    'book_delete_notification'    => 'ลบหนังสือสำเร็จแล้ว',
    'book_sort'                   => 'จัดเรียงหนังสือ',
    'book_sort_notification'      => 'จัดเรียงหนังสือสำเร็จแล้ว',

    // Bookshelves
    'bookshelf_create'            => 'สร้างชั้นวาง',
    'bookshelf_create_notification'    => 'สร้างชั้นวางสำเร็จแล้ว',
    'bookshelf_create_from_book'    => 'แปลงหนังสือเป็นชั้นวาง',
    'bookshelf_create_from_book_notification'    => 'แปลงหนังสือเป็นชั้นวางสำเร็จแล้ว',
    'bookshelf_update'                 => 'แก้ไขชั้นวาง',
    'bookshelf_update_notification'    => 'แก้ไขชั้นวางสำเร็จแล้ว',
    'bookshelf_delete'                 => 'ลบชั้นวาง',
    'bookshelf_delete_notification'    => 'ลบชั้นวางสำเร็จแล้ว',

    // Revisions
    'revision_restore' => 'กู้คืนการแก้ไข',
    'revision_delete' => 'ลบการแก้ไข',
    'revision_delete_notification' => 'ลบการแก้ไขสำเร็จแล้ว',

    // Favourites
    'favourite_add_notification' => 'เพิ่ม ":name" ในรายการโปรดแล้ว',
    'favourite_remove_notification' => 'นำ ":name" ออกจากรายการโปรดแล้ว',

    // Watching
    'watch_update_level_notification' => 'อัปเดตการตั้งค่าการติดตามสำเร็จแล้ว',

    // Auth
    'auth_login' => 'เข้าสู่ระบบ',
    'auth_register' => 'ลงทะเบียนเป็นผู้ใช้ใหม่',
    'auth_password_reset_request' => 'ขอรีเซ็ตรหัสผ่าน',
    'auth_password_reset_update' => 'รีเซ็ตรหัสผ่านแล้ว',
    'mfa_setup_method' => 'ตั้งค่าวิธียืนยันตัวตน MFA',
    'mfa_setup_method_notification' => 'ตั้งค่าการยืนยันตัวตนแบบหลายขั้นตอนสำเร็จแล้ว',
    'mfa_remove_method' => 'ลบวิธียืนยันตัวตน MFA',
    'mfa_remove_method_notification' => 'ลบการยืนยันตัวตนแบบหลายขั้นตอนสำเร็จแล้ว',

    // Settings
    'settings_update' => 'แก้ไขการตั้งค่า',
    'settings_update_notification' => 'แก้ไขการตั้งค่าสำเร็จแล้ว',
    'maintenance_action_run' => 'ดำเนินการบำรุงรักษาระบบ',

    // Webhooks
    'webhook_create' => 'สร้าง Webhook',
    'webhook_create_notification' => 'สร้าง Webhook สำเร็จแล้ว',
    'webhook_update' => 'แก้ไข Webhook',
    'webhook_update_notification' => 'แก้ไข Webhook สำเร็จแล้ว',
    'webhook_delete' => 'ลบ Webhook',
    'webhook_delete_notification' => 'ลบ Webhook สำเร็จแล้ว',

    // Imports
    'import_create' => 'สร้างการนำเข้า',
    'import_create_notification' => 'อัปโหลดไฟล์นำเข้าสำเร็จแล้ว',
    'import_run' => 'ดำเนินการนำเข้า',
    'import_run_notification' => 'นำเข้าเนื้อหาสำเร็จแล้ว',
    'import_delete' => 'ลบการนำเข้า',
    'import_delete_notification' => 'ลบการนำเข้าสำเร็จแล้ว',

    // Users
    'user_create' => 'สร้างผู้ใช้',
    'user_create_notification' => 'สร้างผู้ใช้สำเร็จแล้ว',
    'user_update' => 'แก้ไขผู้ใช้',
    'user_update_notification' => 'แก้ไขผู้ใช้สำเร็จแล้ว',
    'user_delete' => 'ลบผู้ใช้',
    'user_delete_notification' => 'ลบผู้ใช้สำเร็จแล้ว',

    // API Tokens
    'api_token_create' => 'สร้าง API Token',
    'api_token_create_notification' => 'สร้าง API Token สำเร็จแล้ว',
    'api_token_update' => 'แก้ไข API Token',
    'api_token_update_notification' => 'แก้ไข API Token สำเร็จแล้ว',
    'api_token_delete' => 'ลบ API Token',
    'api_token_delete_notification' => 'ลบ API Token สำเร็จแล้ว',

    // Roles
    'role_create' => 'สร้างบทบาท',
    'role_create_notification' => 'สร้างบทบาทสำเร็จแล้ว',
    'role_update' => 'แก้ไขบทบาท',
    'role_update_notification' => 'แก้ไขบทบาทสำเร็จแล้ว',
    'role_delete' => 'ลบบทบาท',
    'role_delete_notification' => 'ลบบทบาทสำเร็จแล้ว',

    // Recycle Bin
    'recycle_bin_empty' => 'ล้างถังรีไซเคิล',
    'recycle_bin_restore' => 'กู้คืนจากถังรีไซเคิล',
    'recycle_bin_destroy' => 'ลบถาวรจากถังรีไซเคิล',

    // Comments
    'commented_on'                => 'แสดงความคิดเห็นใน',
    'comment_create'              => 'เพิ่มความคิดเห็น',
    'comment_update'              => 'แก้ไขความคิดเห็น',
    'comment_delete'              => 'ลบความคิดเห็น',

    // Sort Rules
    'sort_rule_create' => 'สร้างกฎการจัดเรียง',
    'sort_rule_create_notification' => 'สร้างกฎการจัดเรียงสำเร็จแล้ว',
    'sort_rule_update' => 'แก้ไขกฎการจัดเรียง',
    'sort_rule_update_notification' => 'แก้ไขกฎการจัดเรียงสำเร็จแล้ว',
    'sort_rule_delete' => 'ลบกฎการจัดเรียง',
    'sort_rule_delete_notification' => 'ลบกฎการจัดเรียงสำเร็จแล้ว',

    // Other
    'permissions_update'          => 'แก้ไขสิทธิ์',
];
