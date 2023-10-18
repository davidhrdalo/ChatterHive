@extends('layouts.master')

@section('title')
  Edit Post
@endsection

@section('content')
  <div class="container">
    <div class="row" id="main">
      <div class="col-sm-3"></div>
      <div class="col-sm-6">
        <div class="row align-items-center" id="heading" style="text-align:center;">
          <div class="col-sm-12"><h3>Edit Post</h3>
          </div>
        </div>
        <div class="row" id="content">
          @if(session('edit_message'))
            <div class="alert alert-info">{{ session('edit_message') }}</div>
          @endif
          <div class="col-sm-3"></div>
          <div class="col-sm-9">@include('form.form-post-edit')</div>
        </div>
      </div>
    </div>
  </div>
@endsection