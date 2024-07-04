<?php

namespace App\Stacks;

use App\DataTransferObjects\UndoableEvent\UndoableEvent;
use App\Models\User;

interface Stack
{
    public function push(UndoableEvent $event, User $user): void;

    public function pop(int $todoId, User $user): UndoableEvent;
}
