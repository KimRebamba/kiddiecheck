<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin · KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
      <a class="navbar-brand" href="{{ route('admin.index') }}">Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.index') }}">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.families') }}">Families</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.sections') }}">Sections</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.teachers') }}">Teachers</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">Users</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.domains') }}">Domains</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports') }}">Reports</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.help') }}">Help</a></li>
        </ul>
        <form method="post" action="{{ route('logout') }}" class="d-flex">
          @csrf
          <button class="btn btn-outline-light btn-sm" type="submit">Logout</button>
        </form>
      </div>
    </div>
  </nav>
  <main class="container mb-5">
    @yield('content')
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
