<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User test
		$user = User::create([
			'name'=>'John Doe',
			'email'=>'johndoe@gmail.com',
			'password'=>'password123'
		]);

		// Project test
		$projects = [
			[
				'name' => 'Website Redesign',
				'description' => 'Complete overhaul of company website',
				'status' => 'active',
				'color' => '#3B82F6',
				'deadline' => now()->addDays(30)
			],
			[
				'name' => 'Mobile App Development',
				'description' => 'Build iOS and Android apps',
				'status' => 'active',
				'color' => '#10B981',
				'deadline' => now()->addDays(60)
			],
			[
				'name' => 'Database Migration',
				'description' => 'Migrate to new database system',
				'status' => 'completed',
				'color' => '#8B5CF6',
				'deadline' => now()->subDays(5)
			],
		];

		foreach ($projects as $projectData) {
			$user->projects()->create($projectData);
		}
    }
}
