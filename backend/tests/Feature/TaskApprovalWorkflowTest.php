<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;
    protected User $employee;
    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        // Create manager
        $this->manager = User::factory()->create([
            'name' => 'Manager',
        ]);

        // Create employee reporting to manager
        $this->employee = User::factory()->create([
            'name' => 'Employee',
            'manager_id' => $this->manager->id,
        ]);

        // Create task assigned to employee, created by manager
        $this->task = Task::factory()->create([
            'creator_id' => $this->manager->id,
            'assignee_id' => $this->employee->id,
            'status' => Task::STATUS_IN_PROGRESS,
            'title' => ['en' => 'Test Task'],
        ]);
    }

    public function test_assignee_can_submit_task_for_approval(): void
    {
        $response = $this->actingAs($this->employee, 'api')
            ->postJson("/api/v1/tasks/{$this->task->id}/submit-for-approval");

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Task submitted for approval',
            ]);

        $this->task->refresh();
        $this->assertEquals(Task::STATUS_PENDING_REVIEW, $this->task->status);
        $this->assertEquals(Task::APPROVAL_PENDING, $this->task->approval_status);
        $this->assertNotNull($this->task->submitted_at);
    }

    public function test_non_assignee_cannot_submit_task_for_approval(): void
    {
        $response = $this->actingAs($this->manager, 'api')
            ->postJson("/api/v1/tasks/{$this->task->id}/submit-for-approval");

        $response->assertForbidden();
    }

    public function test_cannot_submit_task_not_in_progress(): void
    {
        $this->task->update(['status' => Task::STATUS_PENDING]);

        $response = $this->actingAs($this->employee, 'api')
            ->postJson("/api/v1/tasks/{$this->task->id}/submit-for-approval");

        $response->assertStatus(422);
    }

    public function test_manager_can_approve_task(): void
    {
        // First submit for approval
        $this->task->submitForApproval();

        $response = $this->actingAs($this->manager, 'api')
            ->postJson("/api/v1/tasks/{$this->task->id}/approve");

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Task approved successfully',
            ]);

        $this->task->refresh();
        $this->assertEquals(Task::STATUS_COMPLETED, $this->task->status);
        $this->assertEquals(Task::APPROVAL_APPROVED, $this->task->approval_status);
        $this->assertEquals($this->manager->id, $this->task->approved_by);
        $this->assertNotNull($this->task->approved_at);
    }

    public function test_non_manager_cannot_approve_task(): void
    {
        $this->task->submitForApproval();

        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser, 'api')
            ->postJson("/api/v1/tasks/{$this->task->id}/approve");

        $response->assertForbidden();
    }

    public function test_manager_can_request_revision(): void
    {
        $this->task->submitForApproval();

        $response = $this->actingAs($this->manager, 'api')
            ->postJson("/api/v1/tasks/{$this->task->id}/request-revision", [
                'reason' => 'Please add more details to the report',
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Revision requested',
            ]);

        $this->task->refresh();
        $this->assertEquals(Task::STATUS_IN_PROGRESS, $this->task->status);
        $this->assertEquals(Task::APPROVAL_REJECTED, $this->task->approval_status);
        $this->assertEquals(1, $this->task->revision_count);
        $this->assertCount(1, $this->task->revision_notes);
    }

    public function test_request_revision_requires_reason(): void
    {
        $this->task->submitForApproval();

        $response = $this->actingAs($this->manager, 'api')
            ->postJson("/api/v1/tasks/{$this->task->id}/request-revision", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['reason']);
    }

    public function test_revision_deadline_updates_due_date(): void
    {
        $this->task->submitForApproval();
        $newDeadline = now()->addDays(3)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->manager, 'api')
            ->postJson("/api/v1/tasks/{$this->task->id}/request-revision", [
                'reason' => 'Needs more work',
                'revision_deadline' => $newDeadline,
            ]);

        $response->assertOk();

        $this->task->refresh();
        $this->assertEquals(
            now()->addDays(3)->format('Y-m-d'),
            $this->task->due_date->format('Y-m-d')
        );
    }

    public function test_pending_approvals_returns_tasks_for_manager(): void
    {
        $this->task->submitForApproval();

        $response = $this->actingAs($this->manager, 'api')
            ->getJson('/api/v1/tasks/pending-approvals');

        $response->assertOk()
            ->assertJsonPath('data.data.0.id', $this->task->id);
    }

    public function test_pending_approvals_excludes_others_tasks(): void
    {
        $this->task->submitForApproval();

        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser, 'api')
            ->getJson('/api/v1/tasks/pending-approvals');

        $response->assertOk()
            ->assertJsonPath('data.data', []);
    }
}
