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
                'settings' => [$txt['AR_admin_settings']],
                'list' => [$txt['AR_admin_list']],
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

        $context['data'] = $this->service->getEntries();
    }

    public function delete(): void
    {
        // check the entry exists

        // delete the entry

        redirectexit(self::URL . ';sa=list');

    }

    public function add(): void
    {
        global $txt, $context, $scripturl, $sourcedir;

        // check if we need it
        // AutoRespondHeaders();

        $id = $_REQUEST['id'] ?? 0;
        $isEditing = !empty($id);
        $entry = $isEditing ? $this->service->getEntries([$id])['entries'][$id] : [];

        $context['data'] = [
            'entry' => (array) $entry,
            'boards' => $this->service->getBoards()
        ];
    }

    protected function setContext(string $action): void
    {
        global $context, $scripturl, $txt;

        $context['sub_action'] = $action;
        $context['sub_template'] = 'show_' . $action;
        $context['page_title'] = $txt['AR_admin_' . $action];
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