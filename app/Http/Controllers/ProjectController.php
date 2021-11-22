<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Utility;

class ProjectController extends Controller
{
  public function index(Request $request)
  {
    $workspace = Utility::getWorkspace($request);
    $projects = $workspace->projects()
      ->with('users')
      ->withCount('tasks')
      ->get();
    return Utility::response200($projects);
  }

  public function show(Request $request, $slug)
  {
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->with('milestones')
      ->with(['activities' => function ($query) {
        $query->limit(5);
      }])
      ->with('users')
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    return Utility::response200($project);
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => 'required',
      'description' => 'required',
      'start_date' => 'required|date',
      'end_date' => 'required|date',
    ]);

    $workspace = Utility::getWorkspace($request);
    $project = Project::create([
      'name' => $request->name,
      'slug' => Utility::createSlug('projects', $request->name, 0, $workspace->id),
      'description' => $request->description,
      'start_date' => $request->start_date,
      'end_date' => $request->end_date,
      'budget' => 0,
      'workspace_id' => $workspace->id,
      'created_by' => $request->user()->id,
    ]);

    $project->users()->attach($request->user()->id);
    if ($request->members) {
      foreach ($request->members as $member) {
        $project->users()->attach($member);
      }
    }
    return Utility::response200(null, $project->name . ' created successfully!');
  }

  public function update(Request $request, $slug)
  {
    $this->validate($request, [
      'name' => 'required',
      'description' => 'required',
      'budget' => 'required',
      'start_date' => 'required|date',
      'end_date' => 'required|date',
    ]);
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $project->update([
      'name' => $request->name,
      'slug' => Utility::createSlug('projects', $request->name, 0, $workspace->id),
      'description' => $request->description,
      'start_date' => $request->start_date,
      'end_date' => $request->end_date,
      'budget' => $request->budget,
      'workspace_id' => $workspace->id,
      'created_by' => $request->user()->id,
    ]);
    return Utility::response200(null, $project->name . ' updated successfully!');
  }

  public function assign(Request $request, $slug)
  {
    $this->validate($request, [
      'email' => 'required|email',
    ]);
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $user = $workspace->users()->where('email', $request->email)->first();
    if (!$user) {
      abort(404, 'User not found!');
    }
    if ($project->users()->get()->contains('email', $request->email)) {
      abort(404, 'User has already in this project!');
    }
    $project->users()->attach($user->id);
    return Utility::response200(null, $request->email . ' assigned successfully!');
  }

  public function massAssign(Request $request, $slug)
  {
    $this->validate($request, [
      'emails' => 'required',
    ]);
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $invited = '';
    $error = '';
    foreach ($request->emails as $email) {
      $user = $workspace->users()->where('email', $email)->first();
      if (!$user) {
        $error .= $email . " not found!\n";
      } elseif ($project->users()->get()->contains('email', $email)) {
        $error .= $email . " has already in project!\n";
      } else {
        $project->users()->attach($user->id);
        $invited .= $email . " assigned successfully!\n";
      }
    }
    return Utility::response200(null, $invited . $error);
  }

  public function unAssign(Request $request, $slug)
  {
    $this->validate($request, [
      'email' => 'required|email',
    ]);
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $user = $project->users()->where('email', $request->email)->first();
    if (!$user) {
      abort(404, 'User not found!');
    }
    $project->users()->detach($user->id);
    return Utility::response200(null, $request->email . ' unassigned successfully!');
  }

  public function destroy(Request $request, $slug)
  {
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $project->users()->detach($request->user()->id);
    if ($project->created_by == $request->user()->id) {
      $project->delete();
    }
    return Utility::response200(null, $project->name . ' removed successfully!');
  }
}
