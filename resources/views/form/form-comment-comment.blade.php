<form method="post" action="{{url("comment-comment-action")}}">
    {{csrf_field()}}

    <input type="hidden" name="comment_id" value="{{ $comment->comment_id }}">
        <label><h6>Reply to Comment</h6></label><br>
        <input type="text" name="message" style="width: 150px;"></input>
        <input type="submit" value="Add">
        
</form>