<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = [
            'Project Management System',
            'School Management System',
        ];
        foreach ($names as $name) {
            $project = Project::create([
                'name' => $name,
                'slug' => Utility::createSlug('projects', $name, 0, 1),
                'description' => 'Simple description',
                'start_date' => Carbon::now()->toDate(),
                'end_date' => Carbon::now()->addDays(30)->toDate(),
                'workspace_id' => 1,
                'created_by' => 1,
            ]);

            $project->users()->attach(1);

            $mile = $project->milestones()->create([
                'title' => 'Mile 1',
                'cost' => 10000
            ]);

            $task = $project->tasks()->create([
                'name' => 'Define system workflow',
                'priority' => 'Medium',
                'description' => 'Define system workflow',
                'start_date' => Carbon::now()->toDate(),
                'end_date' => Carbon::now()->addDays(30)->toDate(),
                'milestone_id' => $mile->id
            ]);
            $task->users()->attach(1);
        }
    }
}
