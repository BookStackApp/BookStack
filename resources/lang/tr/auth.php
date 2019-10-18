<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Girilen bilgiler bizdeki kayıtlarla uyuşmuyor.',
    'throttle' => 'Çok fazla giriş yapmaya çalıştınız. Lütfen :seconds saniye içinde tekrar deneyin.',

    // Login & Register
    'sign_up' => 'Kayıt Ol',
    'log_in' => 'Giriş Yap',
    'log_in_with' => ':socialDriver ile giriş yap',
    'sign_up_with' => ':socialDriver ile kayıt ol',
    'logout' => 'Çıkış Yap',

    'name' => 'İsim',
    'username' => 'Kullanıcı Adı',
    'email' => 'Email',
    'password' => 'Şifre',
    'password_confirm' => 'Şifreyi onayla',
    'password_hint' => 'En az 5 karakter olmalı',
    'forgot_password' => 'Şifrenizi mi unuttunuz?',
    'remember_me' => 'Beni Hatırla',
    'ldap_email_hint' => 'Hesabı kullanmak istediğiniz e-mail adresinizi giriniz.',
    'create_account' => 'Hesap Oluştur',
    'already_have_account' => 'Zaten bir hesabınız var mı?',
    'dont_have_account' => 'Hesabınız yok mu?',
    'social_login' => 'Diğer Servisler ile Giriş Yap',
    'social_registration' => 'Diğer Servisler ile Kayıt Ol',
    'social_registration_text' => 'Diğer servisler ile kayıt ol ve giriş yap.',

    'register_thanks' => 'Kayıt olduğunuz için teşekkürler!',
    'register_confirm' => 'Lütfen e-posta adresinizi kontrol edin ve gelen doğrulama bağlantısına tıklayınız. :appName.',
    'registrations_disabled' => 'Kayıt olma özelliği geçici olarak kısıtlanmıştır',
    'registration_email_domain_invalid' => 'Bu e-mail sağlayıcısının bu uygulamaya erişim izni yoktur.',
    'register_success' => 'Artık kayıtlı bir kullanıcı olarak giriş yaptınız.',


    // Password Reset
    'reset_password' => 'Parolayı Sıfırla',
    'reset_password_send_instructions' => 'Aşağıya e-mail adresinizi girdiğinizde parola yenileme bağlantısı mail adresinize gönderilecektir.',
    'reset_password_send_button' => '>Sıfırlama Bağlantısını Gönder',
    'reset_password_sent_success' => 'Sıfırlama bağlantısı :email adresinize gönderildi.',
    'reset_password_success' => 'Parolanız başarıyla sıfırlandı.',
    'email_reset_subject' => ':appName şifrenizi sıfırlayın.',
    'email_reset_text' => ' Parola sıfırlama isteğinde bulunduğunuz için bu maili görüntülüyorsunuz.',
    'email_reset_not_requested' => 'Eğer bu parola sıfırlama isteğinde bulunmadıysanız herhangi bir işlem yapmanıza gerek yoktur.',


    // Email Confirmation
    'email_confirm_subject' => ':appName için girdiğiniz mail adresiniz onaylayınız',
    'email_confirm_greeting' => ':appName\'e katıldığınız için teşekkürler!',
    'email_confirm_text' => 'Lütfen e-mail adresinizi aşağıda bulunan butona tıklayarak onaylayınız:',
    'email_confirm_action' => 'E-Maili Onayla',
    'email_confirm_send_error' => 'e-mail onayı gerekli fakat sistem mail göndermeyi başaramadı. Yöneticiniz ile görüşüp kurulumlarda bir sorun olmadığını doğrulayın.',
    'email_confirm_success' => 'e-mail adresiniz onaylandı!',
    'email_confirm_resent' => 'Doğrulama maili gönderildi, lütfen gelen kutunuzu kontrol ediniz...',

    'email_not_confirmed' => 'E-mail Adresi Doğrulanmadı',
    'email_not_confirmed_text' => 'Sağlamış olduğunuz e-mail adresi henüz doğrulanmadı.',
    'email_not_confirmed_click_link' => 'Lütfen kayıt olduktan kısa süre sonra size gönderilen maildeki bağlantıya tıklayın ve mail adresinizi onaylayın.',
    'email_not_confirmed_resend' => 'Eğer gelen maili bulamadıysanız aşağıdaki formu tekrar doldurarak onay mailini kendinize tekrar gönderebilirsiniz.',
    'email_not_confirmed_resend_button' => 'Doğrulama Mailini Yeniden Yolla',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success' => 'Password set, you now have access to :appName!'
];