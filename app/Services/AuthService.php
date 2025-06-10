<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Mail;
use Str;

class AuthService {

    // Register
    public function register(object $request): User{
            $user = User::create ([
            'uuid'=> Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        // Send the verification code
        $this->otp($user);
        return  $user;
    }

    // Login
    public function login(object $request): ?User{
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)){
            return $user;
        }
       return null;
    }

    // OTP
    public function otp(User $user, string $type = 'verification'): Otp{

        // Check for spam and trouble
        $tries = 3;
        $time = Carbon::now()->subMinutes(30);

        $count = Otp::where([
            'user_id' => $user->id,
            'type' => $type,
            'active' => 1,
        ])->where('created_at', '>=', $time)->count();

        if ($count >= $tries){
            abort(422, 'To many OTP Requests');
        }

        $code = random_int(100000, 999999);

        $otp = Otp::create([
            'user_id' => $user->id,
            'type' => $type,
            'code' => $code,
            'active' => 1,
        ]);

        // Send Email
        Mail::to($user)->send(new OtpMail($user, $otp));
        return $otp;
    }

    // User verification
    public function verify(User $user, object $request): User{
        $otp = Otp::where([
            'user_id' => $user->id,
            'code' => $request->otp,
            'active' => 1,
            'type' => 'verification'
        ])->first();

        if (!$otp){
            abort(422, __('app.invalid_otp'));
        }

        // Update
        $user->email_verified_at = Carbon::now();
        $user->update();

        $otp->active = 0;
        $otp->updated_at = Carbon::now();
        $otp->update();

        return $user;
    }

    // Get  User by email
    public function getUserByEmail(string $email){
        return User::where('email', $email)->first();
    }

    // Reset Password
    public function resetPassword(User $user, object $request): User{

        // Validate otp
        $otp = Otp::where([
            'user_id' => $user->id,
            'code' => $request->otp,
            'active' => 1,
            'type' => 'password-reset',
        ])->first();

        if (!$otp){
            abort(422,__('app.invalid_otp'));
        }

        // Update
        $user->password = $request->password;
        $user->updated_at = Carbon::now();
        $user->update();

        $otp->active = 0;
        $otp->updated_at = Carbon::now();
        $otp->update();

        // return the user
        return $user;

    }
}

