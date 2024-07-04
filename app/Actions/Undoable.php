<?php

namespace App\Actions;

use App\DataTransferObjects\UndoableEvent\UndoableEvent;
use App\Models\Todo;
use App\Models\User;

interface Undoable
{
    public function execute(array $data): ?Todo;

    public function undo(UndoableEvent $event, User $user): ?Todo;

    public function redo(UndoableEvent $event, User $user): ?Todo;
}
