<?php

namespace App\UndoableEvent;

class UndoableEvent
{
    public function __construct(
        public readonly string $action,
        public readonly UndoableEventData $data
    ) {}

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(
            action: $data['action'],
            data: UndoableEventData::fromArray($data['data']),
        );
    }
}
