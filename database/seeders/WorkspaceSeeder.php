<?php

namespace Database\Seeders;

use App\Models\Utility;
use App\Models\Workspace;
use Illuminate\Database\Seeder;

class WorkspaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = [
            'Web Development',
            'App Development'
        ];
        foreach ($names as $name) {
            $workspace = Workspace::create([
                'name' => $name,
                'slug' => Utility::createSlug('workspaces', $name),
                'created_by' => 1,
            ]);

            $workspace->users()->attach(1, ['role' => 'Owner']);
        }
    }
}
