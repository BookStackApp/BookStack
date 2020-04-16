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
    'email_confirm_success' => 'E-posta adresiniz doğrulandı!',
    'email_confirm_resent' => 'Doğrulama e-postası tekrar gönderildi, lütfen gelen kutunuzu kontrol ediniz.',

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
    'user_invite_success' => 'Şifreniz ayarlandı, artık :appName uygulamasına giriş yapabilirsiniz!'
];