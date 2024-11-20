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

function template_ar_show_add()
{
    global $context, $scripturl, $txt;

    $action = $context['data']['id'] ? 'edit' : 'add';
    $id = $context['data']['id'] ? (';id='. $context['data']['id']) : '';

    echo '
<div class="cat_bar">
    <h3 class="catbg">'. $txt['AR_admin_'. $context['sub_action']] .'</h3>
</div>
<div class="windowbg">';

    if (!empty($context['data']['errors'])) {
        echo '<div class="errorbox">'. $txt['AR_form_error_empty'] .'<br /><ul>';

        foreach ($context['data']['errors'] as $key => $field) {
            echo '<li>-'. $txt['AR_form_'. $field] .'</li>';
        }

        echo '</ul></div>';
    }

    echo '
    <form action="'. $scripturl . '?action=admin;area=autorespond;sa='. $context['sub_action'] . $id .'"
        name="autoRespond" 
	    accept-charset="'. $context['character_set'] .'"
	    method="post"
        onsubmit="submitonce(this);">
        <dl class="settings">
            <dt>
                <label>'. $txt['AR_form_title'] .'</label><br />
                <small>'. $txt['AR_form_title_desc'] .'</small>
            </dt>
            <dd>
                <input type="text" name="title" size="55" tabindex="1" maxlength="255" value="'. $context['data']['entry']->getTitle() .'" class="input_text" />
            </dd>
            <dt>
                <label>'. $txt['AR_form_user'] .'</label><br />
                <small>'. $txt['AR_form_user_desc'] .'</small>
                </dt>
            <dd>
                <input type="text" name="user_id" size="5" tabindex="1" maxlength="255" value="'. $context['data']['entry']->getUserId() .'" class="input_text" />
            </dd>';

    /* Boards */
    echo'
            <dt>'. $txt['AR_form_boards'] .'</dt>
            <dd>';

    foreach($context['data']['boards'] as $board)
        echo '
                <label for="'. $board['id'] .'">
                    <input id="'. $board['id'] .'" name="board_id[]" value="'. $board['id'] .'" type="checkbox"
                     '. (in_array($board['id'], $context['data']['entry']->getBoardId()) ? 'checked=yes' : '') .' />
                     '. $board['name'].' ID ('. $board['id'].')
                </label>';

    echo'
            </dd>
            <dt>
                <label>'. $txt['AR_form_body'] .'</label><br />
                <small>'. $txt['AR_form_body_desc'] .'</small>
            </dt>
            <dd>
                <textarea rows="15" cols="50" name="body" id="body">'. $context['data']['entry']->getBody() .'</textarea>
            </dd>     
        </dl>
        <div id="confirm_buttons">
            <input type="submit" value="'. $txt['AR_form_send_'. $action] . '" class="button">
            <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
        </div>         
    </form>
</div>';

}

function template_ar_show_list()
{
    global $context, $txt, $scripturl;

    $session = ';'. $context['session_var'] .'='. $context['session_id'];

    if (!empty($_SESSION['autorespond'])) {
        echo '<div class="infobox">'. $txt[$_SESSION['autorespond']] .'</div>';

        unset($_SESSION['autorespond']);
    }

    if (empty($context['data']['entries'])) {
        echo '
            <div class="noticebox">' . $txt['AR_empty_message_list'] . '</div>';
    } else
    {
        echo '
		<table class="table_grid">
			<thead>
				<tr class="title_bar">
					<th scope="col" class="first_th">'.$txt['AR_list_id'].'</th>
					<th scope="col">'.$txt['AR_list_title'].'</th>
					<th scope="col">'.$txt['AR_list_board'].'</th>
					<th scope="col">'.$txt['AR_list_user'].'</th>
					<th scope="col">'.$txt['AR_form_send_edit'].'</th>
					<th scope="col" class="last_th">'.$txt['AR_delete_this'].'</th>
				</tr>
			</thead>
			<tbody>';

        /* AutoRespondEntity $entry */
        foreach($context['data']['entries'] as $id => $entry)
            echo '
					<tr class="windowbg" style="text-align: center">
						<td class="windowbg2">
						'. $id .'
						</td>
						<td>
						'. $entry->getTitle() .'
						</td>
						<td>
						'. implode(', ', $entry->getBoardId()) .'
						</td>
						<td class="windowbg2">
						'. $context['data']['users'][$entry->getUserId()]['link_color'] .'
						</td>
						<td>
						<a href="'.$scripturl. '?'. \AutoRespond\AutoRespondAdmin::URL .';sa=add;id='. $id . $session .'">'.$txt['AR_form_send_edit'].'</a>
						</td>
						<td class="windowbg2">
						<a href="'.$scripturl. '?'. \AutoRespond\AutoRespondAdmin::URL .';sa=delete;id='. $id . $session .'" class="you_sure">'.$txt['AR_delete_this'].'</a>
						</td>
					</tr>';

        echo '</tbody>
		</table><br />';
    }

    echo '
		<div id="confirm_buttons">
			<a class="button" href="'.$scripturl. '?'. \AutoRespond\AutoRespondAdmin::URL .';sa=add'. $session .'">'. $txt['AR_admin_add']  .'</a>
		</div>';
}