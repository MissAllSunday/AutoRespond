<?php

class AutoRespondAdmin
{
	public function __construct()
	{
		loadLanguage('AutoRespond');
		loadtemplate('AutoRespond');
	}

	public function addAreas(&$admin_areas): void
	{
		global $txt;

		$admin_areas['config']['areas']['autorespond'] = [
			'label' => $txt['AR_menu'],
			'file' => 'AutoRespond.php',
			'function' => 'ModifyAutoRespondSettings',
			'icon' => 'posts.gif',
			'subsections' => [
				'basic' => [$txt['AR_basic_settings']],
				'list' => [$txt['AR_list_page']],
				'add' => [$txt['AR_admin_add']],
			],
		];
	}

	function addSettings($return_config = false)
	{
		global $txt, $scripturl, $context, $sourcedir;

		isAllowedTo('admin_forum');

		require_once($sourcedir . '/ManageSettings.php');

		$context['page_title'] = $txt['AR_admin_panel'];

		$subActions = [
			'basic' => 'BasicAutoRespondSettings',
			'list' => 'AutoRespondListPage',
			'add' => 'AutoRespondAdd',
			'delete' => 'AutoRespondDelete'
		];

		loadGeneralSettingParameters($subActions, 'basic');

		// Load up all the tabs...
		$context[$context['admin_menu_name']]['tab_data'] = [
			'title' => $txt['AR_admin_panel'],
			'description' => $txt['AR_admin_panel_desc'],
			'tabs' => [
				'basic' => []
			],
		];

		call_user_func($subActions[$_REQUEST['sa']]);
	}

	function settings($return_config = false): void
	{
		global $txt, $scripturl, $context, $sourcedir;

		isAllowedTo('admin_forum');

		require_once($sourcedir . '/ManageServer.php');

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

	function AutoRespondListPage()
	{
		global $txt, $context, $scripturl, $sourcedir;

		/* I can has Adminz? */
		isAllowedTo('admin_forum');
		loadLanguage('AutoRespond');
		loadtemplate('AutoRespond');

		require_once($sourcedir . '/OharaDB.class.php');


		/* Prepare the query */
		$params = array(
			'rows' =>'id, board_id, user_id, title, body',
			'order' => '{raw:sort}',
		);
		$data = array(
			'sort' => 'id',
		);
		$query = new OharaDBClass('autorespond');
		$query->Params($params, $data);
		$query->GetData('id');

		/* Store the result in context to handle it better */
		$context['GetARList'] = $query->data_result;

		/* Set some stuff for the page */
		$context['sub_template'] = 'auto_respond_list';
		$context['page_title'] = $txt['AR_admin_list'];
		$context['linktree'][] = array(
			'url' => $scripturl. '?action=admin;area=autorespond;sa=list',
			'name' => $txt['AR_admin_list'],
		);
	}

	protected function add()
	{
		global $txt, $context, $scripturl, $sourcedir;

		AutoRespondHeaders();

		$context['sub_template'] = 'auto_respond_add';
		$context['page_title'] = $txt['AR_admin_adding'];
		$context['linktree'][] = array(
			'url' => $scripturl. '?action=admin;area=autorespond;sa=add',
			'name' => $txt['AR_admin_adding'],
		);

		/* This are empty...nobody knows why... (rolleyes) */
		$context['autorespond']['add'] = array(
			'board_id' => array(),
			'body' => '',
			'title' => '',
			'user_id' => '',
			'id' => ''
		);

		/* Load all boards */
		$context['autorespond']['boards'] = AutoRespondBoards();
	}

	protected function delete() {

		global $sourcedir;

		require_once($sourcedir . '/OharaDB.class.php');

		$validation = AutoRespondValidate();

		/* Safety first! */
		if (isset($_REQUEST['arid']) && in_array($_REQUEST['arid'], array_keys($validation)))
		{
			$params = array(
				'where' => 'id = {int:id}'
			);

			$data = array(
				'id' => $_REQUEST['arid']
			);
			$deletedata = new OharaDBClass('autorespond');
			$deletedata->Params($params, $data);
			$deletedata->DeleteData();

			redirectexit('action=admin;area=autorespond;sa=list');
		}
	}
}