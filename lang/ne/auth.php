<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Login & Register
    'sign_up' => 'साइन अप गर्नुहोस्',
    'log_in' => 'लग इन गर्नुहोस्',
    'log_in_with' => ':socialDriver मार्फत लगइन गर्नुहोस्',
    'sign_up_with' => ':socailDriver प्रयोग गरेर साइन अप गर्नुहोस्',
    'logout' => 'लगआउट',

    'name' => 'नाम',
    'username' => 'प्रयोगकर्ता नाम',
    'email' => 'ईमेल',
    'password' => 'पासवर्ड',
    'password_confirm' => 'पासवर्ड पक्‍का गर्नुहोस्',
    'password_hint' => 'Must be at least 8 characters',
    'forgot_password' => 'पासवर्ड भुल्नुभयो?',
    'remember_me' => 'मलाई सम्झनुहोस्',
    'ldap_email_hint' => 'Please enter an email to use for this account.',
    'create_account' => 'खाता बनाउनुहोस्',
    'already_have_account' => 'तपाईंको पहिले नै खाता छ?',
    'dont_have_account' => 'के तपाईंको खाता छैन?',
    'social_login' => 'Social Login',
    'social_registration' => 'Social Registration',
    'social_registration_text' => 'Register and sign in using another service.',

    'register_thanks' => 'दर्ता गर्नुभएकोमा धन्यवाद!',
    'register_confirm' => 'Please check your email and click the confirmation button to access :appName.',
    'registrations_disabled' => 'Registrations are currently disabled',
    'registration_email_domain_invalid' => 'That email domain does not have access to this application',
    'register_success' => 'Thanks for signing up! You are now registered and signed in.',

    // Login auto-initiation
    'auto_init_starting' => 'लगइन प्रयास गर्दै',
    'auto_init_starting_desc' => 'We\'re contacting your authentication system to start the login process. If there\'s no progress after 5 seconds you can try clicking the link below.',
    'auto_init_start_link' => 'प्रमाणीकरणको साथ अगाडि बढ्नुहोस्',

    // Password Reset
    'reset_password' => 'पासवर्ड रिसेट गर्नुहोस',
    'reset_password_send_instructions' => 'Enter your email below and you will be sent an email with a password reset link.',
    'reset_password_send_button' => 'रिसेट लिङ्क पठाउनुहोस्',
    'reset_password_sent' => 'A password reset link will be sent to :email if that email address is found in the system.',
    'reset_password_success' => 'तपाइँको पासवर्ड सफलतापूर्वक रिसेट गरिएको छ।',
    'email_reset_subject' => 'Reset your :appName password',
    'email_reset_text' => 'तपाईं यो ईमेल प्राप्त गर्दै हुनुहुन्छ किनकि हामीले तपाईंको खाताको लागि पासवर्ड रिसेट अनुरोध प्राप्त गर्यौं।',
    'email_reset_not_requested' => 'यदि तपाईंले पासवर्ड रिसेट अनुरोध गर्नुभएको छैन भने, अगाडि कुनै कार्य आवश्यक पर्दैन।',

    // Email Confirmation
    'email_confirm_subject' => 'Confirm your email on :appName',
    'email_confirm_greeting' => 'Thanks for joining :appName!',
    'email_confirm_text' => 'Please confirm your email address by clicking the button below:',
    'email_confirm_action' => 'इमेल पुष्टि गर्नुहोस्',
    'email_confirm_send_error' => 'इमेल पुष्टिकरण आवश्यक छ तर प्रणालीले इमेल पठाउन सकेन। इमेल सही तरिकाले सेटअप गरिएको छ भनी सुनिश्चित गर्न प्रशासकलाई सम्पर्क गर्नुहोस्।',
    'email_confirm_success' => 'तपाईंको इमेल पुष्टि भएको छ! अब तपाईं यो इमेल ठेगाना प्रयोग गरेर लगइन गर्न सक्षम हुनुहुनेछ।',
    'email_confirm_resent' => 'Confirmation email resent, Please check your inbox.',
    'email_confirm_thanks' => 'Thanks for confirming!',
    'email_confirm_thanks_desc' => 'Please wait a moment while your confirmation is handled. If you are not redirected after 3 seconds press the "Continue" link below to proceed.',

    'email_not_confirmed' => 'Email Address Not Confirmed',
    'email_not_confirmed_text' => 'Your email address has not yet been confirmed.',
    'email_not_confirmed_click_link' => 'Please click the link in the email that was sent shortly after you registered.',
    'email_not_confirmed_resend' => 'If you cannot find the email you can re-send the confirmation email by submitting the form below.',
    'email_not_confirmed_resend_button' => 'पुष्टिकरण इमेल पुन: पठाउनुहोस्',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'खाता पासवर्ड सेट गर्नुहोस्',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'पासवर्ड पक्‍का गर्नुहोस्',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Reconfigure',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'सेटअप',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'मोबाइल एप',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'ब्याकअप कोड',
    'mfa_option_backup_codes_desc' => 'Generates a set of one-time-use backup codes which you\'ll enter on login to verify your identity. Make sure to store these in a safe & secure place.',
    'mfa_gen_confirm_and_enable' => 'Confirm and Enable',
    'mfa_gen_backup_codes_title' => 'Backup Codes Setup',
    'mfa_gen_backup_codes_desc' => 'Store the below list of codes in a safe place. When accessing the system you\'ll be able to use one of the codes as a second authentication mechanism.',
    'mfa_gen_backup_codes_download' => 'कोडहरू डाउनलोड गर्नुहोस्',
    'mfa_gen_backup_codes_usage_warning' => 'प्रत्येक कोड एक पटक मात्र प्रयोग गर्न सकिन्छ।',
    'mfa_gen_totp_title' => 'मोबाइल एप सेटअप',
    'mfa_gen_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan the QR code below using your preferred authentication app to get started.',
    'mfa_gen_totp_verify_setup' => 'सेटअप प्रमाणित गर्नुहोस्',
    'mfa_gen_totp_verify_setup_desc' => 'Verify that all is working by entering a code, generated within your authentication app, in the input box below:',
    'mfa_gen_totp_provide_code_here' => 'तपाईंको एप जेनेरेट गरिएको कोड यहाँ प्रदान गर्नुहोस्।',
    'mfa_verify_access' => 'पहुँच प्रमाणित गर्नुहोस्',
    'mfa_verify_access_desc' => 'Your user account requires you to confirm your identity via an additional level of verification before you\'re granted access. Verify using one of your configured methods to continue.',
    'mfa_verify_no_methods' => 'कुनै पनि तरिका कन्फिगर गरिएको छैन',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'मोबाइल एप प्रयोग गरेर प्रमाणित गर्नुहोस्',
    'mfa_verify_use_backup_codes' => 'ब्याकअप कोड प्रयोग गरेर प्रमाणित गर्नुहोस्',
    'mfa_verify_backup_code' => 'ब्याकअप कोड',
    'mfa_verify_backup_code_desc' => 'तल तपाईंको बाँकी रहेको ब्याकअप कोडहरू मध्ये एउटा प्रविष्ट गर्नुहोस्:',
    'mfa_verify_backup_code_enter_here' => 'यहाँ ब्याकअप कोड प्रविष्ट गर्नुहोस्',
    'mfa_verify_totp_desc' => 'तपाईंको मोबाइल एप प्रयोग गरेर उत्पन्न गरिएको कोड तल प्रविष्ट गर्नुहोस्:',
    'mfa_setup_login_notification' => 'मल्टि-फ्याक्टर विधि कन्फिगर गरिएको छ, कृपया अब कन्फिगर गरिएको विधि प्रयोग गरेर फेरि लगइन गर्नुहोस्।',
];
