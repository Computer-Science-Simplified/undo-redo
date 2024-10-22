<?php

namespace App\Actions;

use App\DataTransferObjects\Todo\StoreTodoData;
use App\DataTransferObjects\Event\Event;
use App\Models\Todo;
use App\Models\User;
use App\Stacks\HistoryStack;
use App\Stacks\UndoStack;

class StoreTodoAction implements Undoable
{
    public function __construct(
        private HistoryStack $historyStack,
        private UndoStack $undoStack,
    ) {}

    public function execute(array $data): Todo
    {
        $dto = StoreTodoData::from($data);

        $todo = Todo::create([
            'title' => $dto->title,
            'user_id' => $dto->user->id,
        ]);

        $event = Event::fromArray([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => null,
                    'after' => $todo->toArray(),
                ],
            ],
        ]);

        $this->historyStack->push($event, $dto->user);

        return $todo;
    }

    public function undo(Event $event, User $user): null
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event->data->todo_id);

        $oldTodo = $todo->toArray();

        $todo->delete();

        $event = Event::fromArray([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => null,
                ],
            ],
        ]);

        $this->undoStack->push($event, $user);

        return null;
    }

    public function redo(Event $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::create(
            $event->data->todo->before,
        );

        $event = Event::fromArray([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => null,
                    'after' => $todo->toArray(),
                ],
            ],
        ]);

        $this->historyStack->push($event, $user);

        return $todo;
    }
}
