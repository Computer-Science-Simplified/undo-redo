<?php

namespace App\Stacks;

use App\Models\User;
use App\UndoableEvent\UndoableEvent;

interface Stack
{
    public function push(UndoableEvent $event, User $user): void;

    public function pop(int $todoId, User $user): UndoableEvent;
}
