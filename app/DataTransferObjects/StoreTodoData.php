<?php

namespace App\DataTransferObjects;

use App\Models\User;

readonly class StoreTodoData
{
    public function __construct(
        public string $title,
        public User $user,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            title: $data['title'],
            user: $data['user'],
        );
    }
}
