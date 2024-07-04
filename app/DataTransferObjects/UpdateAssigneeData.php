<?php

namespace App\DataTransferObjects;

use App\Models\Todo;
use App\Models\User;

readonly class UpdateAssigneeData
{
    public function __construct(
        public Todo $todo,
        public User $user,
        public User $assignee,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            todo: $data['todo'],
            user: $data['user'],
            assignee: $data['assignee'],
        );
    }
}
