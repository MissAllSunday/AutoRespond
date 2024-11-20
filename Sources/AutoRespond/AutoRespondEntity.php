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

class AutoRespondEntity
{
    public string $body = '';
    public string $title = '';
    public int $user_id = 1;
    public int $id = 0;
    public int $board_id = 0;

    public function __construct(array $entry = [])
    {
        $this->setEntry($entry);
    }

    public function setEntry(array $entry): void
    {
        foreach ($entry as $key => $value) {
            $setCall = 'set' . $this->snakeToCamel($key);
            $this->{$setCall}($value);
        }
    }

    public function getBoardId(): int
    {
        return $this->board_id;
    }

    public function setBoardId(int $boardId): void
    {
        $this->board_id = $boardId;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    private function snakeToCamel($input): string
    {
        return \lcfirst(\str_replace('_', '', \ucwords($input, '_')));
    }
}