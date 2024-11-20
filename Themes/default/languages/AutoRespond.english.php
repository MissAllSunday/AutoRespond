<?php

/**
 * Auto respond mod (SMF)
 *
 * @package AutoRespond
 * @version 2.1.1
 * @author Michel Mendiola <suki@missallsunday.com>
 * @copyright Copyright (c) 2024  Michel Mendiola
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

global $txt;

// Admin
$txt['AR_menu'] = 'Auto Respond mod';
$txt['AR_admin_panel'] = 'Auto Respond admin panel';
$txt['AR_admin_panel_desc'] = 'This is the admin panel for the Auto Respond mod, from here you can add/delete/edit your custom messages.';

$txt['AR_admin_settings'] = 'Settings';
$txt['AR_admin_add'] = 'Add a new message';
$txt['AR_admin_list'] = 'Message list';
$txt['AR_admin_edit'] = 'Editing: %s';
$txt['AR_admin_delete'] = 'Deleting: %s';
$txt['AR_admin_edit'] = 'Edit this message:';

//Form
$txt['AR_form_title'] = 'The message\'s title:';
$txt['AR_form_title_desc'] = 'max size allowed: 255). If empty the mod will use the original topic\'s subject';
$txt['AR_form_body'] = 'The message\'s content:';
$txt['AR_form_body_desc'] = 'You can use BBC as well as the following variables:<br />
- {TOPIC_POSTER} The topic\'s poster name.<br />
- {POSTED_TIME} Formatted time.<br />
- {TOPIC_SUBJECT} The topic subject.';
$txt['AR_form_boards'] = $txt['AR_form_board_id'] = 'Select the boards where this message will appear as a response:';
$txt['AR_form_user'] = 'ID of the user who will post the reply:';
$txt['AR_form_user_desc'] = 'If left empty, the mod will use the default user ID: 1';
$txt['AR_form_send_add'] = 'Create';
$txt['AR_form_send_edit'] = 'Edit';
$txt['AR_form_error_empty'] = 'The following fields should not be left empty:';
$txt['AR_form_success_delete'] = 'The entry has been deleted successfully.';
$txt['AR_form_success_add'] = 'The entry has been created successfully.';
$txt['AR_form_success_edit'] = 'The entry has been updated successfully.';
$txt['AR_delete_confirmation'] = 'Do you really want to delete %s ?';
$txt['AR_delete_this'] = 'Delete';
$txt['AR_admin_adding'] = 'Adding a new custom message';
$txt['AR_enable'] = 'Enable AutoRespond mod:';
$txt['AR_enable_sub'] = 'This will let you use the mod.';
$txt['AR_list_title'] = 'Title';
$txt['AR_list_body'] = 'Message';
$txt['AR_list_id'] = 'ID';
$txt['AR_list_user'] = 'User ID';
$txt['AR_list_board'] = 'Board ID';
$txt['AR_update_post_count'] = 'Update the poster\'s count?';
$txt['AR_update_post_count_sub'] = 'If checked, it will increment the post account of the user who will post the reply.';
$txt['AR_lock_topic_after'] = 'Lock the topic after the response?';
$txt['AR_lock_topic_after_sub'] = 'If checked, the topic where the response will be posted will get locked automatically.';
$txt['AR_dummy_ip'] = 'Place a dummy IP (127.0.0.1) instead of the posters IP';
$txt['AR_dummy_ip_sub'] = 'If this is checked, all the messages generated by this mod will have an IP 127.0.0.1 (localhost).';
$txt['AR_manage_desc'] = 'Here\'s a list of all your custom messages, from here you can add/edit/delete any message as well as add new ones';

/* Error strings */
$txt['AR_error_delete'] = 'The message could not be deleted.';
$txt['AR_no_message'] = 'The message does not exist.';
$txt['AR_empty_value'] = 'You need to fill out all the items in the form';
$txt['AR_empty_message_list'] = 'There are no messages yet';