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

class AutoRespondAdmin
{
    const ACTIONS = [
        'settings',
        'list',
        'add',
        'delete',
    ];
    const URL = 'action=admin;area=autorespond';

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

        $context['page_title'] = $txt['AR_admin_panel'];
        $context[$context['admin_menu_name']]['tab_data'] = [
            'title' => $txt['AR_admin_panel'],
            'description' => $txt['AR_admin_panel_desc'],
            'tabs' => [
                'settings' => []
            ],
        ];
        $context['sub_template'] = 'show_settings';
        $action = isset($_REQUEST['sa']) && array_search($_REQUEST['sa'], self::ACTIONS) ?
            $_REQUEST['sa'] : self::ACTIONS[0];
        $context['sub_action'] = $action;

        $this->{$action}();
    }

    public function settings(): void
    {
        global $txt, $scripturl, $context;

        $config_vars = [
            ['check', 'AR_enable', 'subtext' => $txt['AR_enable_sub']],
            ['check', 'AR_update_post_count', 'subtext' => $txt['AR_update_post_count_sub']],
            ['check', 'AR_use_title', 'subtext' => $txt['AR_use_title_sub']],
            ['check', 'AR_lock_topic_after', 'subtext' => $txt['AR_lock_topic_after_sub']],
            ['check', 'AR_dummy_ip', 'subtext' => $txt['AR_dummy_ip_sub']],
        ];

        $context['post_url'] = $scripturl . '?' . self::URL .';save';
        $context['settings_title'] = $txt['AR_admin_panel'];
        $context['page_title'] = $txt['AR_admin_panel'];
        $context['sub_template'] = 'show_settings';

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

        // todo: get all entries
        $context['GetARList'] = [];
        $context['sub_template'] = 'auto_respond_list';
        $context['page_title'] = $txt['AR_admin_list'];
        $context['linktree'][] = [
            'url' => $scripturl. '?'. self::URL .';sa=list',
            'name' => $txt['AR_admin_list'],
        ];
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