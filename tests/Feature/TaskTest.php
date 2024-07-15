<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;

class TaskTest extends TestCase
{
   use RefreshDatabase;

   function auth() {
    $user = User::factory()->create();
    $token = auth()->login($user);
    return $token;
   }

   function test_list_tasks()  {
        $token = $this->auth();

        Task::factory()->count(10)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->get('/api/tasks');


        $response->assertStatus(200);
        $response->assertJsonCount(10);
   }

   function test_create_tasks()  {
        $token = $this->auth();

        $data = [
            "title"=> "task title",
            "description"=> "task description",
            "status"=> "pending",
            "due_date"=> "2024-07-15",

        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->post('/api/tasks',$data);

        
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', $data);
   }

    function test_show_tasks()  {
        $token = $this->auth();
        
        $task = Task::factory()->create();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->post("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJson($task->toArray());
    }

    public function test_update_task()
    {
        $token = $this->auth();
        $task = Task::factory()->create();
        $data = [
            "title"=> "task title",
            "description"=> "task description",
            'status' => 'in_progress',
            'due_date' => '2024-07-20'
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->put("/api/tasks/{$task->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', $data);
    }

    public function test_delete_task()
    {
        $token = $this->auth();
        $task = Task::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->delete("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

}
