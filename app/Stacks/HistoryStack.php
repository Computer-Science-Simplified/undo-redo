<?php

namespace App\Stacks;

use App\DataTransferObjects\Event\Event;
use App\Models\User;
use InvalidArgumentException;
use Redis;

class HistoryStack implements Stack
{
    public function __construct(private Redis $redis)
    {
    }

    public function push(Event $event, User $user): void
    {
        $this->redis->lPush(
            'history:todos:' . $event->data->todo_id . ':' . $user->id,
            json_encode($event),
        );
    }

    public function pop(int $todoId, User $user): Event
    {
        $eventJson = $this->redis->lPop('history:todos:' . $todoId . ':' . $user->id);

        if (!$eventJson) {
            throw new InvalidArgumentException('Not found');
        }

        return Event::fromJson($eventJson);
    }

    public function peek(int $todoId, User $user): Event
    {
        $eventJson = $this->redis->lIndex('history:todos:' . $todoId . ':' . $user->id, 0);

        if (!$eventJson) {
            throw new InvalidArgumentException('Not found');
        }

        return Event::fromJson($eventJson);
    }
}
