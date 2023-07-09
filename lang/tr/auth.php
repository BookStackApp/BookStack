<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Girdiğiniz bilgiler kayıtlarımızla uyuşmuyor.',
    'throttle' => 'Çok fazla giriş yapmaya çalıştınız. Lütfen :seconds saniye içinde tekrar deneyin.',

    // Login & Register
    'sign_up' => 'Kaydol',
    'log_in' => 'Giriş Yap',
    'log_in_with' => ':socialDriver ile giriş yap',
    'sign_up_with' => ':socialDriver ile kaydol',
    'logout' => 'Çıkış Yap',

    'name' => 'İsim',
    'username' => 'Kullanıcı Adı',
    'email' => 'E-posta',
    'password' => 'Şifre',
    'password_confirm' => 'Şifreyi Onaylayın',
    'password_hint' => 'En az 8 karakter olmalı',
    'forgot_password' => 'Şifrenizi mi unuttunuz?',
    'remember_me' => 'Beni Hatırla',
    'ldap_email_hint' => 'Bu hesap için kullanmak istediğiniz e-posta adresini giriniz.',
    'create_account' => 'Hesap Oluştur',
    'already_have_account' => 'Zaten bir hesabınız var mı?',
    'dont_have_account' => 'Hesabınız yok mu?',
    'social_login' => 'Diğer Servisler ile Giriş Yapın',
    'social_registration' => 'Diğer Servisler ile Kaydolun',
    'social_registration_text' => 'Başka bir servis aracılığıyla kaydolun ve giriş yapın.',

    'register_thanks' => 'Kaydolduğunuz için teşekkürler!',
    'register_confirm' => ':appName erişimi için lütfen e-posta adresinizi kontrol edin ve size gönderilen doğrulama bağlantısına tıklayın.',
    'registrations_disabled' => 'Kayıtlar devre dışı bırakılmıştır',
    'registration_email_domain_invalid' => 'Bu e-posta sağlayıcısının uygulamaya erişim izni bulunmuyor',
    'register_success' => 'Kaydolduğunuz için teşekkürler! Artık kayıtlı bir kullanıcı olarak giriş yaptınız.',

    // Login auto-initiation
    'auto_init_starting' => 'Oturum açılmaya çalışılıyor',
    'auto_init_starting_desc' => 'Oturum açma işlemini başlatmak için kimlik doğrulama sisteminizle iletişime geçiyoruz. Eğer 5 saniye sonra herhangi bir ilerleme olmazsa aşağıdaki bağlantıya tıklamayı deneyebilirsiniz.',
    'auto_init_start_link' => 'Kimlik doğrulama ile devam edin',

    // Password Reset
    'reset_password' => 'Şifreyi Sıfırla',
    'reset_password_send_instructions' => 'Aşağıya gireceğiniz e-posta adresine şifre sıfırlama bağlantısı gönderilecektir.',
    'reset_password_send_button' => 'Sıfırlama Bağlantısını Gönder',
    'reset_password_sent' => 'Şifre sıfırlama bağlantısı, :email adresinin sistemde bulunması durumunda e-posta olarak gönderilecektir.',
    'reset_password_success' => 'Şifreniz başarıyla sıfırlandı.',
    'email_reset_subject' => ':appName şifrenizi sıfırlayın',
    'email_reset_text' => 'Hesap şifrenizi sıfırlama isteğinde bulunduğunuz için bu e-postayı aldınız.',
    'email_reset_not_requested' => 'Şifre sıfırlama isteğinde bulunmadıysanız herhangi bir işlem yapmanıza gerek yoktur.',

    // Email Confirmation
    'email_confirm_subject' => ':appName için girdiğiniz e-posta adresini doğrulayın',
    'email_confirm_greeting' => ':appName uygulamasına katıldığınız için teşekkürler!',
    'email_confirm_text' => 'Lütfen aşağıdaki butona tıklayarak e-posta adresinizi doğrulayın:',
    'email_confirm_action' => 'E-posta Adresini Doğrula',
    'email_confirm_send_error' => 'E-posta adresinin doğrulanması gerekiyor fakat sistem, doğrulama bağlantısını göndermeyi başaramadı. E-posta adresinin doğru bir şekilde ayarlığından emin olmak için yöneticiyle iletişime geçin.',
    'email_confirm_success' => 'Email hesabınız onaylandı. Email adresinizi kullanarak giriş yapabilirsiniz.',
    'email_confirm_resent' => 'Doğrulama e-postası tekrar gönderildi, lütfen gelen kutunuzu kontrol ediniz.',
    'email_confirm_thanks' => 'Onayladığınız için teşekkürler!',
    'email_confirm_thanks_desc' => 'Lütfen onayınız işlenirken bir dakika bekleyin. Eğer 3 saniye sonra yönlendirilmediyseniz; devam etmek için aşağıdaki "Devam" linkine basınız.',

    'email_not_confirmed' => 'E-posta Adresi Doğrulanmadı',
    'email_not_confirmed_text' => 'E-posta adresiniz henüz doğrulanmadı.',
    'email_not_confirmed_click_link' => 'Lütfen kaydolduktan hemen sonra size gönderilen e-postadaki bağlantıya tıklayın.',
    'email_not_confirmed_resend' => 'Eğer e-postayı bulamıyorsanız, aşağıdaki formu doldurarak doğrulama e-postasının tekrar gönderilmesini sağlayabilirsiniz.',
    'email_not_confirmed_resend_button' => 'Doğrulama E-postasını Tekrar Gönder',

    // User Invite
    'user_invite_email_subject' => ':appName uygulamasına davet edildiniz!',
    'user_invite_email_greeting' => ':appName üzerinde sizin için bir hesap oluşturuldu.',
    'user_invite_email_text' => 'Hesap şifrenizi belirlemek ve hesabınıza erişim sağlayabilmek için aşağıdaki butona tıklayın:',
    'user_invite_email_action' => 'Hesap Şifresini Belirleyin',
    'user_invite_page_welcome' => ':appName uygulamasına hoş geldiniz!',
    'user_invite_page_text' => 'Hesap kurulumunuzu tamamlamak ve gelecekteki :appName ziyaretlerinizde hesabınıza erişim sağlayabilmeniz için bir şifre belirlemeniz gerekiyor.',
    'user_invite_page_confirm_button' => 'Şifreyi Onayla',
    'user_invite_success_login' => 'Şifre belirlendi, :appName! uygulamasına giriş yapmak için belirlediğiniz şifreyi kullanabilirsiniz',

    // Multi-factor Authentication
    'mfa_setup' => 'Çok Aşamalı Kimlik Doğrulama',
    'mfa_setup_desc' => 'Hesabınıza ekstra bir güvenlik katmanı daha eklemek için çok aşamalı kimlik doğrulamayı kurunuz.',
    'mfa_setup_configured' => 'Zaten yapılandırıldı',
    'mfa_setup_reconfigure' => 'Yeniden yapılandır',
    'mfa_setup_remove_confirmation' => '2 adımlı doğrulamayı kaldırmak istediğinize emin misiniz?',
    'mfa_setup_action' => 'Ayarlar',
    'mfa_backup_codes_usage_limit_warning' => 'Kalan yedekleme kodu sayınız 5\'ten az, hesabınızın kilitlenip kullanım dışı kalmaması için lütfen kodlarınız bitmeden yeni kod üretip saklayınız.',
    'mfa_option_totp_title' => 'Mobil Uygulama',
    'mfa_option_totp_desc' => 'Çok aşamalı kimlik doğrulamayı kullanabilmek için Google Authenticator, Authy veya Microsoft Authenticator gibi TOTP destekleyen bir mobil uygulamaya ihtiyacınız olacaktır.',
    'mfa_option_backup_codes_title' => 'Yedekleme Kodları',
    'mfa_option_backup_codes_desc' => 'Kimliğini doğrulamak için kullanabileceğin aşağıdaki tek kullanımlık yedek kodlarını güvenli bir yerde sakla.',
    'mfa_gen_confirm_and_enable' => 'Onayla ve aktive et',
    'mfa_gen_backup_codes_title' => 'Yedekleme Kodları Kurulumu',
    'mfa_gen_backup_codes_desc' => 'Aşağıdaki kod listesini güvenli bir yerde sakla. Sisteme giriş yaparken kodlardan birini ikinci bir kimlik doğrulama mekanizması olarak kullanabileceksin.',
    'mfa_gen_backup_codes_download' => 'İndirme Kodları',
    'mfa_gen_backup_codes_usage_warning' => 'Her kod tek seferlik kullanılabilir',
    'mfa_gen_totp_title' => 'Mobil Uygulama Kurulumu',
    'mfa_gen_totp_desc' => 'Çok aşamalı kimlik doğrulamayı kullanabilmek için Google Authenticator, Authy veya Microsoft Authenticator gibi TOTP destekleyen bir mobil uygulamaya ihtiyacınız olacaktır.',
    'mfa_gen_totp_scan' => 'Başlamak için aşağıdaki QR kodunu tercih ettiğin kimlik doğrulama uygulamasında tara.',
    'mfa_gen_totp_verify_setup' => 'Kurulumu Doğrula',
    'mfa_gen_totp_verify_setup_desc' => 'Aşağıdaki kutuya kimlik doğrulama uygulamanızda üretilmiş olan kodu girerek hepsini doğrulayabilirsiniz:',
    'mfa_gen_totp_provide_code_here' => 'Uygulamada üretilen kodunuzu buraya giriniz',
    'mfa_verify_access' => 'Girişi Doğrula',
    'mfa_verify_access_desc' => 'Giriş yapmadan önce ek güvenlik doğrulaması amacıyla kimliğinizin doğrulanması gerekmektedir. Aşağıda belirtilen yöntemlerden birini kullanarak devam ediniz.',
    'mfa_verify_no_methods' => 'Hiçbir Yöntem Ayarlanmadı',
    'mfa_verify_no_methods_desc' => 'Hesabınızda çok aşamalı kimlik doğrulama yöntemi bulunamadı. Giriş yapabilmek için en az bir tane yöntemi ayarlamanız gerekmektedir.',
    'mfa_verify_use_totp' => 'Mobil uygulama kullanarak doğrula',
    'mfa_verify_use_backup_codes' => 'Yedekleme kodu kullanarak doğrula',
    'mfa_verify_backup_code' => 'Yedekleme Kodu',
    'mfa_verify_backup_code_desc' => 'Kalan yedekleme kodlarınızdan birini giriniz:',
    'mfa_verify_backup_code_enter_here' => 'Yedekleme kodunuzu buraya giriniz',
    'mfa_verify_totp_desc' => 'Mobil uygulamada üretilmiş kodu aşağıya giriniz:',
    'mfa_setup_login_notification' => '2 adımlı doğrulama ayarlandı, Lütfen 2 adımlı doğrulama kullanarak yeniden giriş yapınız.',
];
