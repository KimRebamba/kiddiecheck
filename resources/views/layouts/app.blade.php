<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>App</title>
</head>
<body>
  <header>
    <nav>
      <a href="{{ route('index') }}">Home</a> |
      <a href="{{ route('family.index') }}">Family</a> |
      <a href="{{ route('admin.index') }}">Admin</a>
    </nav>
    <hr>
  </header>
  <main>
    @yield('content')
  </main>
</body>
</html>
