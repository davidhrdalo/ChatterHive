@extends('layouts.master')

@section('title')
  Users
@endsection

@section('content')
<div class="container">
  <div class="row" id="main">
      <div class="col-sm-3">
      </div>
      <div class="col-sm-6">
          <div class="row align-items-center" id="heading" style="text-align:center;">
              <div class="col-sm-12"><h3>Users With Posts</h3></div>
          </div>
          @forelse ($users as $user)
          <div class="row" id="content">
              <div class="col-sm-12"><a href="{{url("user-details/$user->user_id")}}"><h4>{{$user->name}}</h4></a></div>
              </div>
            @empty
                <p>No users found.</p>
            @endforelse
      </div>
  </div>
</div>
@endsection