<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		// Fetch whole data query
		$query = $request->user()->projects()->with('tasks');

		// Continue query by filter data
		// status
		if ($request->has('status')) {
			$query->where('status', $request->input('status'));
		}

		// search
		if ($request->has('search')) {
			$query->where('name', 'ilike', "%{$request->input('search')}%");
		}

		// overdue - option to include overdue tasks
		if ($request->boolean('overdue')) {
			// request->boolean('overdue') is similar to request->input('overdue')
			$query->where('deadline', '<', now())
				->where('status', '!=', 'completed');
		}

		// sort
		$sortBy = $request->input('sort_by', 'created_at');
		$sortOrder = $request->input('sort_order', 'desc');
		$query->orderBy($sortBy, $sortOrder);

		// Fetch query
		$projects = $query->get();

		// Response
		// For single model, use new ProjectResource()
		// But Project is not single model, so for multiple models use ProjectResource::collection()
		return ProjectResource::collection($projects);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreProjectRequest $request)
	{
		// Fetch from FormRequest
		$validated = $request->validated();

		// Create query
		$project = $request->user()->projects()->create($validated);

		// Response
		return response()->json([
			'success' => true,
			'message' => 'Project created successfully',
			'data' => new ProjectResource($project) // Since this is single model, use new ProjectResource
		], 201);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Request $request, Project $project)
	{
		// Policy which we will create later
		// The gate receives both current authenticated user (auto inject) and specific model instance ($project) you want to authorize against
		Gate::authorize('view', $project);

		// Load
		$project->load(['tasks', 'user']);

		// Response
		return response()->json([
			'success' => true,
			'data' => new ProjectResource($project)
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateProjectRequest $request, Project $project)
	{
		// Policy create later
		Gate::authorize('update', $project);

		// FormRequest validation
		$validated = $request->validated();

		// Update query
		$project->update($validated);

		// response
		return response()->json([
			'success' => true,
			'message' => 'Project updated successfully',
			'data' => new ProjectResource($project->fresh())
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Project $project)
	{
		// Policy 
		Gate::authorize('delete', $project);

		// Delete query
		$project->delete();

		// Response 
		return response()->json([
			'success' => true,
			'message' => 'Project deleted successfully'
		]);
	}

	// Stats
	public function stats(Request $request)
	{
		$projects = $request->user()->projects();

		// you’d only use with() if you actually needed to eager load full project models (+ tasks) or their related data, not for simple aggregates.

		$stats = [
			'total_projects' => $projects->count(),
			'active_projects' => $projects->active()->count(),
			'completed_projects' => $projects->completed()->count(),
			'overdue_projects' => $projects
				->where('deadline', '<', now())
				->where('status', '!=', 'completed')
				->count()
		];

		// Response
		return response()->json([
			'success' => true,
			'stats' => $stats
		]);
	}

	// Deleted Projects
	public function trashed(Request $request)
	{
		// Retrieve trashed projects query
		$projects = $request->user()->projects()->onlyTrashed()->with('tasks')->get();

		// response
		return ProjectResource::collection($projects);
	}

	// Restore Deleted Projects
	public function restore(Request $request, $id)
	{
		$project = $request->user()->projects()->onlyTrashed()->findOrFail($id);
		$project->restore();

		// response
		return response()->json([
			'success'=>true,
			'message'=>'Project restored successfully',
			'data'=>new ProjectResource($project)
		]);
	}
}
