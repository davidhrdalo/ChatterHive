@extends('layouts.master')

@section('title')
  Home
@endsection
@section('content')
<div class="container">
  <div class="row" id="main">
      <div class="col-sm-3">
        <div class="row" id="form">
          <h3><br>New Post<hr></h3>
          @if(session('post_message'))
            <div class="alert alert-info">{{ session('post_message') }}</div>
          @endif
          @if(session('universal_message'))
            <div class="alert alert-info">{{ session('universal_message') }}</div>
          @endif
          @include('form.form-post-create')
        </div>
      </div>
      <div class="col-sm-7">
          <div class="row align-items-center" id="heading" style="text-align:center;">
              <div class="col-sm-12"><h3>New Posts</h3></div>
          </div>
          @forelse ($posts as $post)
          <div class="row" id="content">
              <div class="col-sm-6"><a href="{{url("post-detail/$post->post_id")}}"><h3>{{$post->title}}</h3></a></div>
              <div class="col-sm-6"> <!--<h5>Posted on: {{$post->date}}</h5>--> </div> <!-- Uncomment to show post date -->
              <div class="col-sm-12">Posted by: <a href="{{url("user-details/$post->user_id")}}">{{$post->name}}<br></a><br></div>
              <hr>
              <div class="col-sm-8">Total comments: {{$post->all_comment_count}}</div>
              <div class="col-sm-4">Direct comments: {{$post->comment_count}}</div>
              <!--<div class="col-sm-4">Total likes: {{$post->like_count}}</div>--> <!-- Uncomment to show post likes -->
              <!-- <div class="col-sm-12">{{$post->message}}</div><hr> --> <!-- Uncomment to show post message -->
            </div>
            @empty
                <p>No items found.</p>
            @endforelse
      </div>
  </div>
</div>

@endsection
