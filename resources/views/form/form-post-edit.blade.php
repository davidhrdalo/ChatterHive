<form method="post" action="{{url("post-edit-action")}}">
    {{csrf_field()}}
    <input type="hidden" name="post_id" value="{{ $post->post_id }}">
    <p>
        <label><h4>Post Title</h4></label>
        <input type="text" name="title" value="{{$post->title}}"></input>
    </p>
    <p>
        <label><h4>Message</h4></label>
        <textarea type="text" name="message"rows="8" cols="30">{{$post->message}}</textarea>

    </p>
        <input type="submit" value="Edit">
</form>