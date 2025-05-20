<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <h2>Password Reset Request</h2>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    
    <p>Click this link to reset your password:</p>
    <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
    
    <p>This password reset link will expire in 60 minutes.</p>
    
    <p>If you did not request a password reset, no further action is required.</p>
    
    <p>Thank you,<br>
    {{ config('app.name') }}</p>
</body>
</html>