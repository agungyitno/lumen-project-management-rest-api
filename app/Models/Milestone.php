<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
  protected $guarded = ['id'];

  public function tasks()
  {
    return $this->hasMany(Task::class);
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }
}
