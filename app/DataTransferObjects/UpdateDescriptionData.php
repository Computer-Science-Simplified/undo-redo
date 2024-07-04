<?php

namespace App\DataTransferObjects;

use App\Models\Todo;
use App\Models\User;

readonly class UpdateDescriptionData
{
    public function __construct(
        public Todo $todo,
        public string $description,
        public User $user,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            todo: $data['todo'],
            description: $data['description'],
            user: $data['user'],
        );
    }
}
