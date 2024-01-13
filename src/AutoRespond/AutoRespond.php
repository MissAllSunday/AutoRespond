<?php

namespace AutoRespond;
use OharaDBClass;

class AutoRespond
{
	function createRespond($msgOptions, $topicOptions, $posterOptions)
	{

		global $modSettings, $sourcedir, $context;

		/* Can't do much if the mod is not enable */
		if (empty($modSettings['AR_enable']))
			return;

		require_once($sourcedir . '/OharaDB.class.php');

		/* Get the message for this board */
		$params = [
			'rows' => 'id, board_id, user_id, title, body',
			'where' => 'find_in_set("' . $topicOptions['board'] . '",board_id) <> 0',
		];

		/* Prepare the query */
		$query = new OharaDBClass('autorespond');
		$query->Params($params);
		$query->GetData(null, true);

		/* There's no such thing... */
		if (empty($query->data_result))
			return;

		/* We need this... */
		require_once($sourcedir . '/Subs-Post.php');

		/* We got a message for this board */
		$context['AR_message'] = [
			'id' => $query->data_result['id'],
			'user_id' => (int)$query->data_result['user_id'],
			'title' => $query->data_result['title'],
			'body' => un_preparsecode($query->data_result['body']),
		];

		/* Add in the default replacements. */
		$replacements = [
			'TOPIC_POSTER' => $posterOptions['name'],
			'POSTED_TIME' => date("F j, Y, g:i a"),
			'TOPIC_SUBJECT' => $msgOptions['subject'],
		];

		/* Split the replacements up into two arrays, for use with str_replace */
		$find = [];
		$replace = [];

		foreach ($replacements as $f => $r) {
			$find[] = '{' . $f . '}';
			$replace[] = $r;
		}

		/* Do the variable replacements. */
		$context['AR_message']['body'] = str_replace($find, $replace, $context['AR_message']['body']);

		$newMsgOptions = [
			'id' => 0,
			'subject' => !empty($modSettings['AR_use_title']) ? $context['AR_message']['title'] : $msgOptions['subject'],
			'body' => $context['AR_message']['body'],
			'icon' => 'xx',
			'smileys_enabled' => 1,
			'attachments' => [],
		];

		$newTopicOptions = [
			'id' => $topicOptions['id'],
			'board' => $topicOptions['board'],
			'poll' => null,
			'lock_mode' => !empty($modSettings['AR_lock_topic_after']) ? 1 : null,
			'sticky_mode' => null,
			'mark_as_read' => false,
		];

		$newPosterOptions = [
			'id' => !empty($context['AR_message']['user_id']) ? $context['AR_message']['user_id'] : 1,
			'name' => '',
			'email' => '',
			'update_post_count' => !empty($modSettings['AR_update_post_count']) ? 1 : 0,
			'ip' => !empty($modSettings['AR_dummy_ip']) ? '127.0.0.1' : '',
		];

		/* Finally! */
		createPost($newMsgOptions, $newTopicOptions, $newPosterOptions);
	}

	public function adminMenu(array $menuData): void
	{

	}
}