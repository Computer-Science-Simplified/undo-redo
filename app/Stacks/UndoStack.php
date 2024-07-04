<?php

namespace App\Stacks;

use App\DataTransferObjects\Event\Event;
use App\Models\User;
use InvalidArgumentException;
use Redis;

class UndoStack implements Stack
{
    public function __construct(private Redis $redis)
    {
    }

    public function push(Event $event, User $user): void
    {
        $this->redis->lPush(
            'history:todos:' . $event->data->todo_id . ':undo:' . $user->id,
            json_encode($event),
        );
    }

    public function pop(int $todoId, User $user): Event
    {
        $eventJson = $this->redis->lPop('history:todos:' . $todoId . ':undo:' . $user->id);

        if (!$eventJson) {
            throw new InvalidArgumentException('Not found');
        }

        return Event::fromJson($eventJson);
    }
}
