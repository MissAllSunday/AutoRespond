<?php

declare(strict_types=1);

/**
 * Auto respond mod (SMF)
 *
 * @package AutoRespond
 * @version 2.1.1
 * @author Michel Mendiola <suki@missallsunday.com>
 * @copyright Copyright (c) 2024  Michel Mendiola
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace AutoRespond;

class AutoRespondAdmin
{
    public const ACTIONS = [
        'settings',
        'list',
        'add',
        'delete',
    ];
    public const URL = 'action=admin;area=autorespond';
    public const NOT_EMPTY_VALUES = [
        'body',
        'board_id'
    ];
    private AutoRespondService $service;

    public function __construct(?AutoRespondService $autoRespondService = null)
    {
        global $sourcedir;

        // No DI :(
        require_once($sourcedir . '/AutoRespond/AutoRespondService.php');

        $this->service = $autoRespondService ?? new AutoRespondService();
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

        $action = isset($_REQUEST['sa']) && in_array($_REQUEST['sa'], self::ACTIONS, true) ?
            $this->service->sanitize($_REQUEST['sa']) : self::ACTIONS[0];

        $this->setContext($action);
        $this->{$action}();
    }

    public function settings(): void
    {
        global $txt, $context;

        $context['sub_template'] = 'show_settings';

        $config_vars = [
            ['check', 'AR_enable', 'subtext' => $txt['AR_enable_sub']],
            ['check', 'AR_update_post_count', 'subtext' => $txt['AR_update_post_count_sub']],
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

    public function list(): void
    {
        global $context;

        $context['data'] = $this->service->getEntries();
    }

    public function delete(): void
    {
        $id = $this->service->sanitize($_GET['id']);
        $message = 'AR_error_delete';

        if ($id) {
            $this->service->deleteEntries([$id]);
            $message = 'AR_form_success_delete';
        }

        $this->redirect($message);
    }

    public function add(): void
    {
        global $context;

        $id = isset($_GET['id']) ? $this->service->sanitize($_GET['id']) : 0;

        $context['data'] = [
            'entry' => $this->service->getEntry($id),
            'boards' => $this->service->getBoards(),
            'errors' => [],
            'id' => $id
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->service->sanitize($_POST);

            if ($this->validate($data)) {
                $this->save($data, $id);
            }
        }
    }

    protected function validate(array $data): bool
    {
        global $context;

        $context['data']['errors'] = array_values(array_diff(array_values(self::NOT_EMPTY_VALUES), array_keys($data)));

        return empty($context['data']['errors']);
    }

    protected function save(array $data, int $id): void
    {
        $call = $id ? 'update' : 'insert';

        $this->service->{$call}($data, $id);

        $this->redirect('AR_form_success_'. ($id ? 'edit' :  'add'));
    }

    protected function redirect(string $message = ''): void
    {
        $_SESSION['autorespond'] = $message;

        redirectexit(self::URL .';sa=list');
    }

    protected function setContext(string $action): void
    {
        global $context, $scripturl, $txt;

        $context['sub_action'] = $action;
        $context['sub_template'] = 'ar_show_' . $action;
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