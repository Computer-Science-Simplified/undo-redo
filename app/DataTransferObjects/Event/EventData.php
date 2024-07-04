<?php

namespace App\DataTransferObjects\Event;

readonly class EventData
{
    public function __construct(
        public int           $todo_id,
        public EventDataTodo $todo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            todo_id: $data['todo_id'],
            todo: EventDataTodo::fromArray($data['todo']),
        );
    }
}
