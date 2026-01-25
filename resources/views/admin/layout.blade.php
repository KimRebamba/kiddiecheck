<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin</title>
</head>
<body>
  <header>
    <nav>
      <a href="{{ route('admin.index') }}">Dashboard</a> |
      <a href="{{ route('admin.families') }}">Families</a> |
      <a href="{{ route('admin.profile') }}">Profile</a> |
      <a href="{{ route('admin.reports') }}">Reports</a> |
      <a href="{{ route('admin.sections') }}">Sections</a> |
      <a href="{{ route('admin.teachers') }}">Teachers</a> |
      <a href="{{ route('admin.users') }}">Users</a> |
      <a href="{{ route('admin.help') }}">Help</a>
    </nav>
    <hr>
  </header>
  <main>
    @yield('content')
  </main>
</body>
</html>
