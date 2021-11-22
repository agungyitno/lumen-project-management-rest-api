<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  protected $guarded = ['id'];

  public function users()
  {
    return $this->belongsToMany(User::class, 'users_tasks');
  }
  public function project()
  {
    return $this->belongsTo(Project::class);
  }
  public function milestone()
  {
    return $this->belongsTo(Milestone::class);
  }
}
