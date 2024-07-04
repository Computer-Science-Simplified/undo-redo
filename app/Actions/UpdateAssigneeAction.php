<?php

namespace App\Actions;

use App\Models\Todo;
use App\Models\User;
use Redis;

class UpdateAssigneeAction
{
    public function __construct(private Redis $redis)
    {
    }

    public function execute(Todo $todo, User $user, User $assignee): void
    {
        $oldTodo = json_encode($todo->toArray());

        $todo->assignee_id = $assignee->id;

        $todo->save();

        $this->redis->lPush('history:todos:' . $todo->id . ':' . $user->id, json_encode([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => json_encode($todo->toArray()),
                ],
            ],
        ]));
    }

    public function undo(array $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event['data']['todo_id']);

        $oldTodo = json_encode($todo->toArray());

        $todo->update(json_decode($event['data']['todo']['before'], true));

        $this->redis->lPush('history:todos:' . $todo->id . ':undo:' . $user->id, json_encode([
            'action' => self::class,
            'command' => 'undo',
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => json_encode($todo->toArray()),
                ],
            ],
        ]));

        return $todo;
    }

    public function redo(array $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event['data']['todo_id']);

        $oldTodo = json_encode($todo->toArray());

        $todo->update(json_decode($event['data']['todo']['before'], true));

        $this->redis->lPush('history:todos:' . $todo->id . ':' . $user->id, json_encode([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => json_encode($todo->toArray()),
                ],
            ],
        ]));

        return $todo;
    }
}