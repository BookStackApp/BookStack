<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Ajustes',
    'settings_save' => 'Guardar ajustes',
    'settings_save_success' => 'Ajustes guardados',

    /**
     * App settings
     */

    'app_settings' => 'Ajustes de App',
    'app_name' => 'Nombre de aplicaci√n',
    'app_name_desc' => 'Este nombre es mostrado en la cabecera y en cualquier email de la aplicaci√n',
    'app_name_header' => 'Mostrar el nombre de la aplicaci√n en la cabecera?',
    'app_public_viewing' => 'Permitir vista p√blica?',
    'app_secure_images' => 'Habilitar mayor seguridad para subir im√genes?',
    'app_secure_images_desc' => 'Por razones de performance, todas las im√°genes son p√∫icas. Esta opci√≥n agrega una cadena larga dif√≠cil de adivinar, aseg√∫rese que los indices de directorios no es√°n habilitados para prevenir el acceso f√°cil a las im√°genes.',
    'app_editor' => 'Editor de p√gina',
    'app_editor_desc' => 'Seleccione cu√l editor ser√°usado por todos los usuarios para editar p√ginas.',
    'app_custom_html' => 'Contenido de cabecera HTML customizable',
    'app_custom_html_desc' => 'Cualquier contenido agregado aqu√≠ser√°insertado al final de la secci√n <head> de cada p√gina. Esto es √til para sobreescribir estilo o agregar c√digo para anal√ticas.',
    'app_logo' => 'Logo de la aplicaci√n',
    'app_logo_desc' => 'Esta imagen deber√a de ser 43px en altura. <br>Im√genes grandes ser√n escaladas.',
    'app_primary_color' => 'Color primario de la aplicaci√n',
    'app_primary_color_desc' => 'Esto deber√a ser un valor hexadecimal. <br>Deje el valor vac√o para reiniciar al valor por defecto.',

    /**
     * Registration settings
     */

    'reg_settings' => 'Ajustes de registro',
    'reg_allow' => 'Permitir registro?',
    'reg_default_role' => 'Rol de usuario por defecto desp√es del registro',
    'reg_confirm_email' => 'Requerir email de confirmaaci√n?',
    'reg_confirm_email_desc' => 'Si la restricci√n por dominio es usada, entonces la confirmaci√≥npor email ser√°requerida y el valor a continuaci√n ser√ ignorado.',
    'reg_confirm_restrict_domain' => 'Restringir registro al dominio',
    'reg_confirm_restrict_domain_desc' => 'Introduzca una lista separada por comasa de los emails del dominio a los que les gustar√≠a restringir el registro por dominio. A los usuarios les se√° enviado un emal para confirmar la direcci√≥n antes de que se le permita interactuar con la aplicac√≥n. <br> Note que los usuarios se les permitir √°cambiar sus direcciones de email luego de un registro√©xitoo.',
    'reg_confirm_restrict_domain_placeholder' => 'Ninguna restricci√n establecida',

    /**
     * Role settings
     */

    'roles' => 'Roles',
    'role_user_roles' => 'Roles de usuario',
    'role_create' => 'Crear nuevo rol',
    'role_create_success' => 'Rol creado satisfactoriamente',
    'role_delete' => 'Borrar rol',
    'role_delete_confirm' => 'Se borrar√ el rol con nombre  \':roleName\'.',
    'role_delete_users_assigned' => 'Este rol tiene :userCount usuarios asignados. Si ud. quisiera migrar los usuarios de este rol, seleccione un nuevo rol a continuaci√n.',
    'role_delete_no_migration' => "No migrar usuarios",
    'role_delete_sure' => 'Est√ seguro que desea borrar este rol?',
    'role_delete_success' => 'Rol borrado satisfactoriamente',
    'role_edit' => 'Editar rol',
    'role_details' => 'Detalles de rol',
    'role_name' => 'Nombre de rol',
    'role_desc' => 'Descripci√n corta de rol',
    'role_system' => 'Permisos de sistema',
    'role_manage_users' => 'Gestionar usuarios',
    'role_manage_roles' => 'Gestionar roles y permisos de roles',
    'role_manage_entity_permissions' => 'Gestionar todos los permisos de libros, cap√tulos y p√ginas',
    'role_manage_own_entity_permissions' => 'Gestionar permisos en libros propios, cap√tulos y p√ginas',
    'role_manage_settings' => 'Gestionar ajustes de activos',
    'role_asset' => 'Permisos de activos',
    'role_asset_desc' => 'Estos permisos controlan el acceso por defecto a los activos del sistema. Permisos a Libros, Cap√tulos y p√ginas sobreescribiran estos permisos.',
    'role_all' => 'Todo',
    'role_own' => 'Propio',
    'role_controlled_by_asset' => 'Controlado por el actvo al que ha sido subido',
    'role_save' => 'Guardar rol',
    'role_update_success' => 'Rol actualizado √xitosamente',
    'role_users' => 'Usuarios en este rol',
    'role_users_none' => 'No hay usuarios asignados a este rol',

    /**
     * Users
     */

    'users' => 'Usuarios',
    'user_profile' => 'Perfil de usuario',
    'users_add_new' => 'Agregar nuevo usuario',
    'users_search' => 'Buscar usuarios',
    'users_role' => 'Roles de usuario',
    'users_external_auth_id' => 'ID externo de autenticaci√n',
    'users_password_warning' => 'Solo rellene a continuaci√n si desea cambiar su password:',
    'users_system_public' => 'Este usuario representa cualquier usuario invitado que visita la aplicaci√n. No puede utilizarse para hacer login sino que es asignado autom√ticamente.',
    'users_delete' => 'Borrar usuario',
    'users_delete_named' => 'Borrar usuario :userName',
    'users_delete_warning' => 'Se borrar√ completamente el usuario con el nombre \':userName\' del sistema.',
    'users_delete_confirm' => 'Est√ seguro que desea borrar este usuario?',
    'users_delete_success' => 'Usuarios removidos √xitosamente',
    'users_edit' => 'Editar Usuario',
    'users_edit_profile' => 'Editar perfil',
    'users_edit_success' => 'Usuario actualizado',
    'users_avatar' => 'Avatar del usuario',
    'users_avatar_desc' => 'Esta imagen debe ser aproximadamente 256px por lado.',
    'users_preferred_language' => 'Lenguaje preferido',
    'users_social_accounts' => 'Cuentas sociales',
    'users_social_accounts_info' => 'Aqu√ puede conectar sus otras cuentas para un r√pido y m√s f√cil login. Desconectando una cuenta aqu√≠no revoca accesos ya autorizados. Revoque el acceso desde se perfil desde los ajustes de perfil en la cuenta social conectada.',
    'users_social_connect' => 'Conectar cuenta',
    'users_social_disconnect' => 'Desconectar cuenta',
    'users_social_connected' => 'La cuenta :socialAccount ha sido √xitosamente a√adida a su perfil.',
    'users_social_disconnected' => 'La cuenta :socialAccount ha sido desconectada √xitosamente de su perfil.',

];
