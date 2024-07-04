<?php

namespace App\Actions;

use App\Models\Todo;
use App\Models\User;
use App\UndoableEvent\UndoableEvent;

interface Undoable
{
    public function undo(UndoableEvent $event, User $user): ?Todo;

    public function redo(array $event, User $user): ?Todo;
}
