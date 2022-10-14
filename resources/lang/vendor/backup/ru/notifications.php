<?php

return [
    'exception_message' => 'Сообщение об ошибке: :message',
    'exception_trace' => 'Сведения об ошибке: :trace',
    'exception_message_title' => 'Сообщение об ошибке',
    'exception_trace_title' => 'Сведения об ошибке',

    'backup_failed_subject' => 'Не удалось сделать резервную копию :application_name',
    'backup_failed_body' => 'Внимание: Произошла ошибка во время резервного копирования :application_name',

    'backup_successful_subject' => 'Успешно создана новая резервная копия :application_name',
    'backup_successful_subject_title' => 'Успешно создана новая резервная копия!',
    'backup_successful_body' => 'Отличная новость, новая резервная копия :application_name успешно создана и сохранена на диск :disk_name.',

    'cleanup_failed_subject' => 'Не удалось очистить резервные копии :application_name',
    'cleanup_failed_body' => 'Произошла ошибка при очистке резервных копий :application_name',

    'cleanup_successful_subject' => 'Очистка от резервных копий :application_name прошла успешно',
    'cleanup_successful_subject_title' => 'Очистка резервных копий прошла удачно!',
    'cleanup_successful_body' => 'Очистка от старых резервных копий :application_name на диске :disk_name прошла удачно.',

    'healthy_backup_found_subject' => 'Резервная копия :application_name с диска :disk_name установлена',
    'healthy_backup_found_subject_title' => 'Резервная копия :application_name установлена',
    'healthy_backup_found_body' => 'Резервная копия :application_name успешно установлена. Хорошая работа!',

    'unhealthy_backup_found_subject' => 'Внимание: резервная копия :application_name не установилась',
    'unhealthy_backup_found_subject_title' => 'Внимание: резервная копия для :application_name не установилась. :problem',
    'unhealthy_backup_found_body' => 'Резервная копия для :application_name на диске :disk_name не установилась.',
    'unhealthy_backup_found_not_reachable' => 'Резервная копия не смогла установиться. :error',
    'unhealthy_backup_found_empty' => 'Резервные копии для этого приложения отсутствуют.',
    'unhealthy_backup_found_old' => 'Последнее резервное копирование создано :date является устаревшим.',
    'unhealthy_backup_found_unknown' => 'Извините, точная причина не может быть определена.',
    'unhealthy_backup_found_full' => 'Резервные копии используют слишком много памяти. Используется :disk_usage что выше допустимого предела: :disk_limit.',
];
