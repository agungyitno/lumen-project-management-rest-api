<?php

namespace App\Models;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    // Non Fillable Column
    protected $guarded = ['id'];

    // Hidden Column
    protected $hidden = [
        'password',
    ];

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

    public function createToken()
    {
        $token = sha1(time());
        $insertToken = DB::table('access_tokens')->insert([
            'user_id' => $this->id,
            'token' => $token,
            'expired' => Carbon::now()->addDays(30)->toDateTime()
        ]);
        if ($insertToken) {
            return $token;
        }
    }

    public static function checkToken($token)
    {
        $token = explode(' ', $token);
        if ($token[0] != 'Bearer') {
            return false;
        }
        $isToken = DB::table('access_tokens')->where('token', $token[1])->first();
        if (!$isToken) {
            return false;
        }
        $isValid = Carbon::now()->lessThan($isToken->expired);
        if (!$isValid) {
            return false;
        }
        return User::where('id', $isToken->user_id)->first();
    }

    public static function removeToken($token)
    {
        $token = explode(' ', $token);
        if ($token[0] != 'Bearer') {
            return response()->json(['invalid Token'], 401);
        }
        $isToken = DB::table('access_tokens')->where('token', $token[1])->delete();
        if (!$isToken) {
            return ['Invalid Token'];
        }
        return true;
    }
}
