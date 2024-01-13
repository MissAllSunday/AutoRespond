<?php

class DataService
{
	public function validate()
	{
		global $sourcedir;

		require_once($sourcedir . '/OharaDB.class.php');

		/* Prepare the query */
		$params = array(
			'rows' =>'id'
		);
		$query = new OharaDBClass('autorespond');
		$query->Params($params);
		$query->GetData('id');

		$return = array();

		if (!empty($query->data_result))
			$return = $query->data_result;

		return $return;
	}

	/* Don't wash your dirty laundry in public... */
	function sanitize($item, $body = false)
	{
		global $smcFunc, $sourcedir;

		$item = $smcFunc['htmlspecialchars']($item, ENT_QUOTES);
		$item = $smcFunc['htmltrim']($item, ENT_QUOTES);
		$item = censorText($item);

		if ($body)
		{
			require_once($sourcedir . '/Subs-Post.php');
			preparsecode($item);
		}

		return $item;
	}
}