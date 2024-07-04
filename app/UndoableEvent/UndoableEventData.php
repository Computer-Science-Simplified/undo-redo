<?php

namespace App\UndoableEvent;

readonly class UndoableEventData
{
    public function __construct(
        public int $todo_id,
        public UndoableEventDataTodo $todo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            todo_id: $data['todo_id'],
            todo: UndoableEventDataTodo::fromArray($data['todo']),
        );
    }
}
