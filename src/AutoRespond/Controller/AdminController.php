<?php

namespace AutoRespond\Controller;
class AdminController
{
	const ACTIONS = [
		'settings',
		'list',
		'add',
		'delete',
	];

	public function dispatch(): void
	{
		global $context, $txt;

		$context['page_title'] = $txt['AR_admin_panel'];

		$subActions = array(
			'basic' => 'BasicAutoRespondSettings',
			'list' => 'AutoRespondListPage',
			'add' => 'AutoRespondAdd',
			'add2' => 'AutoRespondAdd2',
			'edit' => 'AutoRespondEdit',
			'edit2' => 'AutoRespondEdit2',
			'delete' => 'AutoRespondDelete'
		);

		loadGeneralSettingParameters(self::ACTIONS, 'basic');

		// Load up all the tabs...
		$context[$context['admin_menu_name']]['tab_data'] = [
			'title' => $txt['AR_admin_panel'],
			'description' => $txt['AR_admin_panel_desc'],
			'tabs' => [
				'settings' => []
			],
		];
	}

}