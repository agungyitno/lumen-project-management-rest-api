<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
  public static function createSlug($table, $string, $id = 0, $workspace = null)
  {
    $slug = Str::slug($string);
    $numb = 2;
    $query = DB::table($table)->select()
      ->where('slug', 'like', $slug . '%')
      ->where('id', '<>', $id);
    $allSlugs = $query->get();
    if ($workspace) {
      $allSlugs = $query->where('workspace_id', $workspace)->get();
    }
    if (!$allSlugs->contains('slug', $slug)) {
      return $slug;
    }

    $newSlug = $slug . '-' . $numb;
    while ($allSlugs->contains('slug', $newSlug)) {
      $newSlug = $slug . '-' . $numb++;
    }

    return $newSlug;
  }

  public static function getWorkspace(Request $request)
  {
    $workspace = Workspace::where('id', $request->user()->current_workspace)->first();
    if (!$workspace) {
      abort(404, 'No active workspace!');
    }
    $checkUser = $workspace->users()->where('id', $request->user()->id)->first();
    if (!$checkUser) {
      abort(401, "You don't have access to this workspace!");
    }
    return $workspace;
  }

  public static function response200($data = [], $message = '')
  {
    return response()->json([
      'success' => true,
      'message' => $message,
      'data' => $data,
    ], 200);
  }

  public static function createActivity(Request $request, Project $project, $type, $name)
  {
    ActivityLog::create([
      'user_id' => $request->user()->id,
      'project_id' => $project->id,
      'log_type' => $type,
      'remark' => $request->user()->name . ' ' . Str::lower($type) . ' <b>' . $name . '</b>',
    ]);
  }
}
