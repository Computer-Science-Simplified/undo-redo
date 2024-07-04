<?php

namespace App\Actions;

use App\Models\Todo;
use App\Models\User;
use Redis;

class StoreTodoAction
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

    public function undo(array $event): null
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event['data']['todo_id']);

        $todo->delete();

        return null;
    }
}
