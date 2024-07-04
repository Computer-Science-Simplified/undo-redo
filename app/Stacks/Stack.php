<?php

namespace App\Stacks;

use App\DataTransferObjects\UndoableEvent\Event;
use App\Models\User;

interface Stack
{
    public function push(Event $event, User $user): void;

    public function pop(int $todoId, User $user): Event;
}
