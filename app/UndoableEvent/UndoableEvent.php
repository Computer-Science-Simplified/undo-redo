<?php

namespace App\UndoableEvent;

readonly class UndoableEvent
{
    public function __construct(
        public string $action,
        public UndoableEventData $data
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
