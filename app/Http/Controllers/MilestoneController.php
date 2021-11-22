<?php 
namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MilestoneController extends Controller
{
  public function store(Request $request, $slug)
  {
    $this->validate($request,[
      'title' => 'required',
      'cost' => 'required|numeric',
      'status' => 'required',
    ]);
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $mile = $project->milestones()->create([
      'title' => $request->title,
      'cost' => $request->cost,
      'status' => $request->status,
      'summary' => $request->summary,
    ]);
    Utility::createActivity($request,$project,'Create Milestone',$mile->title);
    return Utility::response200(null, $mile->title . ' created successfully!');
  }
  public function update(Request $request, $slug, $id)
  {
    $this->validate($request,[
      'title' => 'required',
      'cost' => 'required|numeric',
      'status' => 'required',
    ]);
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $mile = $project->milestones()->where('id',$id)->first();
    if (!$mile) {
      abort(404, 'Milestone not found!');
    }
    $mile->update([
      'title' => $request->title,
      'cost' => $request->cost,
      'status' => $request->status,
      'summary' => $request->summary,
    ]);
    Utility::createActivity($request,$project,'Update Milestone',$mile->title);
    return Utility::response200(null, $mile->title . ' updated successfully!');
  }
  public function destroy(Request $request, $slug, $id)
  {
    $workspace = Utility::getWorkspace($request);
    $project = $workspace->projects()->where('slug', $slug)
      ->first();
    if (!$project) {
      abort(404, 'Project not found!');
    }
    $mile = $project->milestones()->where('id',$id)->first();
    if (!$mile) {
      abort(404, 'Milestone not found!');
    }
    $mile->delete();
    Utility::createActivity($request,$project,'Delete Milestone',$mile->title);
    return Utility::response200(null, $mile->title . ' deleted successfully!');
  }
}
