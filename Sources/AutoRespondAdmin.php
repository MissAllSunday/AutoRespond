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
		global $txt, $context, $scripturl, $sourcedir;

        require_once($sourcedir . '/Subs-Editor.php');

		$id = !empty($_GET['id']) ? $_GET['id'] : 0;
		$isEditing = !empty($id);
        $pageTitle = $txt['AR_admin_add'];
        $data = [];
        $url = $scripturl. '?action=admin;area=autorespond;sa=add';
        $buttonText = $txt['AR_form_send_add'];
        $context['autorespond']['data'] = [
            'title' => '',
            'body' => '',
            'user_id' => '',
        ];

        $editorOptions = [
            'id' => 'autorespond',
            'value' => '',
            'height' => '175px',
            'width' => '100%',
            'labels' => [
                'post_button' => $buttonText,
            ],
            'preview_type' => 2,
            'required' => true,
        ];

        if ($isEditing) {
            $data = $this->service->getEntries()['entries'][$id];
            $pageTitle = sprintf($txt['AR_admin_edit'], $data['title']);
            $url = $url . ';id=' . $id;
            $buttonText = $txt['AR_form_send_edit'];
            $editorOptions['value'] = $data['body'];
            $editorOptions['labels']['post_button'] = $buttonText;
        }

        $context['post_box_name'] = $editorOptions['id'];
		$context['sub_template'] = 'auto_respond_add';
		$context['page_title'] = $pageTitle;
		$context['linktree'][] = [
			'url' => $url,
			'name' => $pageTitle,
        ];

        $context['autorespond'] = [
            'url' => $url,
            'data' => $data,
            'boards' => $this->service->getBoards()
        ];

        create_control_richedit($editorOptions);
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