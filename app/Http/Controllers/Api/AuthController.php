<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService){
        $this->authService = $authService;
    }

    // Register
    public function register(Request $request): Response{

        // validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|max:255'
        ]);

        // create  user
       $user = $this->authService->register($request);

        // create access token
        $token = $user->createToken('auth')->plainTextToken;

        // return
        return response([
            'message' => __('app.registration_success_verify'),
            'results' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ],201);
    }

    // Login
    public function login (Request $request): Response{

        // validate
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255'
        ]);

        // login user
        $user = $this->authService->login($request);

        if (!$user){
            return response([
                'message' => __('auth.failed')
            ], 401);
        }

        // create access token
        $token = $user->createToken('auth')->plainTextToken;

        // return
        return response([
            'message' => __('app.login_success' . ($user->email_verified_at)? '' : '_verify') ,
            'results' => [
                'user' => new UserResource($user),
                'token' => $token,
            ]
        ], 200);

    }

    // OTP
    public function otp(Request $request): Response{

        // get user
        $user = auth()->user();

        // generate otp
        $otp = $this->authService->otp($user);

        // send the otp

        return response([
            'message' => __('app.otp_sent_success')
        ]);
    }

    // User verify
    public function verify(Request $request): Response{

        // validate the request
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        // get user
        $user = auth()->user();

        // Verify otp
        $otp = $this->authService->verify($user, $request);

        return response([
            'message' => __('app.verification_success'),
            'results' => ([
                'user' => new UserResource($user)
            ])
        ]);
    }

    // Reset Otp
    public function resetOtp(Request $request): Response{
        // validate
        $request->validate([
            'email' => 'required|email|max:255|exists:users,email'
        ]);

        // get user
        $user = $this->authService->getUserByEmail($request->email);

        // Generate OTP
        $otp = $this->authService->otp($user, 'password-reset');

        //return
        return response([
            'message' => __('app.otp_sent_success'),
        ]);

    }

    // Reset Password
    public function resetPassword(Request $request){

        // Validate request
        $request->validate([
            'email' => 'required|email|max:255|exists:users,email',
            'otp' => 'required|numeric',
            'password' => 'required|min:6|max:255|confirmed',
            'password_confirmation' => 'required|min:6|max:255'
        ]);

        // Get User
        $user = $this->authService->getUserByEmail($request->email);

        // Reset Password
        $user = $this->authService->resetPassword($user, $request);

        // return
        return response([
            'message' => __('app.password_reset_success'),
        ]);



    }

}
