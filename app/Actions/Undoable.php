<?php

namespace App\Actions;

use App\DataTransferObjects\Event\Event;
use App\Models\Todo;
use App\Models\User;

interface Undoable
{
    public function execute(array $data): ?Todo;

    public function undo(Event $event, User $user): ?Todo;

    public function redo(Event $event, User $user): ?Todo;
}
