<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated=$request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|unique:users,email',
            'password'=>'required',
            'user_code'=>'unique'
        ]);

        $user=User::create([
            'user_code'=>Str::random(5),
            'first_name'=>$validated['first_name'],
            'last_name'=>$validated['last_name'],
            'email'=>$validated['email'],
            'password'=>Hash::make($validated['password'])
        ]);

        $user->save();
        return response()->json(['message'=>'User has been created.'],200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:5'
        ]);

        $user=User::where('email',request('email'))->first();

        $credentials=request(['email','password']);

        if(!Auth::attempt($credentials))
        {
            return response()->json(['message'=>'Unauthorized'],401);
        }
        $user=$request->user();
        return response()->json(['data'=>[
            'user'=>Auth::user()
        ]]);


    }
}
