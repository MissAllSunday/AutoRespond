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

class AutoRespond
{
    private AutoRespondService $service;
    private static int $alreadyCreatedTopicId = 0;
    private string $msgOptionsSubject = '';
    private string $posterOptionsName = '';

    public function __construct(?AutoRespondService $autoRespondService = null)
    {
        global $sourcedir;

        // No DI :(
        require_once($sourcedir . '/AutoRespond/AutoRespondService.php');

        $this->service = $autoRespondService ?? new AutoRespondService();
    }
    public function handleRespond(array $msgOptions, array $topicOptions, array $posterOptions): void
    {
        global $sourcedir;

        $topicId = (int) $topicOptions['id'];

        if (!$this->service->isModEnable() ||
            $this->isRecursive($topicId) ||
            !empty($topicOptions['redirect_topic'])) {
            return;
        }

        $this->msgOptionsSubject = $msgOptions['subject'];
        $this->posterOptionsName = $posterOptions['name'];

        $data = $this->service->getEntriesByBoard((int) $topicOptions['board']);

        require_once($sourcedir . '/Subs-Post.php');

        foreach ($data['entries'] as $entry)  {
            $this->createResponse($entry, $topicId);
        }
    }

    public function createResponse(AutoRespondEntity $entry, int $topic): void
    {
        global $modSettings;

        $newMsgOptions = [
            'id' => 0,
            'subject' => $this->setSubject($entry),
            'body' => $this->setBody($entry),
            'icon' => 'xx',
            'smileys_enabled' => 1,
            'attachments' => [],
        ];

        $newTopicOptions = [
            'id' => $topic,
            'board' => $entry->getBoardId(),
            'poll' => null,
            'lock_mode' => !empty($modSettings['AR_lock_topic_after']) ? 1 : null,
            'sticky_mode' => null,
            'mark_as_read' => false,
        ];

        $newPosterOptions = array(
            'id' => $this->getPosterId($entry),
            'name' => '',
            'email' => '',
            'update_post_count' => !empty($modSettings['AR_update_post_count']) ? 1 : 0,
            'ip' => $this->getPosterIp(),
        );

        createPost($newMsgOptions, $newTopicOptions, $newPosterOptions);

        self::$alreadyCreatedTopicId = $topic;
    }

    protected function setSubject(AutoRespondEntity $entry): string
    {
        return empty($entry->getTitle()) ? $this->msgOptionsSubject : $entry->getTitle();
    }

    protected function setBody(AutoRespondEntity $entry): string
    {
        return un_preparsecode($this->parseMessage($entry->getBody()));
    }

    protected function getPosterId(AutoRespondEntity $entry): int
    {
        $userID = $entry->getUserId();

        return $userID ?: AutoRespondService::DEFAULT_POSTER_ID;
    }

    protected function getPosterIp(): string
    {
        global $modSettings;

        return !empty($modSettings['AR_dummy_ip']) ? AutoRespondService::DEFAULT_POSTER_IP : '';
    }

    protected function parseMessage(string $messageBody) : string
    {
        $replacements = [
            'TOPIC_POSTER' => $this->posterOptionsName,
            'POSTED_TIME' => date("F j, Y, g:i a"),
            'TOPIC_SUBJECT' => $this->msgOptionsSubject,
        ];

        /* Split the replacements up into two arrays, for use with str_replace */
        $find = [];
        $replace = [];

        foreach ($replacements as $f => $r)
        {
            $find[] = '{' . $f . '}';
            $replace[] = $r;
        }

        /* Do the variable replacements. */
        return str_replace($find, $replace, $messageBody);
    }

    protected function isRecursive(int $topicId): bool
    {
        return self::$alreadyCreatedTopicId === $topicId;
    }
}