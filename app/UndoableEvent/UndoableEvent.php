<?php

namespace App\UndoableEvent;

use App\Models\Todo;

readonly class UndoableEvent
{
    public function __construct(
        public string $action,
        public UndoableEventData $data,
    ) {}

    public static function create(string $action, ?Todo $todo): self
    {
        return self::fromArray([
            'action' => $action,
            'data' => [
                'todo_id' => $todo->id,
                'todo' => [
                    'before' => null,
                    'after' => $todo->toArray(),
                ],
            ],
        ]);
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            action: $data['action'],
            data: UndoableEventData::fromArray($data['data']),
        );
    }
}
