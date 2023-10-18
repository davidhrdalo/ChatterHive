@extends('layouts.master')

@section('title')
  Login
@endsection

@section('content')
  <div class="container">
    <div class="row" id="main">
      <div class="col-sm-3"></div>
      <div class="col-sm-6">
        <div class="row align-items-center" id="heading" style="text-align:center;">
          <div class="col-sm-12">
            <p><h3>Welcome to ChatterHive</h3></p>
            <p><h5>Login or register below</h5></p>
          </div>
        </div>
        <div class="row" id="content">
          @if(session('login_message'))
            <div class="alert alert-info">{{ session('login_message') }}</div>
          @endif
          @if(session('universal_message'))
            <div class="alert alert-info">{{ session('universal_message') }}</div>
          @endif
          <div class="col-sm-3"></div>
          <div class="col-sm-9">@include('form.form-user-login')</div>
        </div>
      </div>
    </div>
  </div>
@endsection