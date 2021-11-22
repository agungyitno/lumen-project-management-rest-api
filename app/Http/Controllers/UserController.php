<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Utility;
use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $workspace = Utility::getWorkspace($request);
    $users = $workspace->users()
      ->withCount('projects')
      ->withCount('tasks')
      ->get();
    return Utility::response200($users);
  }

  public function avatar(Request $request,$username)
  {
    $avatar_path = storage_path('/avatar/'.$username.'.png');
    if (!file_exists($avatar_path)) {
      $def_path = storage_path('/avatar/default.png');
      $file = file_get_contents($def_path);
      return response($file,200)->header('Content-Type','image/png');
    }
    $file = file_get_contents($avatar_path);
    return response($file,200)->header('Content-Type','image/png');
  }

  public function invite(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email',
    ]);
    $workspace = Utility::getWorkspace($request);
    $user = User::where('email', $request->email)->first();
    if (!$user) {
      abort(404, 'User not found!');
    }
    if ($workspace->users()->where('id', $user->id)->exists()) {
      abort(404, 'User has already in workspace!');
    }
    $workspace->users()->attach($user->id, ['role' => 'Member']);
    return Utility::response200(null, $user->email . ' invited successfully!');
  }

  public function destroy(Request $request, $id)
  {

    $workspace = Utility::getWorkspace($request);
    $user = $workspace->users()->where('id', $id)->first();
    if (!$user) {
      abort(404, 'User not found!');
    }
    $workspace->users()->detach($user->id);
    return Utility::response200(null, $user->email . ' removed successfully!');
  }
}
