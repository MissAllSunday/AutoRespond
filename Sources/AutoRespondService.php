<?php

namespace AutoRespond;
use AutoRespond\Controller\AdminController;
use OharaDBClass;

class AutoRespondService
{
	public function __construct()
	{
		global $sourcedir;

		require_once($sourcedir . '/Subs-Boards.php');
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

		foreach ($smcFunc['db_fetch_all']($request) as $row)
		{
			$data['entries'][$row['id']] = $row;
			$data['users'][] = $row['user_id'];
		}
		$smcFunc['db_free_result']($request);

		$data['users'] = $this->getUsersData($data['users']);

		return $data;
	}

	public function getBoards(): array
	{
		return getTreeOrder()['boards'];
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