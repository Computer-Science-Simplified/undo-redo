<?php

namespace App\Actions;

use App\DataTransferObjects\UpdateAssigneeData;
use App\Models\Todo;
use App\Models\User;
use App\Stacks\HistoryStack;
use App\Stacks\UndoStack;
use App\UndoableEvent\UndoableEvent;

class UpdateAssigneeAction implements Undoable
{
    public function __construct(
        private HistoryStack $historyStack,
        private UndoStack $undoStack,
    ) {}

    public function execute(array $data): Todo
    {
        $dto = UpdateAssigneeData::from($data);

        $oldTodo = $dto->todo->toArray();

        $dto->todo->assignee_id = $dto->assignee->id;

        $dto->todo->save();

        $event = UndoableEvent::fromArray([
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

    public function undo(UndoableEvent $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event->data->todo_id);

        $oldTodo = $todo->toArray();

        $todo->update($event->data->todo->before);

        $event = UndoableEvent::fromArray([
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

    public function redo(UndoableEvent $event, User $user): ?Todo
    {
        /** @var Todo $todo */
        $todo = Todo::findOrFail($event->data->todo_id);

        $oldTodo = $todo->toArray();

        $todo->update($event->data->todo->before);

        $event = UndoableEvent::fromArray([
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
