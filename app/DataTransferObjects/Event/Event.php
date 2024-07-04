<?php

namespace App\DataTransferObjects\Event;

use App\Models\Todo;

readonly class Event
{
    public function __construct(
        public string    $action,
        public EventData $data,
    ) {}

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            action: $data['action'],
            data: EventData::fromArray($data['data']),
        );
    }
}
