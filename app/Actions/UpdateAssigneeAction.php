<?php

namespace App\Actions;

use App\Models\Todo;
use App\Models\User;
use App\UndoableEvent\UndoableEvent;
use Redis;

class UpdateAssigneeAction implements Undoable
{
    public function __construct(private Redis $redis)
    {
    }

    public function execute(Todo $todo, User $user, User $assignee): void
    {
        $oldTodo = $todo->toArray();

        $todo->assignee_id = $assignee->id;

        $todo->save();

        $this->redis->lPush('history:todos:' . $todo->id . ':' . $user->id, json_encode([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => $todo->toArray(),
                ],
            ],
        ]));
    }

    public function undo(UndoableEvent $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event->data->todo_id);

        $oldTodo = $todo->toArray();

        $todo->update($event->data->todo->before);

        $this->redis->lPush('history:todos:' . $todo->id . ':undo:' . $user->id, json_encode([
            'action' => self::class,
            'command' => 'undo',
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => $todo->toArray(),
                ],
            ],
        ]));

        return $todo;
    }

    public function redo(UndoableEvent $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event->data->todo_id);

        $oldTodo = $todo->toArray();

        $todo->update($event->data->todo->before);

        $this->redis->lPush('history:todos:' . $todo->id . ':' . $user->id, json_encode([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => $todo->toArray(),
                ],
            ],
        ]));

        return $todo;
    }
}
