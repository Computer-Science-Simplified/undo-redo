<?php

namespace App\UndoableEvent;

class UndoableEventDataTodo
{
    public function __construct(
        public readonly ?array $before,
        public readonly ?array $after
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            before: json_decode($data['before'], true),
            after: json_decode($data['after'], true),
        );
    }
}
