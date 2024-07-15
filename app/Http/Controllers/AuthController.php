<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    function register(Request $request) {

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'), 201);
    }

    function login(Request $request) {

        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        if (!$token = JWTAuth::attempt($request->only('email','password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(compact('token'));
    }
}
