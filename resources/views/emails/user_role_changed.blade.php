<!DOCTYPE html>
<html>
<head>
    <title>Role Updated</title>
</head>
<body>
    <p>Dear {{ $user->name }},</p>
    <p>Your role has been updated to {{ $user->role == 0 ? 'Admin' : ($user->role == 1 ? 'Patient' : 'Doctor') }}.</p>
    <p>Thank you!</p>
</body>
</html>
