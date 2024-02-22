<?php

namespace AutoRespond;

class AutoRespondEntity
{
    public string $body = '';
    public string $title = '';
    public int $user_id = 0;
    public int $id = 0;
    public int $board_id = 0;

    public function __construct(array $entry = [])
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