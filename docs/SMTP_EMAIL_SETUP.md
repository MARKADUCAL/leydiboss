# SMTP Email Setup Guide

## Overview

This project uses **Mailtrap** for SMTP email testing and **Laravel's Mail** system for sending emails. Emails are queued and processed asynchronously for better performance.

---

## Configuration

### Environment Variables (.env)

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=fc0ecc91351984
MAIL_PASSWORD=2c9d34bc670a99
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@leydiboss.com
MAIL_FROM_NAME="leydiboss"
```

### Key Points:

- **MAIL_SCHEME**: Must be `smtp` or `smtps` (NOT `tls`)
- **MAIL_ENCRYPTION**: Use `tls` for port 2525
- **Queue Driver**: Set to `database` for background processing

---

## Email Classes

### WelcomeEmail

**Location:** `app/Mail/WelcomeEmail.php`

Sends a welcome email to new customers/admins when they register.

**Properties:**

- Takes a `$user` object (Customer or Admin)
- Subject: "Welcome to leydiboss"
- Template: `resources/views/emails/welcome.blade.php`
- Implements: `ShouldQueue` (queued processing)

**Usage:**

```php
Mail::to($user->email)->send(new WelcomeEmail($user));
```

---

## Email Templates

### welcome.blade.php

**Location:** `resources/views/emails/welcome.blade.php`

Professional welcome email with:

- Header with app name
- Personalized greeting
- Welcome message
- Footer with copyright

**Variables Available:**

- `$name` - User name
- `$email` - User email address

---

## Testing Endpoints

### Test Email Routes

All routes are in `routes/api.php` under "Email Testing Routes" section.

#### 1. Send Welcome Email

```
POST /api/test-email/welcome
```

**Request Body:**

```json
{
    "email": "user@example.com",
    "name": "John Doe",
    "user_type": "customer"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Welcome email sent successfully",
    "email": "user@example.com",
    "user_type": "customer",
    "timestamp": "2026-04-17T10:30:00.000000Z"
}
```

**Features:**

- Auto-creates customer or admin if doesn't exist
- Sends welcome email via queue
- Returns confirmation

#### 2. Check Mail Status

```
GET /api/test-email/status
```

**Response:**

```json
{
    "mail_mailer": "smtp",
    "mail_host": "sandbox.smtp.mailtrap.io",
    "mail_port": 2525,
    "mail_from": {
        "address": "noreply@leydiboss.com",
        "name": "leydiboss"
    },
    "environment": "local"
}
```

---

## Queue Configuration

### Database Queue Setup

1. Emails are stored in the `jobs` table
2. `php artisan queue:work` processes them asynchronously
3. Can retry failed jobs with `php artisan queue:retry all`

### Processing Emails

```bash
# Start queue worker (processes 1 job at a time)
php artisan queue:work

# Process and stop when queue is empty
php artisan queue:work --stop-when-empty

# Process queue with specific timeout
php artisan queue:work --timeout=30

# Flush all pending jobs
php artisan queue:flush

# Retry failed jobs
php artisan queue:retry all
```

### Queue Table Setup

```bash
php artisan queue:table
php artisan migrate
```

---

## Testing Workflow

### Step 1: Setup

```bash
# Configure Mailtrap credentials in .env
# Run migrations if needed
php artisan migrate
```

### Step 2: Start Queue Worker

```bash
# Terminal 1
php artisan queue:work --stop-when-empty
```

### Step 3: Send Test Email (Postman)

```
POST http://localhost:8000/api/test-email/welcome

{
    "email": "test@gmail.com",
    "name": "Test User",
    "user_type": "customer"
}
```

### Step 4: View in Mailtrap

1. Go to https://mailtrap.io
2. Navigate to "My Sandbox"
3. Check inbox for received email
4. Click email to view rendered HTML

---

## Production Deployment

### Important: Remove Test Routes

Before deploying to production, remove or comment out these lines in `routes/api.php`:

```php
// ─── Email Testing Routes (Remove in Production) ───────────────────────
Route::post('/test-email/welcome', [TestEmailController::class, 'sendWelcome']);
Route::get('/test-email/status', [TestEmailController::class, 'getMailStatus']);
```

### Update Environment

```env
# Production SMTP credentials
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App Name"

# Production queue
QUEUE_CONNECTION=redis  # or use 'database'
```

### Use Production Email Services

- **SendGrid** - Reliable, scalable
- **AWS SES** - Affordable, high volume
- **Mailgun** - Developer-friendly
- **Postmark** - Focus on transactional emails

---

## Troubleshooting

### Email Not Sending

1. Check queue worker is running: `php artisan queue:work`
2. Verify SMTP credentials in `.env`
3. Check `MAIL_SCHEME=smtp` (not `tls`)
4. Review logs: `storage/logs/laravel.log`

### Queue Jobs Failing

```bash
# Check failed jobs
php artisan queue:failed

# Retry specific job
php artisan queue:retry {id}

# Retry all failed jobs
php artisan queue:retry all
```

### SMTP Connection Errors

- Verify host, port, username, password
- Ensure firewall allows port 2525
- Test with `php artisan tinker`

---

## Integration Examples

### Send Welcome Email on Registration

```php
// In CustomerAuthController
public function register(Request $request)
{
    $customer = Customer::create([...]);

    // Send welcome email
    Mail::to($customer->email)->send(new WelcomeEmail($customer));

    return response()->json(['success' => true]);
}
```

---

## Best Practices

✅ **Do:**

- Keep SMTP credentials in `.env` (never commit to git)
- Use queued emails for better performance
- Test emails in Mailtrap before production
- Use environment-specific SMTP configs
- Implement rate limiting for email sending

❌ **Don't:**

- Hardcode email credentials
- Send emails synchronously for high volume
- Forget to process queue worker
- Commit `.env` files to version control
- Use test credentials in production

---

## Resources

- [Laravel Mail Documentation](https://laravel.com/docs/mail)
- [Laravel Queues Documentation](https://laravel.com/docs/queues)
- [Mailtrap Documentation](https://mailtrap.io/blog/)
- [Laravel Mailable Classes](https://laravel.com/docs/mail#generating-mailables)

---

## Maintenance

### Regular Tasks

- Monitor email delivery rates
- Archive old emails in Mailtrap
- Review failed queue jobs weekly
- Update SMTP credentials when they expire
- Test email functionality monthly

### Health Checks

```bash
# Check mail configuration
php artisan config:show mail

# Test SMTP connection
php artisan mail:test your-email@example.com

# Verify queue is working
php artisan queue:work --once
```
