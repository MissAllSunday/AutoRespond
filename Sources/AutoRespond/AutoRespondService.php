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

class AutoRespondService
{
    public const TABLE = 'autorespond';

    public const DEFAULT_POSTER_ID = 1;
    public const DEFAULT_POSTER_IP = '127.0.0.1';

    public function __construct()
    {
        global $sourcedir;

        // No DI :(
        require_once($sourcedir . '/AutoRespond/AutoRespondEntity.php');
    }
    public function isModEnable(): bool
    {
        global $modSettings;

        return !empty($modSettings['AR_enable']);
    }

    public function insert(array $data) : void
    {
        global $smcFunc;

        $smcFunc['db_insert']('insert',
            '{db_prefix}'. self::TABLE,
            ['user_id' => 'int', 'title' => 'string', 'board_id' => 'string', 'body' => 'string'],
            $this->formatData($data),
            ['id']
        );
    }

    public function update(array $data, int $id): void
    {
        global $smcFunc;

        $data = $this->formatData($data);

        $smcFunc['db_query']('', '
			UPDATE {db_prefix}'. self::TABLE .'
			SET board_id = {string:board_id},
			    user_id = {int:user_id},
			    title = {string:title},
			    body = {string:body}
			WHERE id = {int:id}',
            [
                'board_id' => $data['board_id'],
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'body' => $data['body'],
                'id' => $id,
            ]
        );
    }

    public function deleteEntries(array $autoRespondIds = []): void
    {
        global $smcFunc;

        if (empty($autoRespondIds)) {
            return;
        }

        $smcFunc['db_query']('', '
			DELETE FROM {db_prefix}'. self::TABLE .'
			WHERE id IN ({array_int:autoRespondIds})',
            ['autoRespondIds' => $autoRespondIds]
        );
    }

    public function getEntries(array $autoRespondIds = []): array
    {
        global $smcFunc;

        return $this->prepareData($smcFunc['db_query']('', '
		SELECT id, board_id, user_id, title, body
		FROM {db_prefix}autorespond AS c
		'. (!empty($autoRespondIds) ? 'WHERE id IN ({array_int:autoRespondIds})' : '') .  '
		ORDER BY {raw:sort}',
            [
                'sort' => 'id',
                'autoRespondIds' => $autoRespondIds
            ]
        ));
    }

    public function getEntry(int $id): AutoRespondEntity
    {
        global $smcFunc;
        $entry = new AutoRespondEntity();

        if (empty($id)) {
            return $entry;
        }

        $request = $smcFunc['db_query']('', '
		SELECT id, board_id, user_id, title, body
		FROM {db_prefix}autorespond
		WHERE id = {int:id}
		ORDER BY {raw:sort}',
            [
                'sort' => 'id',
                'id' => $id
            ]
        );

        [$data] = $smcFunc['db_fetch_all']($request);
        $smcFunc['db_free_result']($request);

        $entry->setEntry($data);

        return $entry;
    }

    public function getEntriesByBoard(int $boardId = 0): array
    {
        global $smcFunc;

        if (empty($boardId)) {
            return [];
        }

        return $this->prepareData($smcFunc['db_query']('', '
            SELECT id, board_id, user_id, title, body
            FROM {db_prefix}autorespond
            WHERE find_in_set("{int:boardId}",board_id) <> 0
            ORDER BY {raw:sort}', [
            'sort' => 'id',
            'boardId' => $boardId
        ]));
    }

    public function getBoards(): array
    {
        global $sourcedir, $boards;

        require_once($sourcedir . '/Subs-Boards.php');

        getBoardTree();

        return $boards;
    }

    public function sanitize($variable)
    {
        global $smcFunc;

        if (is_array($variable)) {
            foreach ($variable as $key => $variableValue) {
                $variable[$key] = $this->sanitize($variableValue);
            }

            return array_filter($variable);
        }

        $var = $smcFunc['htmlspecialchars'](
            $smcFunc['htmltrim']((string) $variable),
            \ENT_QUOTES
        );

        if (ctype_digit($var)) {
            $var = (int) $var;
        }

        return $var;
    }

    protected function prepareData($request) : array
    {
        global $smcFunc;

        $data['users'] = [];
        $data['entries'] = [];

        foreach ($smcFunc['db_fetch_all']($request) as $row)
        {
            $data['entries'][$row['id']] = new AutoRespondEntity($row);
            $data['users'][] = $row['user_id'];
        }
        $smcFunc['db_free_result']($request);

        $data['users'] = $this->getUsersData($data['users']);

        return $data;
    }

    protected function getUsersData($usersIds = []) : array
    {
        global $memberContext;

        $usersIds = loadMemberData(array_unique($usersIds));
        $usersData = [];

        foreach ($usersIds as $userId) {
            loadMemberContext($userId);
            $usersData[$userId] = $memberContext[$userId];
        }

        return $usersData;
    }

    protected function formatData(array $data): array
    {
        $data = array_merge(['user_id' => self::DEFAULT_POSTER_ID], $data);
        $data['board_id'] = implode(',', $data['board_id']);
        array_pop($data);

        return $data;
    }
}