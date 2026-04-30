<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAuthRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
	// Register new user
	public function register(RegisterAuthRequest $request)
	{
		// Call the FormRequest for validation
		$validated = $request->validated();

		// Register query
		$user = User::create($validated);
		$token = $user->createToken('auth_token')->plainTextToken;

		// Response
		return response()->json([
			'success'=>true,
			'message'=>'User registered successfully',
			// Use Resource instead of returning user as it is
			'user'=> new UserResource($user),
			'token'=>$token
		], 201);
	}

	// Login user
	public function login(Request $request)
	{
		// User validation
		$validated = $request->validate([
			'email'=>'required|email',
			'password'=>'required'
		]);

		// Login query
		$user = User::where('email', $validated['email'])->first();

		if (!$user || !Hash::check($validated['password'], $user->password)) {
			throw ValidationException::withMessages([
				'email'=>['The provided credentials are incorrect.']
			]);
		}

		// Renew token
		$user->tokens()->delete();
		$token = $user->createToken('auth_token')->plainTextToken;

		// Response
		return response()->json([
			'success'=>true,
			'message'=>'Login successful',
			'user'=>new UserResource($user),
			'token'=>$token
		]);
	}

	// Logout User
	public function logout(Request $request)
	{
		// Delete token
		$request->user()->currentAccessToken()->delete();

		return response()->json([
			'success'=>true,
			'message'=>'Logged out successfully'
		]);
	}

	// Show current user
	public function me(Request $request)
	{
		return response()->json([
			'success'=>true,
			'user'=>new UserResource($request->user())
		]);
	}

	// Dashboard Endpoint
	public function dashboard(Request $request)
	{
		$user = $request->user();

		$stats = [
			'project'=>[
				'total'=>$user->projects()->count(),
				'active'=>$user->projects()->active()->count(),
				'completed'=>$user->projects()->completed()->count()
			],
			'tasks'=>[
				'total' => $user->tasks()->count(),
				'pending' => $user->tasks()->pending()->count(),
				'completed' => $user->tasks()->completed()->count(),
				'overdue' => $user->tasks()->overdue()->count(),
				'high_priority' => $user->tasks()->highPriority()->count(),
			],
			'recent_tasks'=>TaskResource::collection(
				$user->tasks()->with('project')->orderBy('created_at', 'desc')
					->limit(5)->get()
			),
			'upcoming_deadlines'=>TaskResource::collection(
				$user->tasks()->with('project')->whereNotNull('due_date')
					->where('due_date', '>=', now())
					->where('status', '!=', 'completed')
					->orderBy('due_date', 'asc')
					->limit(5)
					->get()
			)
		];

		// response
		return response()->json([
			'success'=>true,
			'data'=>$stats
		]);
	}
}
