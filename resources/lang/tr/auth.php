<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Bu kimlik bilgileri kayıtlarımızla eşleşmiyor.',
    'throttle' => 'Çok fazla giriş yapma denemesi var. Lütfen :seconds saniye sonra tekrar deneyiniz.',

    // Login & Register
    'sign_up' => 'Kaydol',
    'log_in' => 'Giriş',
    'log_in_with' => ':socialDriver ile giriş',
    'sign_up_with' => ':socialDriver ile kaydol',
    'logout' => 'Çıkış',

    'name' => 'İsim',
    'username' => 'Kulanıcı adı',
    'email' => 'E-Posta',
    'password' => 'Şifre',
    'password_confirm' => 'Şifre Onayla',
    'password_hint' => '7 karakterden fazla olmalı',
    'forgot_password' => 'Şifreni mi Unuttun?',
    'remember_me' => 'Beni Hatırla',
    'ldap_email_hint' => 'Lütfen bu hesap için bir e-posta belirleyiniz.',
    'create_account' => 'Hesap Oluştur',
    'already_have_account' => 'Zaten Hesabın Var mı?',
    'dont_have_account' => 'Hesabın yok mu?',
    'social_login' => 'Sosyal ile giriş',
    'social_registration' => 'Sosyal ile kayıt',
    'social_registration_text' => 'Kayıt olun ve başka bir servis kullanarak oturum açın.',

    'register_thanks' => 'Kayıt olduğunuz için teşekkürler!',
    'register_confirm' => 'Lütfen e-postanızı kontrol edin ve erişmek için :appName onay düğmesini tıklayın.',
    'registrations_disabled' => 'Şu anda kayıtlar kapalıdır',
    'registration_email_domain_invalid' => 'Bu e-posta etki alanının bu uygulamaya erişimi yok',
    'register_success' => 'Üye olduğunuz için teşekkürler! Şimdi kayıt oldunuz ve giriş yaptınız.',


    // Password Reset
    'reset_password' => 'Şifeyi Sıfırla',
    'reset_password_send_instructions' => 'Aşağıya e-posta adresinizi girin, size şifre sıfırlama bağlantısı olan bir e-posta gönderilecektir.',
    'reset_password_send_button' => 'Sıfırlama Linki Gönder',
    'reset_password_sent_success' => ':email adresine bir şifre sıfırlama bağlantısı gönderildi.',
    'reset_password_success' => 'Şifreniz başarıyla sıfırlandı.',
    'email_reset_subject' => ':appName şifreni sıfırla',
    'email_reset_text' => 'Bu e-postayı, hesabınız için bir şifre sıfırlama isteği aldığımız için alıyorsunuz.',
    'email_reset_not_requested' => 'Parola sıfırlama isteğinde bulunmadıysanız, başka bir işlem yapmanız gerekmez.',


    // Email Confirmation
    'email_confirm_subject' => ':appName üzerinde e-postanızı onaylayın',
    'email_confirm_greeting' => ':appName sitemize katıldığınız için teşekkür ederiz!',
    'email_confirm_text' => 'Lütfen aşağıdaki butona tıklayarak e-posta adresinizi onaylayın:',
    'email_confirm_action' => 'E-Posta Onayla',
    'email_confirm_send_error' => 'E-posta onayı gerekli ancak sistem e-postayı gönderemedi. E-postanın doğru ayarlandığından emin olmak için yönetici ile irtibata geçin.',
    'email_confirm_success' => 'E-posta teyit edilmiştir!',
    'email_confirm_resent' => 'Onay e-postası yeniden gönderildi, Lütfen gelen kutunuzu kontrol edin.',

    'email_not_confirmed' => 'E-posta Adresi Onaylanmadı',
    'email_not_confirmed_text' => 'E-posta adresin henüz onaylanmadı.',
    'email_not_confirmed_click_link' => 'Lütfen kayıt olduktan kısa bir süre sonra gönderilen e-postadaki bağlantıya tıklayın.',
    'email_not_confirmed_resend' => 'Eğer size gönderilen e-postayı bulamazsanız, aşağıdaki formu göndererek onay e-postasını tekrar gönderebilirsiniz.',
    'email_not_confirmed_resend_button' => 'Doğrulama e-postasnı tekrar gönder',

    // User Invite
    'user_invite_email_subject' => ':appName ailesine katılmaya davet edildiniz!',
    'user_invite_email_greeting' => ':appName üzerinde sizin için bir hesap oluşturuldu.',
    'user_invite_email_text' => 'Bir hesap şifresi belirlemek ve erişim kazanmak için aşağıdaki butona tıklayın:',
    'user_invite_email_action' => 'Hesap şifresi belirle',
    'user_invite_page_welcome' => ':appName ailesine hoş geldiniz!',
    'user_invite_page_text' => 'Hesabınızı sonlandırmak ve erişim kazanmak için bir şifre ayarlamanız gerekmektedir. Bu şifre :appName sitemize gelecekteki ziyaretleriniz için de gereklidir.',
    'user_invite_page_confirm_button' => 'Şifreyi doğrula',
    'user_invite_success' => 'Şifreniz ayarlandı artık :appName sitemize erişebilirsiniz!'
];