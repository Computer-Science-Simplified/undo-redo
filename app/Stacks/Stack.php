<?php

namespace App\Stacks;

use App\DataTransferObjects\Event\Event;
use App\Models\User;

interface Stack
{
    public function push(Event $event, User $user): void;

    public function pop(int $todoId, User $user): Event;

    public function peek(int $todoId, User $user): Event;
}
