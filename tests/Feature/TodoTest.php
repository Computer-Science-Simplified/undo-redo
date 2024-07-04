<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Redis::flushall();

        parent::tearDown();
    }

    #[Test]
    public function it_should_undo_and_redo_description_changes()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $todo = $this->createTodo('Test Todo');

        $this->updateDescription($todo, 'Test description');

        $todo = $this->getTodo($todo);

        $this->assertSame('Test description', $todo['description']);

        $this->undo($todo);

        $todo = $this->getTodo($todo);

        $this->assertNull($todo['description']);

        $this->redo($todo);

        $todo = $this->getTodo($todo);

        $this->assertSame('Test description', $todo['description']);

        $this->undo($todo);

        $this->undo($todo);

        $this->assertTodoMissing($todo);
    }

    #[Test]
    public function it_should_undo_and_redo_assignee_changes()
    {
        $user = User::factory()->create();

        $assignee = User::factory()->create();

        $this->actingAs($user);

        $todo = $this->createTodo('Test Todo');

        $this->updateAssignee($todo, $assignee);

        $todo = $this->getTodo($todo);

        $this->assertSame($assignee->id, $todo['assignee_id']);

        $this->undo($todo);

        $todo = $this->getTodo($todo);

        $this->assertNull($todo['assignee_id']);

        $this->redo($todo);

        $todo = $this->getTodo($todo);

        $this->assertSame($assignee->id, $todo['assignee_id']);

        $this->undo($todo);

        $this->undo($todo);

        $this->assertTodoMissing($todo);
    }

    #[Test]
    public function it_should_undo_and_redo_creating_a_todo()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $todo = $this->createTodo('Test Todo');

        $todo = $this->getTodo($todo);

        $this->assertSame('Test Todo', $todo['title']);

        $this->undo($todo);

        $this->assertTodoMissing($todo);

        $this->redo($todo);

        $todo = $this->getTodo($todo);

        $this->assertSame('Test Todo', $todo['title']);
    }

    private function updateDescription(array $todo, string $description): void
    {
        $this->patchJson(route('todos.update-description', ['todo' => $todo['id']]), [
            'description' => 'Test description',
        ]);
    }

    private function getTodo(array $todo): array
    {
        return $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->assertStatus(Response::HTTP_OK)
            ->json('data');
    }

    private function undo(array $todo): void
    {
        $this->patchJson(route('todos.undo', ['todoId' => $todo['id']]))
            ->assertStatus(Response::HTTP_OK);
    }

    private function redo(array $todo): void
    {
        $this->patchJson(route('todos.redo', ['todoId' => $todo['id']]));
    }

    private function updateAssignee(array $todo, User $assignee): void
    {
        $this->patchJson(route('todos.update-assignee', ['todo' => $todo['id']]), [
            'assignee_id' => $assignee->id,
        ]);
    }

    private function createTodo(string $title): array
    {
        return $this->postJson(route('todos.store'), [
            'title' => $title,
        ])
            ->assertStatus(Response::HTTP_CREATED)
            ->json('data');
    }

    private function assertTodoMissing(array $todo): void
    {
        $this->getJson(route('todos.show', ['todo' => $todo['id']]))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
