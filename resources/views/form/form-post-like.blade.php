<form method="post" action="{{url("post-like-action")}}">
    {{csrf_field()}}
        <input type="hidden" name="post_id" value="{{ $post->post_id }}">
        <input type="submit" value="Like Post">
</form>