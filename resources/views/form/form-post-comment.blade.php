<form method="post" action="{{url("post-comment-action")}}">
    {{csrf_field()}}
        <input type="hidden" name="post_id" value="{{ $post->post_id }}">
    <p>
        <label><h4>User</h4></label>
        <input type="text" name="name" value="{{ old('name', session('name', '')) }}">
    </p>
    <p>
        <label><h4>Message</h4></label>
        <textarea type="text" name="message"></textarea>
    </p>
    <input type="submit" value="Comment">
</form>