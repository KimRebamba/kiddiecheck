<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Family · KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }
    
    body {
      margin: 0;
      background: #f5f5f5;
      color: #333;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .family-header { background: white; border-bottom: 2px solid #e5e5e5; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

    .header-container { max-width: 1400px; margin: 0 auto; display: flex; align-items: center; padding: 0 20px; height: 70px; }

    /* Brand */
    .brand { font-size: 1.5rem; font-weight: 800; text-decoration: none; margin-right: 3rem; letter-spacing: -0.02em; }

    /* Alternating colors per letter */
    .brand span:nth-child(4n+1) { color: #3B82F6; }
    .brand span:nth-child(4n+2) { color: #F59E0B; }
    .brand span:nth-child(4n+3) { color: #EF4444; }
    .brand span:nth-child(4n+0) { color: #10B981; }

    .nav-links { display: flex; align-items: center; gap: 0.5rem; flex: 1; }

    .nav-link { text-decoration: none; color: #6b7280; font-weight: 600; font-size: 0.95rem; padding: 0.6rem 1.2rem; border-radius: 8px; transition: all 0.2s; }
    .nav-link:hover { color: #667eea; background: #f3f4f6; }
    .nav-link.active { color: #667eea; background: transparent; font-weight: 700; }

    .profile-section { display: flex; align-items: center; gap: 1rem; margin-left: auto; }
    .profile-menu { position: relative; }

    .profile-button {
      background: transparent; border: 2px solid #e5e5e5; color: #6b7280;
      font-weight: 600; font-size: 0.9rem; padding: 0.5rem 1rem; border-radius: 8px;
      cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; text-decoration: none;
    }
    .profile-button:hover { border-color: #667eea; color: #667eea; background: #f9fafb; }

    .dropdown-menu {
      position: absolute; right: 0; top: calc(100% + 8px);
      background: white; border: 2px solid #e5e5e5; border-radius: 12px;
      padding: 0.5rem; min-width: 180px; opacity: 0; visibility: hidden;
      transform: translateY(-10px); transition: all 0.2s;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 1000;
    }
    .profile-menu:hover .dropdown-menu,
    .profile-menu:focus-within .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }

    .dropdown-item {
      display: block; width: 100%; text-align: left; background: transparent; border: none;
      color: #6b7280; font-weight: 600; font-size: 0.9rem; padding: 0.7rem 1rem;
      border-radius: 8px; cursor: pointer; text-decoration: none; transition: all 0.2s;
    }
    .dropdown-item:hover { background: #f3f4f6; color: #667eea; }

    .main { max-width: 1400px; margin: 0 auto; padding: 2rem 20px; }

    .alert { background: white; border: 2px solid #e5e5e5; border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; font-weight: 500; }
    .alert-success { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .alert-error   { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }

    @media (max-width: 768px) {
      .header-container { padding: 15px; height: auto; flex-wrap: wrap; }
      .brand { margin-right: auto; font-size: 1.2rem; }
      .nav-links { width: 100%; justify-content: flex-start; margin-top: 10px; gap: 0.3rem; overflow-x: auto; }
      .nav-link { font-size: 0.85rem; padding: 0.5rem 0.9rem; white-space: nowrap; }
      .profile-section { margin-left: auto; }
      .profile-button { font-size: 0.85rem; padding: 0.4rem 0.8rem; }
    }
  </style>
</head>
<body>

@php
  $navFamily  = DB::table('families')->where('user_id', Auth::id())->first();
  $navStudent = null;

  if ($navFamily) {
    $students = DB::table('students')
      ->where('family_id', $navFamily->user_id)
      ->orderBy('date_of_birth', 'desc')
      ->get();

    $now = \Carbon\Carbon::now();

    // Prefer a child who currently has an active assessment window
    foreach ($students as $s) {
      $hasActivePeriod = DB::table('assessment_periods')
        ->where('student_id', $s->student_id)
        ->where('status', '!=', 'completed')
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->exists();

      if ($hasActivePeriod) {
        $navStudent = $s;
        break;
      }
    }

    // Fallback: first child if none currently has an active assessment
    if (!$navStudent) {
      $navStudent = $students->first();
    }
  }
@endphp

  <header class="family-header">
    <div class="header-container">

      <a class="brand" href="{{ route('family.index') }}">
        @foreach(str_split('KiddieCheck') as $l)<span>{{ $l }}</span>@endforeach
      </a>

      <div class="nav-links">
        <a class="nav-link {{ request()->routeIs('family.index') ? 'active' : '' }}" href="{{ route('family.index') }}">Home</a>
        <a class="nav-link {{ request()->routeIs('family.index') ? 'active' : '' }}" href="{{ route('family.index') }}#family-children">Children</a>
        <a class="nav-link {{ request()->routeIs('family.tests.*') ? 'active' : '' }}"
           href="{{ $navStudent ? route('family.tests.start.show', $navStudent->student_id) : route('family.index') }}">Current Test</a>
        <a class="nav-link {{ request()->routeIs('family.index') ? 'active' : '' }}" href="{{ route('family.index') }}#family-help">Help</a>
      </div>

      <div class="profile-section">
        <div class="profile-menu">
          <a href="{{ $navStudent ? route('family.student.profile', $navStudent->student_id) : route('family.index') }}" class="profile-button" tabindex="0">
            <span>{{ optional(Auth::user())->username ?? 'Account' }}</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </a>
          <div class="dropdown-menu">
            <a href="{{ $navStudent ? route('family.student.profile', $navStudent->student_id) : route('family.index') }}" 
   class="dropdown-item">Profile Settings</a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
              @csrf
              <button type="submit" class="dropdown-item">Logout</button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </header>

  <main class="main">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>