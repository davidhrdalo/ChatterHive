@extends('layouts.master')

@section('title')
  {{$post->title}}
@endsection

@section('content')
<div class="container">
  <div class="row" id="main">
    <div class="col-sm-3">
      <div class="row" id="form">        
          @if(session('detail_message'))
            <div class="alert alert-info">{{ session('detail_message') }}</div>
          @endif
          @if(session('universal_message'))
            <div class="alert alert-info">{{ session('universal_message') }}</div>
          @endif
        <h3><br>Leave a comment<hr></h3>
        @include('form.form-post-comment')
      </div>
    </div>
    <div class="col-sm-7">
      <div class="row" id="content">
        <div class="col-sm-12" style="text-align:center;"><h3>{{$post->title}}</h3><br></div><hr>
        <div class="col-sm-6"><h5>Posted by <a href="{{url("user-details/$post->user_id")}}"> {{$post->name}}</h5></a></div>
        <div class="col-sm-6" style="text-align:center;"><h5>Posted on: {{$post->date}}</h5><br></div><hr>
        <div class="col-sm-12"><p>{{$post->message}}</p><br></div><hr>
        <div class="col-sm-8"><a href="{{url("post-edit/$post->post_id")}}"><p>Edit Post</p></a></div>
        <div class="col-sm-4">@include('form.form-post-delete')</div><hr>
        <div class="col-sm-8">
          <!-- Logged out users are only shown like button -->
          @if(!session('name'))
            @include('form.form-post-like')
          @else
          <!-- Checking the user_has_like function to check whether to display the like or unlike button -->
          @if($user_has_liked)
            @include('form.form-post-unlike')
            @else
              @include('form.form-post-like')
            @endif
          @endif
        </div>    
          <div class="col-sm-4"><p> Post Likes: {{$likes}} </p></div>
        <hr>
        <div class="col-sm-12"><h4>Comments:</h4><br></div>
        @foreach ($comments as $comment)
          <div class="col-sm-1"></div>
          <div class="col-sm-11"><hr></div>
          <div class="col-sm-1"></div>
          <div class="col-sm-6">Comment by <a href="{{url("user-details/$comment->user_id")}}"> {{$comment->name}} </a></div>
          <div class="col-sm-5"><p>{{$comment->date}}</p></div>
          <div class="col-sm-1"></div>
          <div class="col-sm-11"><p>{{$comment->message}}</p></div>
          <div class="col-sm-1"></div>
          <div class="col-sm-11">@include('form.form-comment-comment')<hr></div>
          <!-- Checks if there is an entry in the comments_comments array -->
          <!-- Prevents an error if the value is null -->
          @if(isset($comment_comments[$comment->comment_id]))
            <!-- Looping through comment_comments using comment_id as the search key -->
            @foreach ($comment_comments[$comment->comment_id] as $cc)
              <div class="col-sm-3"></div>
              <div class="col-sm-3">Comment by <a href="{{url("user-details/$cc->user_id")}}"> {{$cc->name}} </a></div>
              <div class="col-sm-6"><p>{{$cc->date}}</p></div>
              <div class="col-sm-3"></div>
              <div class="col-sm-9"><p>{{$cc->c_message}}</p><hr></div>
            @endforeach
          @endif
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection