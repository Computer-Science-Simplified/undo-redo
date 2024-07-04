<?php

namespace App\Actions;

use App\DataTransferObjects\Todo\UpdateDescriptionData;
use App\DataTransferObjects\Event\Event;
use App\Models\Todo;
use App\Models\User;
use App\Stacks\HistoryStack;
use App\Stacks\UndoStack;

class UpdateDescriptionAction implements Undoable
{
    public function __construct(
        private HistoryStack $historyStack,
        private UndoStack $undoStack
    ) {}

    public function execute(array $data): Todo
    {
        $dto = UpdateDescriptionData::from($data);

        $oldTodo = $dto->todo->toArray();

        $dto->todo->description = $dto->description;

        $dto->todo->save();

        $event = Event::fromArray([
            'action' => self::class,
            'data' => [
                'todo_id' => $dto->todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => $dto->todo->toArray(),
                ],
            ],
        ]);

        $this->historyStack->push($event, $dto->user);

        return $dto->todo;
    }

    public function undo(Event $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event->data->todo_id);

        $oldTodo = $todo->toArray();

        $todo->update($event->data->todo->before);

        $event = Event::fromArray([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => $todo->toArray(),
                ],
            ],
        ]);

        $this->undoStack->push($event, $user);

        return $todo;
    }

    public function redo(Event $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event->data->todo_id);

        $oldTodo = $todo->toArray();

        $todo->update($event->data->todo->before);

        $event = Event::fromArray([
            'action' => self::class,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => $oldTodo,
                    'after' => $todo->toArray(),
                ],
            ],
        ]);

        $this->historyStack->push($event, $user);

        return $todo;
    }
}
