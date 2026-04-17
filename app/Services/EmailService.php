<?php

namespace App\Services;

use App\Mail\PasswordResetEmail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send a welcome email to a new user.
     *
     * @param User $user
     * @return bool
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            Mail::send(new WelcomeEmail($user));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send a password reset email.
     *
     * @param string $email
     * @param string $resetUrl
     * @param string $userName
     * @return bool
     */
    public function sendPasswordResetEmail(string $email, string $resetUrl, string $userName): bool
    {
        try {
            Mail::send(new PasswordResetEmail($email, $resetUrl, $userName));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send a generic email.
     *
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $data
     * @return bool
     */
    public function sendGenericEmail(string $to, string $subject, string $view, array $data = []): bool
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send email', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
