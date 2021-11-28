<?php

namespace App\Models;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as AuthCanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail as AuthMustVerifyEmail;

class User extends Model implements AuthenticatableContract, JWTSubject, AuthMustVerifyEmail, AuthCanResetPassword
{
    use Authenticatable, HasFactory, Notifiable, MustVerifyEmail, CanResetPassword;

    // Non Fillable Column
    protected $guarded = ['id'];

    // Hidden Column
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'current_workspace');
    }
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'users_workspaces')->withPivot('role');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'users_projects');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'users_tasks');
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            /**
             * If user email have changed email verification is required
             */
            if ($model->isDirty('email')) {
                $model->setAttribute('email_verified_at', null);
                $model->sendEmailVerificationNotification();
            }
        });
    }
}
