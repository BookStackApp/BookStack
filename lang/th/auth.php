<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'ข้อมูลประจำตัวไม่ตรงกับที่มีในระบบ',
    'throttle' => 'เข้าสู่ระบบล้มเหลวหลายครั้งเกินไป กรุณาลองใหม่ในอีก :seconds วินาที',

    // Login & Register
    'sign_up' => 'สมัครสมาชิก',
    'log_in' => 'เข้าสู่ระบบ',
    'log_in_with' => 'เข้าสู่ระบบด้วย :socialDriver',
    'sign_up_with' => 'สมัครสมาชิกด้วย :socialDriver',
    'logout' => 'ออกจากระบบ',

    'name' => 'ชื่อ',
    'username' => 'ชื่อผู้ใช้',
    'email' => 'อีเมล',
    'password' => 'รหัสผ่าน',
    'password_confirm' => 'ยืนยันรหัสผ่าน',
    'password_hint' => 'ต้องมีอย่างน้อย 8 ตัวอักษร',
    'forgot_password' => 'ลืมรหัสผ่าน?',
    'remember_me' => 'จดจำฉัน',
    'ldap_email_hint' => 'กรุณากรอกอีเมลที่จะใช้กับบัญชีนี้',
    'create_account' => 'สร้างบัญชี',
    'already_have_account' => 'มีบัญชีอยู่แล้ว?',
    'dont_have_account' => 'ยังไม่มีบัญชี?',
    'social_login' => 'เข้าสู่ระบบด้วย Social',
    'social_registration' => 'ลงทะเบียนด้วย Social',
    'social_registration_text' => 'ลงทะเบียนและเข้าสู่ระบบด้วยบริการอื่น',

    'register_thanks' => 'ขอบคุณที่ลงทะเบียน กรุณายืนยันอีเมลเพื่อเข้าสู่ระบบ',
    'register_confirm' => 'ยืนยันและลงทะเบียน',
    'registrations_disabled' => 'ขณะนี้ปิดรับการลงทะเบียน',
    'registration_email_domain_invalid' => 'โดเมนอีเมลนี้ไม่มีสิทธิ์เข้าถึงระบบ',
    'register_success' => 'ขอบคุณที่สมัครสมาชิก คุณได้ลงทะเบียนและเข้าสู่ระบบแล้ว',

    // Login auto-initiation
    'auto_init_starting' => 'กำลังเข้าสู่ระบบ',
    'auto_init_starting_desc' => 'กำลังติดต่อระบบยืนยันตัวตนเพื่อเริ่มกระบวนการเข้าสู่ระบบ หากไม่มีความคืบหน้าภายใน 5 วินาที กรุณาคลิกลิงก์ด้านล่าง',
    'auto_init_start_link' => 'ดำเนินการยืนยันตัวตน',

    // Password Reset
    'reset_password' => 'รีเซ็ตรหัสผ่าน',
    'reset_password_send_instructions' => 'กรอกอีเมลด้านล่าง ระบบจะส่งลิงก์รีเซ็ตรหัสผ่านให้คุณ',
    'reset_password_send_button' => 'ส่งลิงก์รีเซ็ตรหัสผ่าน',
    'reset_password_sent' => 'หากพบอีเมล :email ในระบบ จะมีลิงก์รีเซ็ตรหัสผ่านส่งไปให้',
    'reset_password_success' => 'รีเซ็ตรหัสผ่านสำเร็จแล้ว',
    'email_reset_subject' => 'รีเซ็ตรหัสผ่าน :appName ของคุณ',
    'email_reset_text' => 'คุณได้รับอีเมลนี้เพราะมีการขอรีเซ็ตรหัสผ่านสำหรับบัญชีของคุณ',
    'email_reset_not_requested' => 'หากคุณไม่ได้ขอรีเซ็ตรหัสผ่าน ไม่ต้องดำเนินการใดๆ เพิ่มเติม',

    // Email Confirmation
    'email_confirm_subject' => 'ยืนยันอีเมลของคุณบน :appName',
    'email_confirm_greeting' => 'ขอบคุณที่เข้าร่วม :appName!',
    'email_confirm_text' => 'กรุณายืนยันอีเมลของคุณโดยคลิกปุ่มด้านล่าง:',
    'email_confirm_action' => 'ยืนยันอีเมล',
    'email_confirm_send_error' => 'จำเป็นต้องยืนยันอีเมล แต่ระบบไม่สามารถส่งอีเมลได้ กรุณาติดต่อผู้ดูแลระบบเพื่อตรวจสอบการตั้งค่าอีเมล',
    'email_confirm_success' => 'ยืนยันอีเมลสำเร็จแล้ว สามารถเข้าสู่ระบบได้',
    'email_confirm_resent' => 'ส่งอีเมลยืนยันใหม่แล้ว กรุณาตรวจสอบกล่องจดหมาย',
    'email_confirm_thanks' => 'ขอบคุณที่ยืนยัน!',
    'email_confirm_thanks_desc' => 'กรุณารอสักครู่ขณะที่ระบบดำเนินการยืนยัน หากไม่ถูกเปลี่ยนหน้าภายใน 3 วินาที กรุณาคลิกลิงก์ "ดำเนินการต่อ" ด้านล่าง',

    'email_not_confirmed' => 'ยังไม่ได้ยืนยันอีเมล',
    'email_not_confirmed_text' => 'อีเมลของคุณยังไม่ได้รับการยืนยัน',
    'email_not_confirmed_click_link' => 'กรุณาคลิกลิงก์ในอีเมลที่ส่งให้คุณหลังจากลงทะเบียน',
    'email_not_confirmed_resend' => 'หากไม่พบอีเมล คุณสามารถส่งอีเมลยืนยันอีกครั้งโดยกรอกแบบฟอร์มด้านล่าง',
    'email_not_confirmed_resend_button' => 'ส่งอีเมลยืนยันอีกครั้ง',

    // User Invite
    'user_invite_email_subject' => 'คุณได้รับเชิญให้เข้าร่วม :appName!',
    'user_invite_email_greeting' => 'มีการสร้างบัญชีให้คุณบน :appName',
    'user_invite_email_text' => 'คลิกปุ่มด้านล่างเพื่อตั้งรหัสผ่านและเข้าใช้งาน:',
    'user_invite_email_action' => 'ตั้งรหัสผ่านบัญชี',
    'user_invite_page_welcome' => 'ยินดีต้อนรับสู่ :appName!',
    'user_invite_page_text' => 'เพื่อเสร็จสิ้นการสร้างบัญชีและเข้าใช้งาน คุณต้องตั้งรหัสผ่านสำหรับเข้าสู่ :appName ในครั้งถัดไป',
    'user_invite_page_confirm_button' => 'ยืนยันรหัสผ่าน',
    'user_invite_success_login' => 'ตั้งรหัสผ่านแล้ว คุณสามารถเข้าสู่ระบบ :appName ด้วยรหัสผ่านที่ตั้งไว้ได้แล้ว!',

    // Multi-factor Authentication
    'mfa_setup' => 'ตั้งค่าการยืนยันตัวตนแบบหลายขั้นตอน',
    'mfa_setup_desc' => 'ตั้งค่า MFA เพื่อเพิ่มความปลอดภัยให้บัญชีของคุณ',
    'mfa_setup_configured' => 'ตั้งค่าแล้ว',
    'mfa_setup_reconfigure' => 'ตั้งค่าใหม่',
    'mfa_setup_remove_confirmation' => 'คุณแน่ใจหรือไม่ว่าต้องการลบวิธียืนยันตัวตนแบบหลายขั้นตอนนี้?',
    'mfa_setup_action' => 'ตั้งค่า',
    'mfa_backup_codes_usage_limit_warning' => 'คุณมีรหัสสำรองเหลือน้อยกว่า 5 รหัส กรุณาสร้างและบันทึกชุดใหม่ก่อนหมด เพื่อป้องกันการถูกล็อกออกจากบัญชี',
    'mfa_option_totp_title' => 'แอป Authenticator',
    'mfa_option_totp_desc' => 'ในการใช้การยืนยันตัวตนแบบหลายขั้นตอน คุณต้องมีแอปพลิเคชันมือถือที่รองรับ TOTP เช่น Google Authenticator, Authy หรือ Microsoft Authenticator',
    'mfa_option_backup_codes_title' => 'รหัสสำรอง',
    'mfa_option_backup_codes_desc' => 'สร้างชุดรหัสสำรองแบบใช้ครั้งเดียว ซึ่งจะใช้กรอกเมื่อเข้าสู่ระบบเพื่อยืนยันตัวตน กรุณาเก็บรักษาไว้ในที่ปลอดภัย',
    'mfa_gen_confirm_and_enable' => 'ยืนยันและเปิดใช้งาน',
    'mfa_gen_backup_codes_title' => 'ตั้งค่ารหัสสำรอง',
    'mfa_gen_backup_codes_desc' => 'บันทึกรายการรหัสด้านล่างไว้ในที่ปลอดภัย เมื่อเข้าระบบคุณสามารถใช้รหัสเหล่านี้เป็นการยืนยันตัวตนขั้นที่สองได้',
    'mfa_gen_backup_codes_download' => 'ดาวน์โหลดรหัส',
    'mfa_gen_backup_codes_usage_warning' => 'รหัสแต่ละรหัสใช้ได้เพียงครั้งเดียว',
    'mfa_gen_totp_title' => 'ตั้งค่าแอปมือถือ',
    'mfa_gen_totp_desc' => 'ในการใช้การยืนยันตัวตนแบบหลายขั้นตอน คุณต้องมีแอปพลิเคชันมือถือที่รองรับ TOTP เช่น Google Authenticator, Authy หรือ Microsoft Authenticator',
    'mfa_gen_totp_scan' => 'สแกน QR code ด้านล่างด้วยแอป Authenticator ที่คุณต้องการใช้',
    'mfa_gen_totp_verify_setup' => 'ยืนยันการตั้งค่า',
    'mfa_gen_totp_verify_setup_desc' => 'ยืนยันว่าทุกอย่างทำงานได้โดยกรอกรหัสที่สร้างจากแอป Authenticator ในช่องด้านล่าง:',
    'mfa_gen_totp_provide_code_here' => 'กรอกรหัสที่สร้างจากแอปของคุณที่นี่',
    'mfa_verify_access' => 'ยืนยันการเข้าถึง',
    'mfa_verify_access_desc' => 'บัญชีของคุณต้องยืนยันตัวตนผ่านการตรวจสอบเพิ่มเติมก่อนเข้าใช้งาน กรุณายืนยันด้วยวิธีที่ตั้งค่าไว้เพื่อดำเนินการต่อ',
    'mfa_verify_no_methods' => 'ยังไม่ได้ตั้งค่าวิธียืนยันตัวตน',
    'mfa_verify_no_methods_desc' => 'ไม่พบวิธียืนยันตัวตนแบบหลายขั้นตอนสำหรับบัญชีของคุณ กรุณาตั้งค่าอย่างน้อยหนึ่งวิธีก่อนเข้าใช้งาน',
    'mfa_verify_use_totp' => 'ยืนยันด้วยแอปมือถือ',
    'mfa_verify_use_backup_codes' => 'ยืนยันด้วยรหัสสำรอง',
    'mfa_verify_backup_code' => 'รหัสสำรอง',
    'mfa_verify_backup_code_desc' => 'กรอกรหัสสำรองที่เหลืออยู่ของคุณด้านล่าง:',
    'mfa_verify_backup_code_enter_here' => 'กรอกรหัสสำรองที่นี่',
    'mfa_verify_totp_desc' => 'กรอกรหัสที่สร้างจากแอปมือถือของคุณด้านล่าง:',
    'mfa_setup_login_notification' => 'ตั้งค่าวิธียืนยันตัวตนแล้ว กรุณาเข้าสู่ระบบอีกครั้งด้วยวิธีที่ตั้งค่าไว้',
];
