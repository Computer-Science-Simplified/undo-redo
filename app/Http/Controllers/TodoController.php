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
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
    public function __construct(private HistoryStack $historyStack, private UndoStack $undoStack)
    {
    }

    public function store(Request $request, StoreTodoAction $action)
    {
        $todo = $action->execute([
            ...$request->all(),
            'user' => $request->user(),
        ]);

        return response([
            'data' => $todo,
        ], Response::HTTP_CREATED);
    }

    public function show(Todo $todo)
    {
        return response([
            'data' => $todo,
        ], Response::HTTP_OK);
    }

    public function updateDescription(Request $request, Todo $todo, UpdateDescriptionAction $action)
    {
        $action->execute([
            ...$request->all(),
            'user' => $request->user(),
            'todo' => $todo
        ]);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function updateAssignee(Request $request, Todo $todo, UpdateAssigneeAction $action)
    {
        $assignee = User::findOrFail($request->assignee_id);

        $action->execute([
            'assignee' => $assignee,
            'user' => $request->user(),
            'todo' => $todo,
        ]);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function undo(Request $request, int $todoId)
    {
        DB::transaction(function () use ($todoId, $request) {
            $event = $this->historyStack->peek($todoId, $request->user());

            /** @var Undoable $action */
            $action = app($event->action);

            $data = $action->undo($event, $request->user());

            $this->historyStack->pop($todoId, $request->user());

            return response([
                'data' => $data,
            ], Response::HTTP_OK);
        });
    }

    public function redo(Request $request, int $todoId)
    {
        DB::transaction(function () use ($todoId, $request) {
            $event = $this->undoStack->peek($todoId, $request->user());

            /** @var Undoable $action */
            $action = app($event->action);

            $data = $action->redo($event, $request->user());

            $this->undoStack->pop($todoId, $request->user());

            return response([
                'data' => $data,
            ], Response::HTTP_OK);
        });
    }
}
