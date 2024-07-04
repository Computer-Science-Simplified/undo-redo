<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_should_undo_and_redo_description_changes()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $todo = $this->postJson(route('todos.store'), [
            'title' => 'Test Todo',
        ])->json('data');

        $this->patchJson(route('todos.update-description', ['todo' => $todo['id']]), [
            'description' => 'Test description',
        ]);

        $todo = $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->json('data');

        $this->assertSame('Test description', $todo['description']);

        $this->patchJson(route('todos.undo', ['todoId' => $todo['id']]));

        $todo = $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->json('data');

        $this->assertNull($todo['description']);

        $this->patchJson(route('todos.redo', ['todoId' => $todo['id']]));

        $todo = $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->json('data');

        $this->assertSame('Test description', $todo['description']);
    }

    #[Test]
    public function it_should_undo_and_redo_assignee_changes()
    {
        $user = User::factory()->create();

        $assignee = User::factory()->create();

        $this->actingAs($user);

        $todo = $this->postJson(route('todos.store'), [
            'title' => 'Test Todo',
        ])->json('data');

        $this->patchJson(route('todos.update-assignee', ['todo' => $todo['id']]), [
            'assignee_id' => $assignee->id,
        ]);

        $todo = $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->json('data');

        $this->assertSame($assignee->id, $todo['assignee_id']);

        $this->patchJson(route('todos.undo', ['todoId' => $todo['id']]));

        $todo = $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->json('data');

        $this->assertNull($todo['assignee_id']);

        $this->patchJson(route('todos.redo', ['todoId' => $todo['id']]));

        $todo = $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->json('data');

        $this->assertSame($assignee->id, $todo['assignee_id']);
    }

    #[Test]
    public function it_should_undo_and_redo_creating_a_todo()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $todo = $this->postJson(route('todos.store'), [
            'title' => 'Test Todo',
        ])->json('data');

        $todo = $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->json('data');

        $this->assertSame('Test Todo', $todo['title']);

        $this->patchJson(route('todos.undo', ['todoId' => $todo['id']]));

        $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->assertStatus(Response::HTTP_NOT_FOUND);

        $this->patchJson(route('todos.redo', ['todoId' => $todo['id']]));

        $todo = $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->assertStatus(Response::HTTP_OK)
            ->json('data');

        $this->assertSame('Test Todo', $todo['title']);
    }
}
