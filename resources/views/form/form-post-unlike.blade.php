<form method="post" action="{{url("post-unlike-action")}}">
    {{csrf_field()}}
    <input type="hidden" name="post_id" value="{{ $post->post_id }}">
    <input type="submit" value="Remove Like">
</form>