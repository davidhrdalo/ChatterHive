<form method="post" action="{{url("post-create-action")}}">
    {{csrf_field()}}
    <p>
        <label><h4>User</h4></label>
        <input type="text" name="name" value="{{ old('name', session('name', '')) }}">
    </p>
    <p>
        <label><h4>Post Title</h4></label>
        <input type="text" name="title">
    </p>
    <p>
        <label><h4>Message</h4></label>
        <textarea type="text" name="message"rows="8"></textarea>

    </p>
        <input type="submit" value="Post">
</form>