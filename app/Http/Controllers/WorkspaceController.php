<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WorkspaceController extends Controller
{
  public function index(Request $request)
  {
    $workspaces = $request->user()->workspaces()->get();
    if (count($workspaces) <= 0) {
      abort(404, "You don't have workspace");
    }
    return Utility::response200($workspaces);
  }

  public function show(Request $request, $slug)
  {
    $workspace = $request->user()->workspaces()->where('slug', $slug)->first();
    return Utility::response200($workspace);
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => 'required',
    ]);
    $workspace = Workspace::create([
      'name' => $request->name,
      'slug' => Utility::createSlug('workspaces', $request->name),
      'created_by' => $request->user()->id,
    ]);
    if (!$workspace) {
      abort(404, 'Create workspace failed!');
    }
    $workspace->users()->attach($request->user()->id, ['role' => 'Owner']);
    return Utility::response200($workspace, $workspace->name . ' workspace created successfully!');
  }

  public function update(Request $request, $slug)
  {
    $this->validate($request, [
      'name' => 'required',
    ]);
    $workspace = Workspace::where('slug', $slug)->first();
    if (!$workspace) {
      abort(404, 'Workspace not found!');
    }
    if (!$workspace->users()->where('id', $request->user()->id)->exists()) {
      abort(401, "You don't have access to this workspace!");
    }
    $workspace->update([
      'name' => $request->name,
      'slug' => Utility::createSlug('workspaces', $request->name, $workspace->id),
    ]);
    if (!$workspace) {
      abort(404, 'Update workspace failed!');
    }
    return Utility::response200($workspace, $workspace->name . ' workspace updated successfully!');
  }

  public function destroy(Request $request, $slug)
  {
    $workspace = Workspace::where('slug', $slug)->first();
    if (!$workspace) {
      abort(404, 'Workspace not found!');
    }
    if (!$workspace->users()->where('id', $request->user()->id)->exists()) {
      abort(401, "You don't have access to this workspace!");
    }
    $workspace->delete();
    return Utility::response200(null, $workspace->name . ' workspace deleted successfully!');
  }

  public function change(Request $request, $slug)
  {
    $workspace = Workspace::where('slug', $slug)->first();
    if (!$workspace) {
      abort(404, 'Workspace not found!');
    }
    if (!$workspace->users()->where('id', $request->user()->id)->exists()) {
      abort(401, "You don't have access to this workspace!");
    }
    $request->user()->update([
      'current_workspace' => $workspace->id
    ]);
    return Utility::response200(null, $workspace->name . ' workspace actived successfully!');
  }
}
