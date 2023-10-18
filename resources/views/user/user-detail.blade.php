@extends('layouts.master')
  @section('title')
    Posts by {{$name}}
  @endsection

@section('content')
  <div class="container">
    <div class="row" id="main">
      <div class="col-sm-3"></div>
      <div class="col-sm-6">
        <div class="row align-items-center" id="heading" style="text-align:center;">
          <div class="col-sm-12"><h3>Posts by {{$name}}</h3></div>
        </div>
        @forelse ($user_posts as $user_post)
          <div class="row" id="content">
            <div class="col-sm-12"><a href="{{url("post-detail/$user_post->post_id")}}"><h4>{{$user_post->title}}</h4></a></div>
          </div>
        @empty
          <p>No users found.</p>
        @endforelse
      </div>
    </div>
  </div>
@endsection