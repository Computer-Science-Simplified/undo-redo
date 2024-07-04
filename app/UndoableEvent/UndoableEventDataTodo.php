<?php

namespace App\UndoableEvent;

readonly class UndoableEventDataTodo
{
    public function __construct(
        public ?array $before,
        public ?array $after,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            before: $data['before'],
            after: $data['after'],
        );
    }
}
