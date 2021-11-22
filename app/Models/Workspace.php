<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
  protected $guarded = ['id'];

  public function users()
  {
    return $this->belongsToMany(User::class, 'users_workspaces')->withPivot('role');
  }

  public function projects()
  {
    return $this->hasMany(Project::class);
  }
}
