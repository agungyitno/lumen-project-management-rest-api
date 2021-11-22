<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
  public function index(Request $request)
  {
    $workspace = Utility::getWorkspace($request);
    $tasks = Task::with('project')->get();
    return Utility::response200($tasks);
  }
  public function taskBoard(Request $request, $slug)
  {
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $tasks = $project->tasks()->with('users')->get();
    return Utility::response200($tasks);
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => 'required',
      'priority' => 'required',
      'start_date' => 'required|date',
      'end_date' => 'required|date',
    ]);
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('id', $request->projectId)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $project->tasks()->create([
      'name' => $request->name,
      'priority' => $request->priority,
      'description' => $request->description,
      'start_date' => $request->start_date,
      'end_date' => $request->end_date,
      'milestone_id' => $request->mileston,
    ]);
  }
}
