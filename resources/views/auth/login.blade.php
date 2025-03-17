<!DOCTYPE html>
<style>
    #random_secret {
        font-size: 10px;
        position: absolute;
        bottom: 0;
    }
</style>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <label for="email">Email:</label>
    <input type="email" name="email" autocomplete="email" required>
    <br/><br/>
    <label for="password">Password:</label>
    <input type="password" name="password" autocomplete="current-password" required>
    <br/><br/>
    <label for="mfa_code">MFA code:</label>
    <input type="text" name="mfa_code" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code">
    <br/><br/>
    <button type="submit">Login</button>
</form>
@if ($errors->any())
    <div>
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
<div id="random_secret">
    <pre>Random seed: {{ $random_secret }}</pre>
</div>
</body>
</html>
