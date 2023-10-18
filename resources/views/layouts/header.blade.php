<!-- Created using boostrap nav class-->
<nav class="navbar navbar-expand-sm navbar-dark navbar-custom fixed-top">
    <div class="container-fluid">
    <a class="navbar-brand" href="{{asset('/')}}"><img src="{{URL('/images/icon.png')}}" alt ="icon" width="100" height="55"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="{{ url('/') }}">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('users') }}">Users</a>
        </li>
        </ul>
        <ul class="navbar-nav">
        <li class="nav-item">
          @if(!session('name'))
            <a class="nav-link" href="{{ url('login') }}">Login</a>
          @else
            <a class="nav-link" href="{{ url('clear-session') }}">Log Out</a>
          @endif
        </li>
      </ul>
    </div>
  </div>
</nav>