<?php

declare(strict_types=1);

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

use AutoRespond\AutoRespondService as AutoRespondService;

class AutoRespondAdmin
{
    const ACTIONS = [
        'settings',
        'list',
        'add',
        'delete',
    ];
    const URL = 'action=admin;area=autorespond';
    private AutoRespondService $service;

    public function __construct()
    {
        global $sourcedir;

        // No DI :(
        require_once($sourcedir . '/AutoRespond/AutoRespondService.php');

        $this->service = new AutoRespondService();
    }

    public function menu(&$admin_areas): void
    {
        global $txt;

        $this->loadRequiredFiles();

        $admin_areas['config']['areas']['autorespond'] = [
            'label' => $txt['AR_menu'],
            'function' => [$this, 'main'],
            'icon' => 'posts.gif',
            'subsections' => [
                'settings' => [$txt['AR_basic_settings']],
                'list' => [$txt['AR_list_page']],
                'add' => [$txt['AR_admin_add']],
            ],
        ];
    }

    public function main(): void
    {
        global $txt, $context;

        $context[$context['admin_menu_name']]['tab_data'] = [
            'title' => $txt['AR_admin_panel'],
            'description' => $txt['AR_admin_panel_desc'],
            'tabs' => [
                self::ACTIONS[0] => []
            ],
        ];

        $action = isset($_REQUEST['sa']) && array_search($_REQUEST['sa'], self::ACTIONS) ?
            $_REQUEST['sa'] : self::ACTIONS[0];

        $this->setContext($action);
        $this->{$action}();
    }

    public function settings(): void
    {
        global $txt;

        $config_vars = [
            ['check', 'AR_enable', 'subtext' => $txt['AR_enable_sub']],
            ['check', 'AR_update_post_count', 'subtext' => $txt['AR_update_post_count_sub']],
            ['check', 'AR_use_title', 'subtext' => $txt['AR_use_title_sub']],
            ['check', 'AR_lock_topic_after', 'subtext' => $txt['AR_lock_topic_after_sub']],
            ['check', 'AR_dummy_ip', 'subtext' => $txt['AR_dummy_ip_sub']],
        ];

        if (isset($_GET['save']))
        {
            checkSession();
            saveDBSettings($config_vars);
            redirectexit(self::URL);
        }

        prepareDBSettingContext($config_vars);
    }

    function list(): void
    {
        global $txt, $context, $scripturl, $sourcedir;

        $context['entries'] = $this->service->getEntries();
        $context['sub_template'] = 'auto_respond_list';
        $context['page_title'] = $txt['AR_admin_list'];
        $context['linktree'][] = [
            'url' => $scripturl. '?'. self::URL .';sa=list',
            'name' => $txt['AR_admin_list'],
        ];
    }

    public function delete(): void
    {
        // check the entry exists

        // delete the entry

        redirectexit(self::URL . ';sa=list');

    }

    public function add()
    {
        global $txt, $context, $scripturl, $sourcedir;

        // check if we need it
        // AutoRespondHeaders();

        $context['sub_template'] = 'auto_respond_add';
        $context['page_title'] = $txt['AR_admin_adding'];
        $context['linktree'][] = array(
            'url' => $scripturl. '?action=admin;area=autorespond;sa=add',
            'name' => $txt['AR_admin_adding'],
        );

        /* This are empty...nobody knows why... (rolleyes) */
        $context['autorespond']['add'] = [
            'board_id' => [],
            'body' => '',
            'title' => '',
            'user_id' => '',
            'id' => ''
        ];

        /* Load all boards */
        $context['autorespond']['boards'] = [];
    }

    protected function setContext(string $action): void
    {
        global $context, $scripturl, $txt;

        $context['sub_action'] = $action;
        $context['sub_template'] = 'show_' . $action;
        $context['page_title'] = $txt['AR_admin_' . $action];
        $context['linktree'][] = [
            'url' => $scripturl. '?' . self::URL . ';sa=' . $action,
            'name' => $txt['AR_admin_' . $action],
        ];
        $context['post_url'] = $scripturl . '?' . self::URL .';save';
        $context['settings_title'] = $context['page_title'];
    }

    protected function loadRequiredFiles(): void
    {
        global $sourcedir;

        isAllowedTo('admin_forum');

        loadLanguage('AutoRespond');
        loadtemplate('AutoRespond');

        require_once($sourcedir . '/ManageSettings.php');
        require_once($sourcedir . '/ManageServer.php');
    }
}