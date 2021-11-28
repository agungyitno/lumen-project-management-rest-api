<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Utility;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        if (!$token = Auth::attempt(array($fieldType => $request->username, 'password' => $request->password))) {
            abort(401, ['error' => 'Unauthorized']);
        }
        return $this->respondWithToken($token);
    }

    public function me(Request $request)
    {
        $user = Auth::user();
        $active = $request->user()->workspaces()->where('id', $user->current_workspace)->first();
        $data = [
            'user' => $user,
            'active' => $active
        ];
        return Utility::response200($data);
        return response()->json($user, 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function emailRequestVerification(Request $request)
    {
        // return response()->json($request->user()->hasVerifiedEmail());
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('Email address is already verified.');
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json('Email request verification sent to ' . Auth::user()->email);
    }
    public function emailVerify(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
        ]);
        \Tymon\JWTAuth\Facades\JWTAuth::getToken();
        \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
        if (!$request->user()) {
            return response()->json('Invalid token', 401);
        }

        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('Email address ' . $request->user()->getEmailForVerification() . ' is already verified.');
        }
        $request->user()->markEmailAsVerified();
        return response()->json('Email address ' . $request->user()->email . ' successfully verified.');
    }
}
