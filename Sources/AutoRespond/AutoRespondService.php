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

class AutoRespondService
{
	public function __construct()
	{
		global $sourcedir;

		require_once($sourcedir . '/Subs-Boards.php');
	}

	public function deleteEntries(array $autoRespondIds = []): void
	{
		global $smcFunc;

		if (empty($autoRespondIds)) {
			return;
		}

		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}log_boards
			WHERE id_board IN ({array_int:autoRespondIds})',
			['autoRespondIds' => $autoRespondIds]
		);
	}

	public function getEntries(array $autoRespondIds = []): array
	{
		global $smcFunc;

		$data = ['users', 'entries'];

		if (empty($autoRespondIds)) {
			return $data;
		}

		$request = $smcFunc['db_query']('', '
		SELECT id, board_id, user_id, title, body
		FROM {db_prefix}autorespond AS c
		'. (!empty($autoRespondIds) ? 'WHERE id IN ({array_int:autoRespondIds})' : '') .  '
		ORDER BY {raw:sort}',
			[
				'sort' => 'id',
				'autoRespondIds' => $autoRespondIds
			]
		);

		return $this->prepareData($smcFunc['db_fetch_all']($request));
	}

	public function getEntriesByBoard(int $boardId = 0): array
	{
		global $smcFunc;

		if (empty($boardId)) {
			return [];
		}

		$request = $smcFunc['db_query']('', '
		SELECT id, board_id, user_id, title, body
		FROM {db_prefix}autorespond AS c
		WHERE find_in_set("{int:boardId}",board_id) <> 0
		ORDER BY {raw:sort}',
			[
				'sort' => 'id',
				'boardId' => $boardId
			]
		);

		return $this->prepareData($smcFunc['db_fetch_all']($request));
	}

	public function getBoards(): array
	{
		return getTreeOrder()['boards'];
	}

	protected function prepareData($request) : array
	{
		global $smcFunc;

		$data = ['users', 'entries'];

		foreach ($smcFunc['db_fetch_all']($request) as $row)
		{
			$data['entries'][$row['id']] = $row;
			$data['users'][] = $row['user_id'];
		}
		$smcFunc['db_free_result']($request);

		$data['users'] = $this->getUsersData($data['users']);

		return $data;
	}

	protected function getUsersData($usersIds = []) : array
	{
		global $memberContext;

		loadMemberData($usersIds);

		$usersData = [];

		foreach ($usersIds as $id) {
			$usersData[$id] = $memberContext[$id];
		}

		return $usersData;
	}

}