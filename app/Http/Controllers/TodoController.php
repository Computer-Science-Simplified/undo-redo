<?php

namespace App\Http\Controllers;

use App\Actions\StoreTodoAction;
use App\Actions\UpdateDescriptionAction;
use App\Models\Todo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
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
}
