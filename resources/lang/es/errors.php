<?php

return [

    /**
     * Error text strings.
     */

    // Permissions
    'permission' => 'No tiene permisos para visualizar la página solicitada.',
    'permissionJson' => 'No tiene permisos para ejecutar la acción solicitada.',

    // Auth
    'error_user_exists_different_creds' => 'Un usuario con el correo electrónico :email ya existe pero con credenciales diferentes.',
    'email_already_confirmed' => 'El correo electrónico ya ha sido confirmado, intente acceder a la aplicación.',
    'email_confirmation_invalid' => 'Este token de confirmación no es válido o ya ha sido usado, intente registrar uno nuevamente.',
    'email_confirmation_expired' => 'El token de confirmación ha expirado, un nuevo email de confirmacón ha sido enviado.',
    'ldap_fail_anonymous' => 'El acceso con LDAP ha fallado usando binding anónimo',
    'ldap_fail_authed' => 'El acceso LDAP ha fallado usando el dn & contraseña enviados',
    'ldap_extension_not_installed' => 'La extensión LDAP PHP no se encuentra instalada',
    'ldap_cannot_connect' => 'No se puede conectar con el servidor ldap, la conexión inicial ha fallado',
    'social_no_action_defined' => 'Acción no definida',
    'social_login_bad_response' => "Se ha recibido un error durante el acceso con :socialAccount error: \n:error",
    'social_account_in_use' => 'la cuenta :socialAccount ya se encuentra en uso, intente acceder a través de la opción :socialAccount .',
    'social_account_email_in_use' => 'El correo electrónico :email ya se encuentra en uso. Si ya dispone de una cuenta puede acceder a través de su cuenta :socialAccount desde la configuración de perfil.',
    'social_account_existing' => 'La cuenta :socialAccount ya se encuentra asignada a su perfil.',
    'social_account_already_used_existing' => 'La cuenta :socialAccount ya está siendo usada por otro usuario.',
    'social_account_not_used' => 'La cuenta :socialAccount no está asociada a ningún usuario. Por favor adjúntela a su configuración de perfil. ',
    'social_account_register_instructions' => 'Si no dispone de una cuenta, puede registrar una cuenta usando la opción de :socialAccount .',
    'social_driver_not_found' => 'Driver social no encontrado',
    'social_driver_not_configured' => 'Su configuración :socialAccount no es correcta.',

    // System
    'path_not_writable' => 'El fichero no pudo ser subido a la ruta :filePath . Asegúrese de que es escribible por el servidor.',
    'cannot_get_image_from_url' => 'No se puede obtener la imagen desde :url',
    'cannot_create_thumbs' => 'El servidor no puede crear la miniatura de la imagen. Compruebe que tiene la extensión PHP GD instalada.',
    'server_upload_limit' => 'El servidor no permite la subida de ficheros de este tamaño. Intente subir un fichero de menor tamaño.',
    'uploaded'  => 'El servidor no permite la subida de ficheros de este tamaño. Intente subir un fichero de menor tamaño.',
    'image_upload_error' => 'Ha ocurrido un error al subir la imagen',
    'image_upload_type_error' => 'El tipo de imagen que se quiere subir no es válido',
    'file_upload_timeout' => 'La carga del archivo ha caducado.',

    // Attachments
    'attachment_page_mismatch' => 'Página no coincidente durante la subida del adjunto ',
    'attachment_not_found' => 'No se encontró el adjunto',

    // Pages
    'page_draft_autosave_fail' => 'Fallo al guardar borrador. Asegúrese de que tiene conexión a Internet antes de guardar este borrador',
    'page_custom_home_deletion' => 'No se puede borrar una página mientras esté configurada como página de inicio',

    // Entities
    'entity_not_found' => 'Entidad no encontrada',
    'bookshelf_not_found' => 'Estante no encontrado',
    'book_not_found' => 'Libro no encontrado',
    'page_not_found' => 'Página no encontrada',
    'chapter_not_found' => 'Capítulo no encontrado',
    'selected_book_not_found' => 'El libro seleccionado no fue encontrado',
    'selected_book_chapter_not_found' => 'El libro o capítulo seleccionado no fue encontrado',
    'guests_cannot_save_drafts' => 'Los invitados no pueden guardar borradores',

    // Users
    'users_cannot_delete_only_admin' => 'No se puede borrar el único administrador',
    'users_cannot_delete_guest' => 'No se puede borrar el usuario invitado',

    // Roles
    'role_cannot_be_edited' => 'Este rol no puede ser editado',
    'role_system_cannot_be_deleted' => 'Este rol es un rol de sistema y no puede ser borrado',
    'role_registration_default_cannot_delete' => 'Este rol no puede ser borrado mientras sea el rol por defecto de nuevos registros',

    // Comments
    'comment_list' => 'Se ha producido un error al buscar los comentarios.',
    'cannot_add_comment_to_draft' => 'No puedes añadir comentarios a un borrador.',
    'comment_add' => 'Se ha producido un error al añadir el comentario.',
    'comment_delete' => 'Se ha producido un error al eliminar el comentario.',
    'empty_comment' => 'No se puede agregar un comentario vacío.',

    // Error pages
    '404_page_not_found' => 'Página no encontrada',
    'sorry_page_not_found' => 'Lo sentimos, la página a la que intenta acceder no pudo ser encontrada.',
    'return_home' => 'Volver a la página de inicio',
    'error_occurred' => 'Ha ocurrido un error',
    'app_down' => 'La aplicación :appName se encuentra caída en este momento',
    'back_soon' => 'Volverá a estar operativa pronto.',
];
