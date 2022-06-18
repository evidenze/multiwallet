<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Registration controller
     *
     * @param  mixed $request
     * @return Response
     */
    public function register(RegisterRequest $request)
    {
        //Encrypt the password
        $password = bcrypt($request->password);

        //Store new user to database
        $user               = new User;
        $user->firstname    = $request->firstname;
        $user->lastname     = $request->lastname;
        $user->email        = $request->email;
        $user->phone_number = $request->phone_number;
        $user->password     = $password;
        $user->save();

        //Generate API token for the user
        $token = $user->createToken('MyApp')->accessToken;
        Auth::login($user);


        $data = [
            'status'  => (bool) $user,
            'message' => 'User registered successfully!',
            'token'   => $token,
            'user'    => $user,
            'data'    => $user,
        ];

        return response()->json($data, 201);
    }
}
