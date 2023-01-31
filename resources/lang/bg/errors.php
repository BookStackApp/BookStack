<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Нямате права за достъп до избраната страница.',
    'permissionJson' => 'Нямате права да извършите тази операция.',

    // Auth
    'error_user_exists_different_creds' => 'Потребител с емайл :email вече съществува но с други данни.',
    'email_already_confirmed' => 'Емейлът вече беше потвърден. Моля опитрайте да влезете.',
    'email_confirmation_invalid' => 'Този код за достъп не е валиден или вече е бил използван, Моля опитай да се регистрираш отново.',
    'email_confirmation_expired' => 'Кодът за потвърждение изтече, нов емейл за потвърждение беше изпратен.',
    'email_confirmation_awaiting' => 'Емайл адреса, който използвате трябва да се потвърди',
    'ldap_fail_anonymous' => 'LDAP достъпът е неуспешен с анонимни настройки',
    'ldap_fail_authed' => 'Опита за достъп чрез LDAP с използваната парола не беше успешен',
    'ldap_extension_not_installed' => 'LDAP PHP не беше инсталирана',
    'ldap_cannot_connect' => 'Не може да се свържете с Ldap сървъра, първоначалната връзка се разпадна',
    'saml_already_logged_in' => 'Вече сте влезли',
    'saml_user_not_registered' => 'Потребителят :name не е регистриран и автоматичната регистрация не е достъпна',
    'saml_no_email_address' => 'Не успяхме да намерим емейл адрес, за този потребител, от информацията предоставена от външната система',
    'saml_invalid_response_id' => 'Заявката от външната система не е разпознат от процеса започнат от това приложение. Връщането назад след влизане може да породи този проблем.',
    'saml_fail_authed' => 'Влизането чрез :system не беше успешно, системата не успя да удостовери потребителя',
    'oidc_already_logged_in' => 'Вече си вписан',
    'oidc_user_not_registered' => 'Потребителят :name не е регистриран, а автоматичната регистрация е изключена',
    'oidc_no_email_address' => 'Не можах да намеря имейл адрес за този потребител в данните, предоставени от външната удостоверителна система',
    'oidc_fail_authed' => 'Вписването чрез :system не беше успешно, тъй като системата не предостави успешна оторизация',
    'social_no_action_defined' => 'Действието не беше дефинирано',
    'social_login_bad_response' => "Възникна грешка по време на :socialAccount login: \n:error",
    'social_account_in_use' => 'Този :socialAccount вече е използван. Опитайте се да влезете чрез опцията за :socialAccount.',
    'social_account_email_in_use' => 'Този емейл адрес вече е бил използван. Ако вече имате профил, може да го свържете чрез :socialAccount от вашия профил.',
    'social_account_existing' => 'Този :socialAccount вече в свързан с вашия профил.',
    'social_account_already_used_existing' => 'Този :socialAccount вече се използва от друг потребител.',
    'social_account_not_used' => 'Социалният профил :socialAccount не е свързан с потребител. Моля, свържи го в настройките на профила си. ',
    'social_account_register_instructions' => 'Ако все още нямаш профил, може да се регистрираш чрез опцията :socialAccount.',
    'social_driver_not_found' => 'Кодът за връзка със социалната мрежа не съществува',
    'social_driver_not_configured' => 'Социалните настройки на твоя :socialAccount не са конфигурирани правилно.',
    'invite_token_expired' => 'Твоята покана е изтекла. Вместо това може да пробваш да възстановиш паролата на профила си.',

    // System
    'path_not_writable' => 'Не може да се качи файл в :filePath. Увери се на сървъра, че в пътя може да се записва.',
    'cannot_get_image_from_url' => 'Не мога да взема съобщението от :url',
    'cannot_create_thumbs' => 'Сървърът не може да създаде малки изображения. Моля, увери се, че разширението GD PHP е инсталирано.',
    'server_upload_limit' => 'Сървърът не позволява качвания с такъв размер. Моля, пробвайте файл с по-малък размер.',
    'uploaded'  => 'Сървърът не позволява качвания с такъв размер. Моля, пробвайте файл с по-малък размер.',
    'file_upload_timeout' => 'Качването на файла изтече.',

    // Drawing & Images
    'image_upload_error' => 'Възникна грешка при качването на изображението',
    'image_upload_type_error' => 'Типът на качваното изображение е невалиден',
    'drawing_data_not_found' => 'Drawing data could not be loaded. The drawing file might no longer exist or you may not have permission to access it.',

    // Attachments
    'attachment_not_found' => 'Прикачения файл не е намерен',

    // Pages
    'page_draft_autosave_fail' => 'Неуспешно запазване на черновата. Увери се, че имаш свързаност с интернет преди да запазиш страницата',
    'page_custom_home_deletion' => 'Не мога да изтрия страницата, докато е настроена като начална',

    // Entities
    'entity_not_found' => 'Обектът не е намерен',
    'bookshelf_not_found' => 'Shelf not found',
    'book_not_found' => 'Книгата не е намерена',
    'page_not_found' => 'Страницата не е намерена',
    'chapter_not_found' => 'Главата не е намерена',
    'selected_book_not_found' => 'Избраната книга не е намерена',
    'selected_book_chapter_not_found' => 'Избраната книга или глава не е намерена',
    'guests_cannot_save_drafts' => 'Гостите не могат да запазват чернови',

    // Users
    'users_cannot_delete_only_admin' => 'Не можеш да изтриеш единствения администратор',
    'users_cannot_delete_guest' => 'Не можеш да изтриеш потребителя на госта',

    // Roles
    'role_cannot_be_edited' => 'Ролята не може да бъде редактирана',
    'role_system_cannot_be_deleted' => 'Тази роля е системна и не може да бъде изтрита',
    'role_registration_default_cannot_delete' => 'Тази роля не може да бъде изтрита, докато е настроена по подразбиране за нови регистрации',
    'role_cannot_remove_only_admin' => 'Този потребител е единственият с присвоена администраторска роля. Приложи администраторската роля на друг потребител, преди да я премахнеш от тук.',

    // Comments
    'comment_list' => 'Настъпи грешка при зареждането на коментарите.',
    'cannot_add_comment_to_draft' => 'Не може да добавяте коментари към чернова.',
    'comment_add' => 'Възникна грешка при актуализиране/добавяне на коментар.',
    'comment_delete' => 'Възникна грешка при изтриването на коментара.',
    'empty_comment' => 'Не може да добавите празен коментар.',

    // Error pages
    '404_page_not_found' => 'Страницата не е намерена',
    'sorry_page_not_found' => 'Страницата, която търсите не може да бъде намерена.',
    'sorry_page_not_found_permission_warning' => 'Ако смятате, че тази страница съществува, най-вероятно нямате право да я преглеждате.',
    'image_not_found' => 'Изображението не е намерено',
    'image_not_found_subtitle' => 'Съжалявам, файлът на изображението, което търсиш, не може да бъде намерен.',
    'image_not_found_details' => 'Ако си очаквал/а това изображение да същестува, може да е било изтрито.',
    'return_home' => 'Назад към Начало',
    'error_occurred' => 'Възникна грешка',
    'app_down' => ':appName не е достъпно в момента',
    'back_soon' => 'Ще се върне обратно онлайн скоро.',

    // API errors
    'api_no_authorization_found' => 'Но беше намерен код за достъп в заявката',
    'api_bad_authorization_format' => 'В заявката имаше код за достъп, но формата изглежда е неправилен',
    'api_user_token_not_found' => 'Няма открит API код, който да отговоря на предоставения такъв',
    'api_incorrect_token_secret' => 'Секретния код, който беше предоставен за достъп до API-а е неправилен',
    'api_user_no_api_permission' => 'Собственика на АPI кода няма право да прави API заявки',
    'api_user_token_expired' => 'Кода за достъп, който беше използван, вече не е валиден',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Беше върната грешка, когато се изпрати тестовият емейл:',

];
