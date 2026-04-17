<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Models\Customer;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{
    /**
     * Send a test welcome email
     */
    public function sendWelcome(Request $request)
    {
        try {
            $email = $request->input('email', 'test@example.com');
            $name = $request->input('name', 'Test Customer');
            $userType = $request->input('user_type', 'customer'); // 'customer' or 'admin'
            
            if ($userType === 'admin') {
                // Create or get admin
                $user = Admin::firstOrCreate(
                    ['email' => $email],
                    ['name' => $name, 'password' => bcrypt('password123')]
                );
            } else {
                // Create or get customer
                $user = Customer::firstOrCreate(
                    ['email' => $email],
                    ['name' => $name, 'password' => bcrypt('password123')]
                );
            }
            
            // Send email
            Mail::to($user->email)->send(new WelcomeEmail($user));
            
            return response()->json([
                'success' => true,
                'message' => 'Welcome email sent successfully',
                'email' => $user->email,
                'user_type' => $userType,
                'timestamp' => now()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mail configuration status
     */
    public function getMailStatus()
    {
        return response()->json([
            'mail_mailer' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_from' => config('mail.from'),
            'environment' => app()->environment()
        ], 200);
    }
}

