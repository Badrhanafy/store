<!DOCTYPE html>
<html>
<head>
    <title>Test Email</title>
</head>
<body>
    <h1>Test Email</h1>
    @isset($resetUrl)
        <p>Reset URL: {{ $resetUrl }}</p>
    @endisset
    <p>This is a test email from your Laravel application.</p>
</body>
</html>