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

class AutoRespond
{
    public function getAllBoards(): array
    {
        global $sourcedir;

        require_once($sourcedir . '/Subs-Boards.php');

        return getTreeOrder()['boards'];
    }

    public function getEntry(): AutoRespondEntity
    {
        return new AutoRespondEntity();
    }
}