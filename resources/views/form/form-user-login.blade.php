<form method="post" action="{{url("login_action")}}">
    {{csrf_field()}}
    <input type="hidden" name="previous_url" value="{{ url()->previous() }}">
    <p>
        <label><h4>Login/Register</h4></label>
        <input type="text" name="name" value="{{ old('name', session('name', '')) }}">
    </p>
        <input type="submit" value="Login">
</form>