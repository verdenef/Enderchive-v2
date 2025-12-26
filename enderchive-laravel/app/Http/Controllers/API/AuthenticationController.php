<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthenticationController extends Controller
{
    /**
     * Register a new account.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'                  => 'required|string|min:4|max:255',
                'username'              => 'required|string|max:50|alpha_dash|unique:users,username|different:email',
                'email'                 => 'required|string|email|max:255|unique:users',
                'password'              => 'required|string|min:8',
                'password_confirmation' => 'required|string|same:password',
            ], [
                'name.required'                  => 'Name is required',
                'name.min'                       => 'Name must be at least 4 characters',
                'name.max'                       => 'Name must not exceed 255 characters',
                'username.required'               => 'Username is required',
                'username.max'                    => 'Username must not exceed 50 characters',
                'username.alpha_dash'             => 'Username can only contain letters, numbers, dashes and underscores',
                'username.unique'                 => 'This username is already taken',
                'username.different'               => 'Username must be different from email',
                'email.required'                 => 'Email is required',
                'email.email'                    => 'Please provide a valid email address',
                'email.unique'                   => 'This email is already registered',
                'password.required'              => 'Password is required',
                'password.min'                  => 'Password must be at least 8 characters',
                'password_confirmation.required' => 'Password confirmation is required',
                'password_confirmation.same'     => 'Password confirmation does not match',
            ]);

            $user = User::create([
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'response_code' => 201,
                'status'        => 'success',
                'message'       => 'Successfully registered',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validation failed',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Registration failed',
            ], 500);
        }
    }

    /**
     * Login and return auth token.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|string',
                'password' => 'required|string',
            ]);

            $login = $credentials['email'];
            
            // Login supports email or username
            $user = filter_var($login, FILTER_VALIDATE_EMAIL)
                ? User::where('email', $login)->first()
                : User::where('username', $login)->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'Unauthorized',
                ], 401);
            }

            // Login the user for the session
            Auth::login($user);
            
            /** @var \Laravel\Sanctum\HasApiTokens $user */
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Login successful',
                'user_info'     => [
                    'id'       => $user->user_id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'email'    => $user->email,
                ],
                'token'       => $token,
                'token_type'  => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validation failed',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Login failed',
            ], 500);
        }
    }

    /**
     * Get authenticated user information — protected route.
     */
    public function userInfo()
    {
        try {
            $user = Auth::user();

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'User information retrieved successfully',
                'user_info'     => [
                    'id'       => $user->user_id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'email'    => $user->email,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('User Info Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Failed to fetch user information',
            ], 500);
        }
    }

    /**
     * Get list of all users (admin only) — protected route.
     */
    public function getAllUsers()
    {
        try {
            $user = Auth::user();
            
            // Simple admin check - check by email instead of user_id
            if ($user->email !== 'admin@example.com') { // Check by admin email
                return response()->json([
                    'response_code' => 403,
                    'status'        => 'error',
                    'message'       => 'Admin access required',
                ], 403);
            }
            
            $users = User::latest()->paginate(10);

            return response()->json([
                'response_code'  => 200,
                'status'         => 'success',
                'message'        => 'User list retrieved successfully',
                'data_user_list' => $users,
            ]);
        } catch (\Exception $e) {
            Log::error('Get All Users Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Failed to fetch user list',
            ], 500);
        }
    }

    /**
     * Logout user and revoke tokens — protected route.
     */
    public function logOut(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                // Revoke only the current token (not all tokens)
                $request->user()->currentAccessToken()->delete();

                return response()->json([
                    'response_code' => 200,
                    'status'        => 'success',
                    'message'       => 'Successfully logged out',
                ]);
            }

            return response()->json([
                'response_code' => 401,
                'status'        => 'error',
                'message'       => 'User not authenticated',
            ], 401);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'An error occurred during logout',
            ], 500);
        }
    }
}