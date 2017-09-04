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
    'app_name' => 'Nombre de aplicación',
    'app_name_desc' => 'Este nombre es mostrado en la cabecera y en cualquier email de la aplicación',
    'app_name_header' => 'Mostrar el nombre de la aplicación en la cabecera?',
    'app_public_viewing' => 'Permitir vista pública?',
    'app_secure_images' => 'Habilitar mayor seguridad para subir imágenes?',
    'app_secure_images_desc' => 'Por razones de performance, todas las imágenes son púicas. Esta opción agrega una cadena larga difícil de adivinar, asegúrese que los indices de directorios no esán habilitados para prevenir el acceso fácil a las imágenes.',
    'app_editor' => 'Editor de página',
    'app_editor_desc' => 'Seleccione cuál editor ser usado por todos los usuarios para editar páginas.',
    'app_custom_html' => 'Contenido de cabecera HTML customizable',
    'app_custom_html_desc' => 'Cualquier contenido agregado aquíseráinsertado al final de la secón <head> de cada ágina. Esto esútil para sobreescribir estilo o agregar código para anaíticas.',
    'app_logo' => 'Logo de la aplicación',
    'app_logo_desc' => 'Esta imagen debería de ser 43px en altura. <br>Iágenes grandes seán escaladas.',
    'app_primary_color' => 'Color primario de la aplicación',
    'app_primary_color_desc' => 'Esto debería ser un valor hexadecimal. <br>Deje el valor vaío para reiniciar al valor por defecto.',

    /**
     * Registration settings
     */

    'reg_settings' => 'Ajustes de registro',
    'reg_allow' => 'Permitir registro?',
    'reg_default_role' => 'Rol de usuario por defecto despúes del registro',
    'reg_confirm_email' => 'Requerir email de confirmaación?',
    'reg_confirm_email_desc' => 'Si la restricción por dominio es usada, entonces la confirmaciónpor email serárequerida y el valor a continuón será ignorado.',
    'reg_confirm_restrict_domain' => 'Restringir registro al dominio',
    'reg_confirm_restrict_domain_desc' => 'Introduzca una lista separada por comasa de los emails del dominio a los que les gustaría restringir el registro por dominio. A los usuarios les seá enviado un emal para confirmar la dirección antes de que se le permita interactuar con la aplicacón. <br> Note que los usuarios se les permitir ácambiar sus direcciones de email luego de un registr éxioso.',
    'reg_confirm_restrict_domain_placeholder' => 'Ninguna restricción establecida',

    /**
     * Role settings
     */

    'roles' => 'Roles',
    'role_user_roles' => 'Roles de usuario',
    'role_create' => 'Crear nuevo rol',
    'role_create_success' => 'Rol creado satisfactoriamente',
    'role_delete' => 'Borrar rol',
    'role_delete_confirm' => 'Se borrará el rol con nombre  \':roleName\'.',
    'role_delete_users_assigned' => 'Este rol tiene :userCount usuarios asignados. Si ud. quisiera migrar los usuarios de este rol, seleccione un nuevo rol a continuación.',
    'role_delete_no_migration' => "No migrar usuarios",
    'role_delete_sure' => 'Está seguro que desea borrar este rol?',
    'role_delete_success' => 'Rol borrado satisfactoriamente',
    'role_edit' => 'Editar rol',
    'role_details' => 'Detalles de rol',
    'role_name' => 'Nombre de rol',
    'role_desc' => 'Descripción corta de rol',
    'role_system' => 'Permisos de sistema',
    'role_manage_users' => 'Gestionar usuarios',
    'role_manage_roles' => 'Gestionar roles y permisos de roles',
    'role_manage_entity_permissions' => 'Gestionar todos los permisos de libros, capítulos y áginas',
    'role_manage_own_entity_permissions' => 'Gestionar permisos en libros propios, capítulos y páginas',
    'role_manage_settings' => 'Gestionar ajustes de activos',
    'role_asset' => 'Permisos de activos',
    'role_asset_desc' => 'Estos permisos controlan el acceso por defecto a los activos del sistema. Permisos a Libros, Capítulos y áginas sobreescribiran estos permisos.',
    'role_all' => 'Todo',
    'role_own' => 'Propio',
    'role_controlled_by_asset' => 'Controlado por el actvo al que ha sido subido',
    'role_save' => 'Guardar rol',
    'role_update_success' => 'Rol actualizado éxitosamente',
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
    'users_external_auth_id' => 'ID externo de autenticación',
    'users_password_warning' => 'Solo rellene a continuación si desea cambiar su password:',
    'users_system_public' => 'Este usuario representa cualquier usuario invitado que visita la aplicación. No puede utilizarse para hacer login sio que es asignado automáticamente.',
    'users_books_view_type' => 'Diseño de pantalla preferido para libros',
    'users_delete' => 'Borrar usuario',
    'users_delete_named' => 'Borrar usuario :userName',
    'users_delete_warning' => 'Se borrará completamente el usuario con el nombre \':userName\' del sistema.',
    'users_delete_confirm' => 'Está seguro que desea borrar este usuario?',
    'users_delete_success' => 'Usuarios removidos éxitosamente',
    'users_edit' => 'Editar Usuario',
    'users_edit_profile' => 'Editar perfil',
    'users_edit_success' => 'Usuario actualizado',
    'users_avatar' => 'Avatar del usuario',
    'users_avatar_desc' => 'Esta imagen debe ser aproximadamente 256px por lado.',
    'users_preferred_language' => 'Lenguaje preferido',
    'users_social_accounts' => 'Cuentas sociales',
    'users_social_accounts_info' => 'Aquí puede conectar sus otras cuentas para un ápido y ás ácil login. Desconectando una cuenta aqu íno revca accesos ya autorizados. Revoque el acceso desde se perfil desde los ajustes de perfil en la cuenta social conectada.',
    'users_social_connect' => 'Conectar cuenta',
    'users_social_disconnect' => 'Desconectar cuenta',
    'users_social_connected' => 'La cuenta :socialAccount ha sido éxitosamente añadida a su perfil.',
    'users_social_disconnected' => 'La cuenta :socialAccount ha sido desconectada éxitosamente de su perfil.',

];
