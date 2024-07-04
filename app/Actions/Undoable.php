<?php

namespace App\Actions;

use App\Models\Todo;
use App\Models\User;

interface Undoable
{
    public function undo(array $event, User $user): ?Todo;

    public function redo(array $event, User $user): ?Todo;
}
