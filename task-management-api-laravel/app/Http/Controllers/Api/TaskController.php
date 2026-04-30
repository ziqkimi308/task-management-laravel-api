<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		// Fetch query
        $query = $request->user()->tasks()->with(['project', 'user']);

		// Filter
		// project
		if ($request->has('project_id')) {
			$query->where('project_id', $request->input('project_id'));
		}

		// status
		if ($request->has('status')) {
			$query->where('status', $request->input('status'));
		}

		// priority
		if ($request->has('priority')) {
			$query->where('priority', $request->input('priority'));
		}

		// overdue
		if ($request->has('overdue')) {
			$query->overdue(); // This is a scope
		}

		// high_priority
		if ($request->has('high_priority')) {
			$query->highPriority(); // This is a scope
		}

		// search
		if ($request->has('search')) {
			$search = $request->input('search');
			$query->where(function($query) use ($search) {
				$query->where('title', 'ilike', "%{$search}%")
					->orWhere('description', 'ilike', "%{$search}");
			});
		}

		// sort
		$sortBy = $request->input('sort_by', 'created_at');
		$sortOrder = $request->input('sort_order', 'desc');
		$query->orderBy($sortBy, $sortOrder);

		// Get
		$tasks = $query->get();

		// Response
		return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        // Validation via policy
		// The thing is we need user_id for policy check, that's why we fetch task's parent which is project which contain user_id and sent to policy
		$project = Project::findOrFail($request->project_id);
		Gate::authorize('view', $project);

		// Validate task data
		$validated = $request->validated();

		// Create query
		$task = $request->user()->tasks()->create($validated);
		$task->load('project');

		// Response
		return response()->json([
			'success'=>true,
			'message'=>'Task created successfully',
			'data'=>new TaskResource($task)
		], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task)
    {
		// Policy validation
		// Since task is complete model, it has user_id already. So sent to policy
		Gate::authorize('view', $task);

		$task->load(['project', 'user']);

		return response()->json([
			'success'=>true,
			'data'=>new TaskResource($task)
		]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // Policy validation - verify user
		Gate::authorize('update', $task);

		// If user changing/updating the project the task was under
		if ($request->has('project_id')) {
			$project = Project::findOrFail($request->project_id);
			// Check by compare project's userid == current user id
			Gate::authorize('view', $project);
		}

		// Prepare update data
		$validated = $request->validated();

		// Update query
		$task->update($validated);

		// Response
		return response()->json([
			'success'=>true,
			'message'=>'Task updated successfully',
			'data'=>new TaskResource($task->fresh(['project']))
		]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
		// Policy validation
        Gate::authorize('delete', $task);

		// Delete query
		$task->delete();

		// response
		return response()->json([
			'success'=>true,
			'message'=>'Task deleted successfully'
		]);
    }

	// Bulk Update
	public function bulkUpdate(Request $request)
	{
		// Update data validation
		$validated = $request->validate([
			'task_ids'=>'required|array',
			'task_ids.*'=>'exists:tasks,id',
			'status'=>'required|in:todo,in_progress,completed'
		]);

		// Update query
		Task::whereIn('id', $validated['task_ids'])
			->where('user_id', $request->user()->id)
			->update(['status'=>$validated['status']]);

		// response
		return response()->json([
			'success'=>true,
			'message'=>'Tasks updated successfully'
		]);
	}
}
