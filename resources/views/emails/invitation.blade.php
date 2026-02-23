<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Invitation to join :company', ['company' => $invitation->company->name]) }}</title>
</head>
<body>
    <p>{{ __('Hello,') }}</p>
    <p>{{ __('You have been invited to join :company as :role.', ['company' => $invitation->company->name, 'role' => ucfirst($invitation->role)]) }}</p>
    <p><a href="{{ route('invitations.accept', ['token' => $invitation->token]) }}">{{ __('Accept invitation') }}</a></p>
    <p>{{ __('This link expires in 7 days.') }}</p>
</body>
</html>
