<h1>Verify Google Authenticator</h1>
<form action="/user/google2fa/authenticate" method="POST">
    @csrf
    <input name="one_time_password" type="text">
    <button type="submit">{{ __('Authenticate') }}</button>
</form>
