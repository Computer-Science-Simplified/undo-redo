<?php

namespace App\Actions;

use App\Models\Todo;
use App\Models\User;
use Redis;

class UpdateDescriptionAction
{
    public function __construct(private Redis $redis)
    {
    }

    public function execute(Todo $todo, User $user, string $description): void
    {
        $oldTodo = json_encode($todo->toArray());

        $todo->description = $description;

        $todo->save();

        $this->redis->rPush('history:todos:' . $user->id, json_encode([
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
}
