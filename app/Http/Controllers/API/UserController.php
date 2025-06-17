<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $response = [
            "success" => false,
            "isAllowed" => true,
            "message" => "",
        ];

        try {
            $rules = [
                'name'     => 'required|string|min:4',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $response["message"] = $validator->errors()->first();
                return response()->json($response, 422);
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $formatUser = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];

            $response['success'] = true;
            $response['isAllowed'] = true;
            $response['message'] = 'Successfully registered';
            $response['user'] = $formatUser;

            return response()->json($response, 201);
        } catch (Exception $e) {
            $response['success'] = false;
            $response['isAllowed'] = false;
            $response['message'] = 'Failed to register user';

            return response()->json($response, 500);
        }
    }

    public function login(Request $request)
    {
        $response = [
            "success" => false,
            "isAllowed" => true,
            "message" => "",
        ];

        try {
            $rules = [
                'email'    => 'required|email',
                'password' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $response["message"] = $validator->errors()->first();
                return response()->json($response, 422);
            }

            if (!Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])) {
                $response['message'] = 'Invalid credentials';
                $response['isAllowed'] = false;
                return response()->json($response, 401);
            }

            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            $formatUser = [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ];

            $response['success'] = true;
            $response['isAllowed'] = true;
            $response['message'] = 'Login successful';
            $response['token'] = $token;
            $response['user'] = $formatUser;

            return response()->json($response, 200);
        } catch (Exception $e) {
            $response['success'] = false;
            $response['isAllowed'] = false;
            $response['message'] = 'Failed to log in user';

            return response()->json($response, 500);
        }
    }

    public function logout(Request $request)
    {
        $response = [
            "success" => false,
            "isAllowed" => true,
            "message" => "",
        ];

        try {
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();

                $response['success'] = true;
                $response['isAllowed'] = true;
                $response['message'] = 'Successfully logged out';

                return response()->json($response, 200);
            }

            $response['success'] = true;
            $response['isAllowed'] = false;
            $response['message'] = 'User not authenticated';

            return response()->json($response, 401);
        } catch (Exception $e) {

            $response['success'] = false;
            $response['isAllowed'] = false;
            $response['message'] = 'Failed to log out user';

            return response()->json($response, 500);
        }
    }
}
