<?php

/**
 * Auto respond mod (SMF)
 *
 * @package AutoRespond
 * @version 2.1
 * @author Michel Mendiola <suki@missallsunday.com>
 * @copyright Copyright (c) 2024  Michel Mendiola
 * @license https://opensource.org/license/mit/
 */

global $txt;

// Admin
$txt['AR_menu'] = 'Мод Auto Respond';
$txt['AR_admin_panel'] = 'Конфигурация Auto Respond';
$txt['AR_admin_panel_desc'] = 'Это панель управления для мода Auto Respond, здесь вы можете добавлять/удалять/редактировать свои пользовательские сообщения.';

$txt['AR_admin_settings'] = $txt['settings'];
$txt['AR_admin_add'] = 'Добавить сообщение';
$txt['AR_admin_list'] = 'Список сообщений';
$txt['AR_admin_edit'] = 'Редактирование: %s';
$txt['AR_admin_delete'] = 'Удаление: %s';
$txt['AR_admin_edit'] = 'Редактировать это сообщение:';

//Form
$txt['AR_form_title'] = 'Заголовок сообщения:';
$txt['AR_form_title_desc'] = 'Максимально допустимый размер: 255. Если пусто, мод будет использовать тему оригинальной темы.';
$txt['AR_form_body'] = 'Текст сообщения:';
$txt['AR_form_body_desc'] = 'Вы можете использовать BBCode, а также следующие переменные:<br />
- {TOPIC_POSTER} Имя автора темы.<br />
- {POSTED_TIME} Форматированное время.<br />
- {TOPIC_SUBJECT} Заголовок темы.';
$txt['AR_form_boards'] = $txt['AR_form_board_id'] = 'Выберите разделы, на которых это сообщение будет отображаться в качестве ответа:';
$txt['AR_form_user'] = 'ID пользователя, который будет размещать ответ:';
$txt['AR_form_user_desc'] = 'Если оставить пустым, мод будет использовать идентификатор пользователя по умолчанию: 1';
$txt['AR_form_send_add'] = 'Создать';
$txt['AR_form_send_edit'] = 'Изменить';
$txt['AR_form_error_empty'] = 'Следующие поля не должны оставаться пустыми:';
$txt['AR_form_success_delete'] = 'Запись была успешно удалена.';
$txt['AR_form_success_add'] = 'Запись была успешно создана.';
$txt['AR_form_success_edit'] = 'Запись была успешно обновлена.';
$txt['AR_delete_confirmation'] = 'Хотите удалить %s ?';
$txt['AR_delete_this'] = 'Удалить';
$txt['AR_admin_adding'] = 'Добавление нового сообщения';
$txt['AR_enable'] = 'Включить мод Auto Respond:';
$txt['AR_enable_sub'] = 'Это позволит вам использовать мод.';
$txt['AR_list_title'] = 'Заголовок';
$txt['AR_list_body'] = 'Сообщение';
$txt['AR_list_id'] = 'ID';
$txt['AR_list_user'] = 'ID пользователя';
$txt['AR_list_board'] = 'ID раздела';
$txt['AR_update_post_count'] = 'Обновить счётчик сообщений?';
$txt['AR_update_post_count_sub'] = 'Если установить флажок, то будет увеличен счётчик сообщений пользователя, который опубликует ответ.';
$txt['AR_lock_topic_after'] = 'Закрывать тему после ответа?';
$txt['AR_lock_topic_after_sub'] = 'Если флажок установлен, тема, в которой будет размещён ответ, будет автоматически заблокирована.';
 $txt['AR_dummy_ip'] = 'Отображать фиктивный IP-адрес (127.0.0.1) вместо IP-адреса автора сообщений.';
$txt['AR_dummy_ip_sub'] = 'Если этот флажок установлен, то все сообщения, генерируемые этим модом, будут иметь IP 127.0.0.1 (localhost).';
$txt['AR_manage_desc'] = 'Здесь отображается список всех ваших пользовательских сообщений, отсюда вы можете добавлять/редактировать/удалять любые сообщения, а также добавлять новые.';

/* Error strings */
$txt['AR_error_delete'] = 'Сообщение не удалось удалить.';
$txt['AR_no_message'] = 'Сообщение не существует.';
$txt['AR_empty_value'] = 'Вам необходимо заполнить все пункты формы';
$txt['AR_empty_message_list'] = 'Сообщений пока нет';
