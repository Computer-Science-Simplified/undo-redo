<?php

namespace App\Stacks;

use App\Models\Todo;
use App\Models\User;
use App\UndoableEvent\UndoableEvent;
use InvalidArgumentException;
use Redis;

class UndoStack implements Stack
{
    public function __construct(private Redis $redis)
    {
    }

    public function push(UndoableEvent $event, User $user): void
    {
        $this->redis->lPush(
            'history:todos:' . $event->data->todo_id . ':undo:' . $user->id,
            json_encode($event),
        );
    }

    public function pop(int $todoId, User $user): UndoableEvent
    {
        $eventJson = $this->redis->lPop('history:todos:' . $todoId . ':undo:' . $user->id);

        if (!$eventJson) {
            throw new InvalidArgumentException('Not found');
        }

        return UndoableEvent::fromJson($eventJson);
    }
}
