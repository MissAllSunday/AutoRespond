<?php

declare(strict_types = 1);

/**
 * Auto respond mod (SMF)
 *
 * @package AutoRespond
 * @version 2.1
 * @author Michel Mendiola <suki@missallsunday.com>
 * @copyright Copyright (c) 2024  Michel Mendiola
 * @license https://opensource.org/license/mit/
 */

namespace AutoRespond;

class AutoRespond
{
	private AutoRespondService $service;

	public function __construct()
	{
		global $sourcedir;

		loadLanguage('AutoRespond');
		loadtemplate('AutoRespond');

		require_once($sourcedir . '/Subs-Post.php');

		// No DI :(
		$this->service = new AutoRespondService();
	}

	public function createRespond()
	{
		global $topicOptions, $msgOptions, $posterOptions, $modSettings, $context;

		if (empty($modSettings['AR_enable']))
			return;

		$entry = $this->service->getEntriesByBoard($topicOptions['board']);

		if (empty($entry))
			return;

		$context['AR_message'] = $entry;

		$context['AR_message']['body'] = un_preparsecode($entry['body']);


		$replacements = [
			'TOPIC_POSTER' => $posterOptions['name'],
			'POSTED_TIME' => date("F j, Y, g:i a"),
			'TOPIC_SUBJECT' => $msgOptions['subject'],
		];


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

		createPost($newMsgOptions, $newTopicOptions, $newPosterOptions);
	}
}

//Oh, wouldn't it be great if I *was* crazy? Then the world would be okay.