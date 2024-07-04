<?php

namespace App\Http\Controllers;

use App\Actions\StoreTodoAction;
use App\Actions\UpdateDescriptionAction;
use App\Models\Todo;
use Illuminate\Http\Request;
use Redis;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
    public function __construct(private Redis $redis)
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

    public function undo(Request $request, Todo $todo)
    {
        $event = $this->redis->lPop('history:todos:' . $todo->id . ':' . $request->user()->id);

        if (!$event) {
            return response('', Response::HTTP_NOT_FOUND);
        }

        $event = json_decode($event, true);

        $action = app($event['action']);

        $newTodo = $action->undo($event);

        return response([
            'data' => $newTodo,
        ], Response::HTTP_OK);
    }
}
