<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{    
    /**
     * Login controller
     *
     * @param  mixed $request
     * @return Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password
        ];

        //Attempt login
        if (Auth::attempt($credentials)) {

            //Generate API token after successful login
            $token = User::find(Auth::id())->createToken('MyApp')->accessToken;

            return response()->json([
                'message' => 'Login successful',
                'status'  => true,
                'token'   => $token,
                'user'    => Auth::user()
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid email or password',
            'status'  => false
        ], 400);
    }
}
