<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// User
		$user = User::where('email', 'johndoe@gmail.com')->first();
		// Project
		$project = Project::where('user_id', $user->id)->first();

		if (!$project) return;

		$tasks = [
			[
				'title' => 'Design homepage mockup',
				'description' => 'Create initial design in Figma',
				'status' => 'completed',
				'priority' => 'high',
				'due_date' => now()->addDays(7),
				'completed_at' => now()->subDays(1)
			],
			[
				'title' => 'Implement authentication',
				'description' => 'Add user login and registration',
				'status' => 'in_progress',
				'priority' => 'urgent',
				'due_date' => now()->addDays(3)
			],
			[
				'title' => 'Setup CI/CD pipeline',
				'description' => 'Configure GitHub Actions',
				'status' => 'todo',
				'priority' => 'medium',
				'due_date' => now()->addDays(14)
			],
			[

				'title' => 'Write API documentation',
				'description' => 'Document all endpoints',
				'status' => 'todo',
				'priority' => 'low',
				'due_date' => now()->addDays(21)
			],
			[
				'title' => 'Performance optimization',
				'description' => 'Optimize database queries',
				'status' => 'todo',
				'priority' => 'high',
				'due_date' => now()->subDays(2) // Overdue!
			],
		];

		foreach ($tasks as $taskData) {
			$user->tasks()->create(array_merge($taskData, ['project_id'=>$project->id]));
		}
	}
}
