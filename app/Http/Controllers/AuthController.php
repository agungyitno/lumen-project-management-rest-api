<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        //
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users|min:6',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8|confirmed',
            'name' => 'required',
            'agree' => 'accepted',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Register Success!', 'data' => $user], 201);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($fieldType, $request->username)->first();
        if (!$user) {
            return response()->json(['message' => 'Username or email not registered!'], 404);
        }
        $isValidPassword = Hash::check($request->password, $user->password);
        if (!$isValidPassword) {
            return response()->json(['message' => 'Invalid Password!'], 404);
        }

        $token = $user->createToken();

        return response()->json(['message' => 'Logged in', 'token' => $token], 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $active = $request->user()->workspaces()->where('id',$request->user()->current_workspace)->first();
        return response()->json([
            'user' => $user,
            'activeWorkspace' => $active
        ], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        if (User::removeToken($token)) {
            return response()->json(['message' => 'Logged out'], 200);
        }
    }
}
