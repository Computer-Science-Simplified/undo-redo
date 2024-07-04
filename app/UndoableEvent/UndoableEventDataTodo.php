<?php

namespace App\UndoableEvent;

readonly class UndoableEventDataTodo
{
    public function __construct(
        public ?array $before,
        public ?array $after
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            before: json_decode($data['before'], true),
            after: json_decode($data['after'], true),
        );
    }
}
