<?php

namespace App\Actions;

use App\Models\Todo;
use App\Models\User;
use Redis;

class StoreTodoAction implements Undoable
{
    public function __construct(private Redis $redis)
    {
    }

    public function execute(string $title, User $user): Todo
    {
        $todo = Todo::create([
            'title' => $title,
            'user_id' => $user->id,
        ]);

        $this->redis->lPush('history:todos:' . $todo->id . ':' . $user->id, json_encode([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => null,
                    'after' => json_encode($todo->toArray()),
                ],
            ],
        ]));

        return $todo;
    }

    public function undo(array $event, User $user): null
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event['data']['todo_id']);

        $oldTodo = json_encode($todo);

        $todo->delete();

        $this->redis->lPush('history:todos:' . $todo->id . ':undo:' . $user->id, json_encode([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => null,
                ],
            ],
        ]));

        return null;
    }

    public function redo(array $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::create(
            json_decode($event['data']['todo']['before'], true),
        );

        $this->redis->lPush('history:todos:' . $todo->id . ':undo:' . $user->id, json_encode([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => null,
                    'after' => json_encode($todo),
                ],
            ],
        ]));

        return $todo;
    }
}
