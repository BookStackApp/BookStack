<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'คุณไม่มีสิทธิ์เข้าถึงหน้าที่ร้องขอ',
    'permissionJson' => 'คุณไม่มีสิทธิ์ดำเนินการที่ร้องขอ',

    // Auth
    'error_user_exists_different_creds' => 'มีผู้ใช้ที่ใช้อีเมล :email อยู่แล้วแต่ใช้ข้อมูลประจำตัวต่างกัน',
    'auth_pre_register_theme_prevention' => 'ไม่สามารถลงทะเบียนบัญชีผู้ใช้สำหรับข้อมูลที่ให้มาได้',
    'email_already_confirmed' => 'ยืนยันอีเมลแล้ว กรุณาลองเข้าสู่ระบบ',
    'email_confirmation_invalid' => 'โทเค็นยืนยันนี้ไม่ถูกต้องหรือถูกใช้ไปแล้ว กรุณาลองลงทะเบียนใหม่',
    'email_confirmation_expired' => 'โทเค็นยืนยันหมดอายุแล้ว ส่งอีเมลยืนยันใหม่ให้แล้ว',
    'email_confirmation_awaiting' => 'ที่อยู่อีเมลของบัญชีที่ใช้งานอยู่ต้องได้รับการยืนยัน',
    'ldap_fail_anonymous' => 'การเข้าถึง LDAP ล้มเหลวโดยใช้การเชื่อมต่อแบบไม่ระบุตัวตน',
    'ldap_fail_authed' => 'การเข้าถึง LDAP ล้มเหลวโดยใช้ข้อมูล dn และรหัสผ่านที่กำหนด',
    'ldap_extension_not_installed' => 'ไม่ได้ติดตั้ง PHP extension สำหรับ LDAP',
    'ldap_cannot_connect' => 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ LDAP ได้ การเชื่อมต่อเริ่มต้นล้มเหลว',
    'saml_already_logged_in' => 'เข้าสู่ระบบแล้ว',
    'saml_no_email_address' => 'ไม่พบที่อยู่อีเมลสำหรับผู้ใช้นี้ในข้อมูลที่ระบบยืนยันตัวตนภายนอกส่งมา',
    'saml_invalid_response_id' => 'คำขอจากระบบยืนยันตัวตนภายนอกไม่ได้รับการยอมรับจากกระบวนการที่เริ่มต้นโดยแอปพลิเคชันนี้ การนำทางย้อนกลับหลังเข้าสู่ระบบอาจทำให้เกิดปัญหานี้',
    'saml_fail_authed' => 'การเข้าสู่ระบบด้วย :system ล้มเหลว ระบบไม่ได้ให้การอนุญาตที่สำเร็จ',
    'oidc_already_logged_in' => 'เข้าสู่ระบบแล้ว',
    'oidc_no_email_address' => 'ไม่พบที่อยู่อีเมลสำหรับผู้ใช้นี้ในข้อมูลที่ระบบยืนยันตัวตนภายนอกส่งมา',
    'oidc_fail_authed' => 'การเข้าสู่ระบบด้วย :system ล้มเหลว ระบบไม่ได้ให้การอนุญาตที่สำเร็จ',
    'social_no_action_defined' => 'ไม่ได้กำหนดการดำเนินการ',
    'social_login_bad_response' => "เกิดข้อผิดพลาดระหว่างเข้าสู่ระบบด้วย :socialAccount: \n:error",
    'social_account_in_use' => 'บัญชี :socialAccount นี้ถูกใช้งานแล้ว ลองเข้าสู่ระบบผ่านตัวเลือก :socialAccount',
    'social_account_email_in_use' => 'อีเมล :email ถูกใช้งานแล้ว หากมีบัญชีอยู่แล้ว คุณสามารถเชื่อมต่อบัญชี :socialAccount จากการตั้งค่าโปรไฟล์ได้',
    'social_account_existing' => ':socialAccount นี้เชื่อมต่อกับโปรไฟล์ของคุณแล้ว',
    'social_account_already_used_existing' => 'บัญชี :socialAccount นี้ถูกใช้งานโดยผู้ใช้อื่นแล้ว',
    'social_account_not_used' => 'บัญชี :socialAccount นี้ไม่ได้เชื่อมต่อกับผู้ใช้ใด กรุณาแนบในการตั้งค่าโปรไฟล์',
    'social_account_register_instructions' => 'หากยังไม่มีบัญชี คุณสามารถลงทะเบียนโดยใช้ตัวเลือก :socialAccount',
    'social_driver_not_found' => 'ไม่พบ Social driver',
    'social_driver_not_configured' => 'การตั้งค่า Social ของ :socialAccount ไม่ถูกต้อง',
    'invite_token_expired' => 'ลิงก์เชิญนี้หมดอายุแล้ว คุณสามารถลองรีเซ็ตรหัสผ่านบัญชีแทนได้',
    'login_user_not_found' => 'ไม่พบผู้ใช้สำหรับการดำเนินการนี้',

    // System
    'path_not_writable' => 'ไม่สามารถอัปโหลดไปยังพาธ :filePath ได้ โปรดตรวจสอบว่าเซิร์ฟเวอร์มีสิทธิ์เขียนได้',
    'cannot_get_image_from_url' => 'ไม่สามารถดึงรูปภาพจาก :url ได้',
    'cannot_create_thumbs' => 'เซิร์ฟเวอร์ไม่สามารถสร้างภาพย่อได้ โปรดตรวจสอบว่าติดตั้ง PHP extension GD แล้ว',
    'server_upload_limit' => 'เซิร์ฟเวอร์ไม่อนุญาตให้อัปโหลดไฟล์ขนาดนี้ กรุณาลองใช้ไฟล์ขนาดเล็กกว่า',
    'server_post_limit' => 'เซิร์ฟเวอร์ไม่สามารถรับข้อมูลในปริมาณที่กำหนดได้ ลองใหม่ด้วยข้อมูลน้อยลงหรือไฟล์ขนาดเล็กกว่า',
    'uploaded'  => 'เซิร์ฟเวอร์ไม่อนุญาตให้อัปโหลดไฟล์ขนาดนี้ กรุณาลองใช้ไฟล์ขนาดเล็กกว่า',

    // Drawing & Images
    'image_upload_error' => 'เกิดข้อผิดพลาดขณะอัปโหลดรูปภาพ',
    'image_upload_type_error' => 'ประเภทรูปภาพที่อัปโหลดไม่ถูกต้อง',
    'image_upload_replace_type' => 'การแทนที่ไฟล์รูปภาพต้องใช้ประเภทเดียวกัน',
    'image_upload_memory_limit' => 'ไม่สามารถจัดการการอัปโหลดรูปภาพและ/หรือสร้างภาพย่อได้เนื่องจากทรัพยากรระบบไม่เพียงพอ',
    'image_thumbnail_memory_limit' => 'ไม่สามารถสร้างขนาดรูปภาพต่างๆ ได้เนื่องจากทรัพยากรระบบไม่เพียงพอ',
    'image_gallery_thumbnail_memory_limit' => 'ไม่สามารถสร้างภาพย่อแกลเลอรีได้เนื่องจากทรัพยากรระบบไม่เพียงพอ',
    'drawing_data_not_found' => 'ไม่สามารถโหลดข้อมูลภาพวาดได้ ไฟล์ภาพวาดอาจไม่มีอยู่แล้วหรือคุณไม่มีสิทธิ์เข้าถึง',

    // Attachments
    'attachment_not_found' => 'ไม่พบไฟล์แนบ',
    'attachment_upload_error' => 'เกิดข้อผิดพลาดขณะอัปโหลดไฟล์แนบ',

    // Pages
    'page_draft_autosave_fail' => 'บันทึกร่างล้มเหลว โปรดตรวจสอบการเชื่อมต่ออินเทอร์เน็ตก่อนบันทึกหน้านี้',
    'page_draft_delete_fail' => 'ลบร่างหน้าและดึงเนื้อหาที่บันทึกปัจจุบันล้มเหลว',
    'page_custom_home_deletion' => 'ไม่สามารถลบหน้าได้ในขณะที่ตั้งเป็นหน้าแรก',

    // Entities
    'entity_not_found' => 'ไม่พบรายการ',
    'bookshelf_not_found' => 'ไม่พบชั้นวาง',
    'book_not_found' => 'ไม่พบหนังสือ',
    'page_not_found' => 'ไม่พบหน้า',
    'chapter_not_found' => 'ไม่พบบท',
    'selected_book_not_found' => 'ไม่พบหนังสือที่เลือก',
    'selected_book_chapter_not_found' => 'ไม่พบหนังสือหรือบทที่เลือก',
    'guests_cannot_save_drafts' => 'ผู้เยี่ยมชมไม่สามารถบันทึกร่างได้',

    // Users
    'users_cannot_delete_only_admin' => 'ไม่สามารถลบผู้ดูแลระบบคนเดียวได้',
    'users_cannot_delete_guest' => 'ไม่สามารถลบผู้ใช้แบบผู้เยี่ยมชมได้',
    'users_could_not_send_invite' => 'ไม่สามารถสร้างผู้ใช้ได้เนื่องจากส่งอีเมลเชิญล้มเหลว',

    // Roles
    'role_cannot_be_edited' => 'บทบาทนี้ไม่สามารถแก้ไขได้',
    'role_system_cannot_be_deleted' => 'บทบาทนี้เป็นบทบาทระบบและไม่สามารถลบได้',
    'role_registration_default_cannot_delete' => 'บทบาทนี้ไม่สามารถลบได้ในขณะที่ตั้งเป็นบทบาทลงทะเบียนเริ่มต้น',
    'role_cannot_remove_only_admin' => 'ผู้ใช้นี้เป็นผู้ใช้คนเดียวที่ได้รับบทบาทผู้ดูแลระบบ กรุณากำหนดบทบาทผู้ดูแลระบบให้ผู้ใช้อื่นก่อนที่จะลบออกที่นี่',

    // Comments
    'comment_list' => 'เกิดข้อผิดพลาดขณะดึงความคิดเห็น',
    'cannot_add_comment_to_draft' => 'ไม่สามารถเพิ่มความคิดเห็นในร่างได้',
    'comment_add' => 'เกิดข้อผิดพลาดขณะเพิ่ม/อัปเดตความคิดเห็น',
    'comment_delete' => 'เกิดข้อผิดพลาดขณะลบความคิดเห็น',
    'empty_comment' => 'ไม่สามารถเพิ่มความคิดเห็นที่ว่างเปล่าได้',

    // Error pages
    '404_page_not_found' => 'ไม่พบหน้า',
    'sorry_page_not_found' => 'ขออภัย ไม่พบหน้าที่คุณกำลังมองหา',
    'sorry_page_not_found_permission_warning' => 'หากคุณคาดว่าหน้านี้มีอยู่ คุณอาจไม่มีสิทธิ์ดูหน้านี้',
    'image_not_found' => 'ไม่พบรูปภาพ',
    'image_not_found_subtitle' => 'ขออภัย ไม่พบไฟล์รูปภาพที่คุณกำลังมองหา',
    'image_not_found_details' => 'หากคุณคาดว่ารูปภาพนี้มีอยู่ อาจถูกลบไปแล้ว',
    'return_home' => 'กลับไปหน้าแรก',
    'error_occurred' => 'เกิดข้อผิดพลาด',
    'app_down' => ':appName ไม่พร้อมใช้งานในขณะนี้',
    'back_soon' => 'จะกลับมาให้บริการเร็วๆ นี้',

    // Import
    'import_zip_cant_read' => 'ไม่สามารถอ่านไฟล์ ZIP ได้',
    'import_zip_cant_decode_data' => 'ไม่สามารถค้นหาและถอดรหัสเนื้อหา data.json ใน ZIP ได้',
    'import_zip_no_data' => 'ข้อมูลในไฟล์ ZIP ไม่มีเนื้อหาหนังสือ บท หรือหน้าที่คาดไว้',
    'import_zip_data_too_large' => 'เนื้อหา data.json ใน ZIP เกินขนาดอัปโหลดสูงสุดที่กำหนดในแอปพลิเคชัน',
    'import_validation_failed' => 'ตรวจสอบ ZIP นำเข้าล้มเหลวพร้อมข้อผิดพลาด:',
    'import_zip_failed_notification' => 'นำเข้าไฟล์ ZIP ล้มเหลว',
    'import_perms_books' => 'คุณขาดสิทธิ์ที่จำเป็นในการสร้างหนังสือ',
    'import_perms_chapters' => 'คุณขาดสิทธิ์ที่จำเป็นในการสร้างบท',
    'import_perms_pages' => 'คุณขาดสิทธิ์ที่จำเป็นในการสร้างหน้า',
    'import_perms_images' => 'คุณขาดสิทธิ์ที่จำเป็นในการสร้างรูปภาพ',
    'import_perms_attachments' => 'คุณขาดสิทธิ์ที่จำเป็นในการสร้างไฟล์แนบ',

    // API errors
    'api_no_authorization_found' => 'ไม่พบโทเค็นการอนุญาตในคำขอ',
    'api_bad_authorization_format' => 'พบโทเค็นการอนุญาตในคำขอแต่รูปแบบดูเหมือนไม่ถูกต้อง',
    'api_user_token_not_found' => 'ไม่พบ API token ที่ตรงกับโทเค็นการอนุญาตที่ให้มา',
    'api_incorrect_token_secret' => 'รหัสลับที่ให้มาสำหรับ API token ที่ใช้ไม่ถูกต้อง',
    'api_user_no_api_permission' => 'เจ้าของ API token ที่ใช้ไม่มีสิทธิ์เรียกใช้ API',
    'api_user_token_expired' => 'โทเค็นการอนุญาตที่ใช้หมดอายุแล้ว',
    'api_cookie_auth_only_get' => 'อนุญาตเฉพาะคำขอ GET เมื่อใช้ API ด้วยการยืนยันตัวตนแบบ cookie',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'เกิดข้อผิดพลาดขณะส่งอีเมลทดสอบ:',

    // HTTP errors
    'http_ssr_url_no_match' => 'URL ไม่ตรงกับโฮสต์ SSR ที่อนุญาตที่กำหนดค่าไว้',
];
