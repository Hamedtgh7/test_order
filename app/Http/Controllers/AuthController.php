<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data=$request->validate([
            'name'=>'required|max:30',
            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);

        $user=User::query()->create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password'])
        ]);

        Auth::login($user);

        $token=$user->createToken('StoreManager')->plainTextToken;

        return response()->json([
            'message'=>'User registered',
            'token'=>$token
        ]);
    }

    public function login(Request $request)
    {
        $data=$request->validate([
            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);

        if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
            $user=Auth::user();

            $token=$user->createToken('StoreManager')->plainTextToken;

            return response()->json([
                'message'=>'User login',
                'token'=>$token
            ]);
        }

        return response()->json(['message'=>'invalid'],401);
    }
}
