<?php

use AutoRespond\AutoRespondService;

class AutoRespondAdmin
{
	const ACTIONS = [
		'settings',
		'listPage',
		'add',
		'delete',
	];
	private AutoRespondService $service;

	public function __construct()
	{
		global $sourcedir;

		require_once($sourcedir . '/ManageServer.php');
		require_once($sourcedir . '/ManageSettings.php');

		loadLanguage('AutoRespond');
		loadtemplate('AutoRespond');

		$this->service = new AutoRespondService();
	}

	public function adminMenu(array &$menuData): void
	{
		global $txt;

		$menuData['config']['areas']['autorespond'] = [
			'label' => $txt['AR_menu'],
			'function' => [$this, 'addSettings'],
			'icon' => 'posts.gif',
			'subsections' => [
				'settings' => [$txt['AR_basic_settings']],
				'listPage' => [$txt['AR_list_page']],
				'add' => [$txt['AR_admin_add']],
			],
		];
	}

	function addSettings($return_config = false)
	{
		global $txt, $context;

		isAllowedTo('admin_forum');

		$context['page_title'] = $txt['AR_admin_panel'];

		$context['sub_template'] = 'show_settings';

		// Load up all the tabs...
		$context[$context['admin_menu_name']]['tab_data'] = [
			'title' => $txt['AR_admin_panel'],
			'description' => $txt['AR_admin_panel_desc'],
			'tabs' => [
				'settings' => []
			],
		];

		if (!empty(self::ACTIONS[$_REQUEST['sa']])) {
			$this->{self::ACTIONS[$_REQUEST['sa']]}();
		}

	}

	function settings($return_config = false): void
	{
		global $txt, $scripturl, $context;

		$config_vars = [
			['check', 'AR_enable', 'subtext' => $txt['AR_enable_sub']],
			['check', 'AR_update_post_count', 'subtext' => $txt['AR_update_post_count_sub']],
			['check', 'AR_use_title', 'subtext' => $txt['AR_use_title_sub']],
			['check', 'AR_lock_topic_after', 'subtext' => $txt['AR_lock_topic_after_sub']],
			['check', 'AR_dummy_ip', 'subtext' => $txt['AR_dummy_ip_sub']],

		];

		$context['post_url'] = $scripturl . '?action=admin;area=autorespond;save';
		$context['settings_title'] = $txt['AR_admin_panel'];
		$context['page_title'] = $txt['AR_admin_panel'];
		$context['sub_template'] = 'show_settings';

		if (isset($_GET['save']))
		{
			checkSession();
			saveDBSettings($config_vars);
			redirectexit('action=admin;area=autorespond');
		}

		prepareDBSettingContext($config_vars);
	}

	function listPage()
	{
		global $txt, $context, $scripturl;

		$context['GetARList'] = $this->service->getEntries();
		$context['sub_template'] = 'auto_respond_list';
		$context['page_title'] = $txt['AR_admin_list'];
		$context['linktree'][] = array(
			'url' => $scripturl. '?action=admin;area=autorespond;sa=listPage',
			'name' => $txt['AR_admin_list'],
		);
	}

	protected function add()
	{
		global $txt, $context, $scripturl;

		$id = !empty($_GET['id']) ? $_GET['id'] : 0;
		$isEditing = !empty($id);

		$context['sub_template'] = 'auto_respond_add';
		$context['page_title'] = $txt['AR_admin_adding'];
		$context['linktree'][] = array(
			'url' => $scripturl. '?action=admin;area=autorespond;sa=add' . ($isEditing ? (';id=' . $id) : ''),
			'name' => $txt['AR_admin_adding'],
		);

		$context['autorespond']['add'] = $isEditing ? $this->service->getEntries()['entries'][$id] : [];
		$context['autorespond']['boards'] = $this->service->getBoards();
	}

	protected function delete()
	{
		if (empty($_REQUEST['id'])) {
			// set some error message about it
			redirectexit('action=admin;area=autorespond;sa=list');
		}

		$id = $_REQUEST['id'];
		$entry = $this->service->getEntries([$id])['entries'][$id];

		if (empty($entry)) {
			// set some error not valid
			redirectexit('action=admin;area=autorespond;sa=list');
		}

		$this->service->deleteEntries([$id]);

		redirectexit('action=admin;area=autorespond;sa=list');
	}
}