<?php

namespace App\Http\Controllers;

use App\Actions\StoreTodoAction;
use App\Actions\Undoable;
use App\Actions\UpdateAssigneeAction;
use App\Actions\UpdateDescriptionAction;
use App\Models\Todo;
use App\Models\User;
use App\Stacks\HistoryStack;
use App\Stacks\UndoStack;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
    public function __construct(private HistoryStack $historyStack, private UndoStack $undoStack)
    {
    }

    public function store(Request $request, StoreTodoAction $action)
    {
        $todo = $action->execute($request->title, $request->user());

        return response([
            'data' => $todo,
        ], Response::HTTP_CREATED);
    }

    public function updateDescription(Request $request, Todo $todo, UpdateDescriptionAction $action)
    {
        $action->execute($todo, $request->user(), $request->description);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function updateAssignee(Request $request, Todo $todo, UpdateAssigneeAction $action)
    {
        $assignee = User::findOrFail($request->assignee_id);

        $action->execute($todo, $request->user(), $assignee);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function undo(Request $request, int $todoId)
    {
        $event = $this->historyStack->pop($todoId, $request->user());

        /** @var Undoable $action */
        $action = app($event->action);

        $newTodo = $action->undo($event, $request->user());

        return response([
            'data' => $newTodo,
        ], Response::HTTP_OK);
    }

    public function redo(Request $request, int $todoId)
    {
        $event = $this->undoStack->pop($todoId, $request->user());

        /** @var Undoable $action */
        $action = app($event->action);

        $newTodo = $action->redo($event, $request->user());

        return response([
            'data' => $newTodo,
        ], Response::HTTP_OK);
    }
}
