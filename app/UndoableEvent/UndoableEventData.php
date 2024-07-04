<?php

namespace App\UndoableEvent;

class UndoableEventData
{
    public function __construct(
        public readonly int $todo_id,
        public readonly UndoableEventDataTodo $todo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            todo_id: $data['todo_id'],
            todo: UndoableEventDataTodo::fromArray($data['todo']),
        );
    }
}
