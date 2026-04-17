<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }

        .content {
            background-color: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .password-box {
            background-color: #e8f4f8;
            border: 1px solid #0ca5d8;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .password-box h3 {
            margin-top: 0;
            color: #0ca5d8;
        }

        .password-text {
            font-size: 18px;
            font-weight: bold;
            font-family: monospace;
            letter-spacing: 1px;
            color: #333;
        }

        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 12px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>

        <div class="content">
            <h2>Hello {{ $name }},</h2>

            <p>We received a request to reset the password for your account associated with
                <strong>{{ $email }}</strong>.
            </p>

            <p>Click the button below to reset your password:</p>

            <a href="{{ $resetUrl }}" class="button">Reset Password</a>

            <p>Or copy and paste this link in your browser:</p>
            <p style="word-break: break-all;">{{ $resetUrl }}</p>

            @if ($newPassword)
                <div class="password-box">
                    <h3>Your Temporary Password:</h3>
                    <p class="password-text">{{ $newPassword }}</p>
                    <p style="font-size: 12px; color: #666; margin: 10px 0 0 0;">Keep this password safe. You can change
                        it in your account settings after logging in.</p>
                </div>
            @endif

            <div class="warning">
                <strong>⚠️ Important:</strong> This link will expire in {{ $expiresIn }} minutes. If you didn't
                request a password reset, please ignore this email or contact our support team.
            </div>

            <p>If you have any questions, please contact our support team.</p>

            <p>Best regards,<br>
                The {{ config('app.name') }} Team</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
