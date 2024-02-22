<?php

/**
 * Auto respond mod (SMF)
 *
 * @package AutoRespond
 * @version 2.0.2
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2017 Jessica González
 * @license http://www.mozilla.org/MPL/ MPL 2.0
 */


/* This works for both adding and editing, yeah, I'm lazy... */
function template_auto_respond_add()
{
	global $context, $scripturl, $txt;

	$sa = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : 'add';

	/* The nice form, actually this is just an ugly table */
	echo '
	<form action="', $scripturl, '?action=admin;area=autorespond;sa=',$sa,'2','" method="post" target="_self" id="postmodify" class="flow_hidden" onsubmit="submitonce(this);smc_saveEntities(\'postmodify\', [\'title\', \'body\']);" >
		<div class="cat_bar">
			<h3 class="catbg">',$txt['AR_admin_'.$sa],'</h3>
		</div>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
			<div class="content">
				<dl class="settings">';

	/* Title */
	echo'
					<dt>
						',$txt['AR_form_title'],'
					</dt>
					<dd>
						<input type="text" name="title" size="55" tabindex="1" maxlength="255" value="',$context['autorespond'][$sa]['title'],'" class="input_text" />
					</dd>';

	/* User */
	echo'
					<dt>
						',$txt['AR_form_user'],'
					</dt>
					<dd>
						<input type="text" name="user_id" size="5" tabindex="1" maxlength="255" value="',$context['autorespond'][$sa]['user_id'],'" class="input_text" />
					</dd>';

	/* Boards */
	echo'
					<dt>
						',$txt['AR_form_boards'],'
					</dt>
					<dd>';

	foreach($context['autorespond']['boards'] as $b)
		echo '
						<label for="',$b['id_board'],'"><input id="',$b['id_board'],'" name="board_id[]" value="',$b['id_board'],'" type="checkbox" ',(in_array($b['id_board'],$context['autorespond'][$sa]['board_id']) ? 'checked=yes' : ''),' />',$b['name'],' ID (',$b['id_board'],')</label><br />';

	echo'
					</dd>';

	/* Textarea */
	echo'
					<dt>
						',$txt['AR_form_body'],'
						<small>',$txt['AR_form_body_decs'],'</small>
					</dt>
					<dd>
						<textarea rows="15" cols="50" name="body" id="body">',$context['autorespond'][$sa]['body'],'</textarea>
					</dd>
				</dl>';

		/* Done with the fields, lets show a submit button */
		echo '
				<hr class="hrcolor clear" />
				<div id="confirm_buttons" class="righttext">
					<input type="hidden" id="', $context['session_var'], '" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="hidden" id="arid" name="arid" value="',$context['autorespond'][$sa]['id'],'" />
					<input type="submit" name="send" class="sbtn" value="',$txt['AR_form_send_'.$sa],'" />
				</div>
				<span class="botslice"><span></span></span>
			</div>
		</div>
	</form>';
}

function template_auto_respond_list()
{
	global $context, $txt, $scripturl;

	/* Tell the user theres no messages */
	if (empty($context['GetARList']))
		echo '
			<span class="clear upperframe">
				<span></span>
			</span>
			<div class="roundframe rfix">
				<div class="innerframe">
					<div class="content">
						',$txt['AR_empty_message_list'],'
					</div>
				</div>
			</div>
			<span class="lowerframe">
				<span></span>
			</span><br />';

	/* Omgosh! */
	else
	{
		echo '
		<table class="table_grid" cellspacing="0" width="100%">
			<thead>
				<tr class="catbg">
					<th scope="col" class="first_th">',$txt['AR_list_id'],'</th>
					<th scope="col">',$txt['AR_list_title'],'</th>
					<th scope="col">',$txt['AR_list_body'],'</th>
					<th scope="col">',$txt['AR_list_board'],'</th>
					<th scope="col">',$txt['AR_list_user'],'</th>
					<th scope="col">',$txt['AR_form_send_edit'],'</th>
					<th scope="col" class="last_th">',$txt['AR_delete_this'],'</th>
				</tr>
			</thead>
			<tbody>';

		foreach($context['GetARList'] as $AR_list)
			echo '
					<tr class="windowbg" style="text-align: center">
						<td class="windowbg2">
						',$AR_list['id'],'
						</td>
						<td>
						',$AR_list['title'],'
						</td>
						<td class="windowbg2">
						',AutoRespondTruncate($AR_list['body'], 20),'
						</td>
						<td>
						',$AR_list['board_id'],'
						</td>
						<td class="windowbg2">
						',$AR_list['user_id'],'
						</td>
						<td>
						<a href="'.$scripturl. '?action=admin;area=autorespond;sa=edit;arid=',$AR_list['id'],'">',$txt['AR_form_send_edit'],'</a>
						</td>
						<td class="windowbg2">
						<a href="'.$scripturl. '?action=admin;area=autorespond;sa=delete;arid=',$AR_list['id'],'">',$txt['AR_delete_this'],'</a>
						</td>
					</tr>';

		echo '</tbody>
		</table><br />';
	}

	/* Add button */
	echo '
		<div id="confirm_buttons">
			<form action="',$scripturl, '?action=admin;area=autorespond;sa=add" method="post" target="_self">
				<input type="submit" name="send" class="sbtn" value="',$txt['AR_admin_add'],'" />
			</form>
		</div>';
}
