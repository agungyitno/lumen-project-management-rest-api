<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  protected $guarded = ['id'];

  public function users()
  {
    return $this->belongsToMany(User::class, 'users_projects');
  }

  public function milestones()
  {
    return $this->hasMany(Milestone::class);
  }

  public function tasks()
  {
    return $this->hasMany(Task::class);
  }

  public function activities()
  {
    return $this->hasMany(ActivityLog::class);
  }

  public function workspace()
  {
    return $this->belongsTo(Workspace::class);
  }
}
